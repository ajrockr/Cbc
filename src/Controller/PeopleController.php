<?php

namespace App\Controller;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PeopleController extends AbstractController
{
    #[Route('/people', name: 'app_people')]
    public function index(Request $request, PersonRepository $person): Response
    {
        // Get the people
        $people = $person->findAll();
        $data = [];
        foreach ($people as $p) {
            $data[] = [
                'first_name'        => $p->getFirstName(),
                'last_name'         => $p->getLastName(),
                'middle_name'       => $p->getMiddleName(),
                'graduation_year'   => $p->getGraduationYear(),
                'id'                => $p->getId()
            ];
        }

        $form = $this->createFormBuilder()
            ->add('inputFirstName', TextType::class, [
                'label' => 'First Name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('inputLastName', TextType::class, [
                'label' => 'Last Name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('inputMiddleName', TextType::class, [
                'label' => 'Middle Name',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('inputGraduationYear', NumberType::class, [
                'label' => 'Graduation Year',
                'attr' => [
                    'class' => 'form-control',
                    'pattern' => '[0-9]*',
                    'minlength' => 4,
                    'maxlength' => 4
                ]
            ])
            ->add('close', ButtonType::class, [
                'label' => 'Close',
                'attr' => [
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm()
        ;
        
        // Handle form submission
        $form->handleRequest($request);

        $insertErrors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $input = $form->getData();

            if (
                ! $person->addPerson((new Person), [
                    'first_name' => $input['inputFirstName'],
                    'last_name' => $input['inputLastName'],
                    'middle_name' => $input['inputMiddleName'] ?? null,
                    'graduation_year' => (int) $input['inputGraduationYear']
                ])
            ) {
                $insertErrors = [
                    'failed adding person into database.'
                ];
            }

            return $this->redirectToRoute('app_people');
        }

        return $this->render('people/index.html.twig', [
            'controller_name' => 'PeopleController',
            'people' => $data,
            'form' => $form->createView(),
            'errors' => $insertErrors
        ]);
    }

    #[Route('/people/add', name: 'app_people_add')]
    public function add()
    {

    }
}
