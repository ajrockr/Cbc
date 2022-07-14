<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Config;
use App\Repository\UserRepository;
use App\Repository\ConfigRepository;
use App\Repository\RepairRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProfileController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(Request $request, Security $security, RepairRepository $repairRepository, UserRepository $userRepository, ConfigRepository $configRepository): Response
    {
        $inputSuccess = '';
        // Create password change form
        $changePasswordForm = $this->createFormBuilder()
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Current Password',
                'attr' => [
                    'class' => 'form-control mb-2',
                    'required' => true
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'form-control mb-2 has-validation',
                        'pattern' => '(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}',
                        'id' => 'validationCustomPassword'
                    ]
                ],
                'help' => 'Password must be atleast 8 characters, containan uppercase and lowercase letter, and at least one number.',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
                
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Change',
                'attr' => [
                    'id' => 'changePassword',
                    'class' => 'form-control w-100 btn btn-primary mt-2'
                ]
            ])
            ->getForm()
        ;

        // Get available themes data
        if (null === $availableThemes = $configRepository->createQueryBuilder('c')
                    ->select('c.config_value')
                    ->andWhere('c.config_item = :available_themes')
                    ->setParameter('available_themes', 'available_themes')
                    ->getQuery()
                    ->getOneOrNullResult()) {
            $availableThemes = ['default'];
        }
        
        $availableThemes = explode(', ', $availableThemes['config_value']);
        $availableThemes = array_combine($availableThemes, $availableThemes);

        // Create change theme form
        $changeThemeForm = $this->createFormBuilder(options: ['allow_extra_fields' => true])
            ->add('theme', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => $availableThemes,
                'data' => $security->getUser()->getTheme()
            ])
            ->add('submit'.mt_rand(), ButtonType::class, [
                'label' => 'Change',
                'attr' => [
                    'id' => 'changeTheme',
                    'class' => 'form-control w-100 btn btn-primary mt-2'
                ]
            ])
            ->getForm()
        ;

        // Get repair information
        $repairs = $repairRepository->getUserRepairs($security->getUser()->getId());
        // @todo make a repair view page and change link in this view to it

        // Handle password change form
        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $input = $changePasswordForm->getData();

            if (false === $this->isPasswordValid($security->getUser(), $input['oldPassword'])) {
                $inputPasswordErrors = 'Current password is invalid, please try again.';
            } else {
                if (false === $this->changePassword($security->getUser(), $userRepository, $input['newPassword'])) {
                    $inputPasswordErrors = 'Failed changing password.';
                }

                $inputSuccess = 'Successfully changed password.';
            }

            // Return the render with errors
            return $this->render('user_profile/index.html.twig', [
                'inputPasswordErrors' => $inputPasswordErrors,
                'inputSuccess' => $inputSuccess,
                'userRepairs' => $repairs,
                'changePasswordForm' => $changePasswordForm->createView(),
                'changeThemeForm' => $changeThemeForm->createView()
            ]);
        }

        // Handle change theme form
        $changeThemeForm->handleRequest($request);
        if ($changeThemeForm->isSubmitted() && $changeThemeForm->isValid()) {
            $input = $changeThemeForm->getData();
            $userRepository->setTheme($security->getUser(), $input['theme']);

            // Redirect back to user profile to change theme
            return $this->redirect($request->getUri());
        }
        
        // Default render
        return $this->render('user_profile/index.html.twig', [
            'userRepairs' => $repairs,
            'changePasswordForm' => $changePasswordForm->createView(),
            'changeThemeForm' => $changeThemeForm->createView()
        ]);
    }

    private function isPasswordValid(User $user, string $plainPassword): bool {
        if ($this->passwordHasher->isPasswordValid($user, $plainPassword)) return true;

        return false;
    }

    private function changePassword(User $user, UserRepository $userRepository, string $plainPassword): bool {
        try {
            $userRepository->upgradePassword(
                $user,
                $this->passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );
        } catch(\Exception $e) {
            return false;
        }

        return true;
    }
}
