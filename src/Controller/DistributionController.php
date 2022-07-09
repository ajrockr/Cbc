<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Entity\CartSlot;
use App\Entity\Distribute;
use Monolog\DateTimeImmutable;
use App\Repository\SlotRepository;
use App\Repository\ConfigRepository;
use App\Repository\DistributeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DistributionController extends AbstractController
{
    #[Route('/distribute', name: 'app_distribution')]
    public function distribution(Request $request, DistributeRepository $distributeRepository, FormFactoryInterface $formFactory, ManagerRegistry $doctrine): Response
    {
        // Get assets that can be distributed
        $toDistribute = $distributeRepository->getAssetsNotDistributed();

        // Bypass twig errors be creating an empty array that will be overwritten if there is data pressent
        $forms = [];

        // Create a dynamic array of forms for reach asset that can be distributed to a person
        foreach ($toDistribute as $key => $data) {
            $forms[$key] = $formFactory->createNamed('form_' . $key)
                ->add('asset_tag_text', TextType::class, [
                    'label' => $data['asset_tag'],
                    'attr' => [
                        'value' => $data['asset_tag']
                    ]
                ])
                ->add('asset_tag', HiddenType::class, [
                    'data' => $data['asset_tag']
                ])
                ->add('person_text', TextType::class, [
                    'label' => $data['person'],
                    'attr' => [
                        'value' => $data['person']
                    ]
                ])
                ->add('person', HiddenType::class, [
                    'data' => $data['person']
                ])
                ->add('slot_number_text', TextType::class, [
                    'label' => $data['slot_number'],
                    'attr' => [
                        'value' => $data['slot_number']
                    ]
                ])
                ->add('slot_number', HiddenType::class, [
                    'data' => $data['slot_number']
                ])
                ->add('distribute', SubmitType::class, [
                    'label' => 'Distribute',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
            ;
        }

        // To handle the requests, we will loop through the forms in order to deterine which one was submitted
        foreach ($forms as $form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $input = $form->getData();

                $entityManger = $doctrine->getManager();
                $distributeEntity = $entityManger->getRepository(Distribute::class)
                                                ->findOneBy([
                                                    'assetTag' => $input['asset_tag'],
                                                ])
                ;

                if (null === $distributeEntity) {
                    $distributeEntity = new Distribute();
                    $distributeEntity->setAssetTag($input['asset_tag']);
                    $distributeEntity->setSlotNumber($input['slot_number']);
                    $distributeEntity->setDistributed(false);
                    $distributeEntity->setCreatedAt(new \DateTimeImmutable("now"));
                    $distributeEntity->setDistributedBy($this->getUser()->getUserIdentifier());
                    $entityManger->persist($distributeEntity);
                    $entityManger->flush();
                }

                $distributeEntity->setAssetTag($input['asset_tag']);
                $distributeEntity->setSlotNumber($input['slot_number']);
                $distributeEntity->setDistributed(false);
                $distributeEntity->setCreatedAt(new \DateTimeImmutable("now"));
                $distributeEntity->setDistributedBy($this->getUser()->getUserIdentifier());
                $entityManger->flush();

                return $this->redirectToRoute('app_distribution');
            }
        }

        return $this->render('distribution/distribute.html.twig', [
            'toDistribute' => $toDistribute,
            'forms' => array_map(function($form) {
                return $form->createView();
            }, $forms),
        ]);
    }

    #[Route('/roulette', name: 'app_roulette')]
    public function roulette(Request $request, DistributeRepository $distributeRepository): Response
    {
        $toDistribute = $distributeRepository->getAssetsForRoulette();
        $distributed = $distributeRepository->getAssetsDistributed();

        return $this->render('distribution/roulette.html.twig', [
            'roulette' => $toDistribute,
            'distributed' => $distributed,
        ]);
    }

    #[Route('/roulette/distribute/{id<\d+>}', name: 'app_distribute_distribute')]
    public function distribute(int $id, ManagerRegistry $doctrine, ConfigRepository $config): Response
    {
        $entityManger = $doctrine->getManager();
        if (null === $distributeEntity = $entityManger->getRepository(Distribute::class)->findOneBy(['id' => $id])) {
            return $this->redirectToRoute('app_roulette');
        }

        $distributeEntity->setDistributed('true');
        $distributeEntity->setDistributedAt(new \DateTimeImmutable("now"));
        $entityManger->flush();

        // If $CONFIG['UNASSIGN_SLOT_ON_DISTRIBUTION'] remove asset from slot
        if (true == $config->item('unassign_slot_on_distribution')) {
            $slotEntity = $doctrine->getRepository(Slot::class);
            $cartslotEntity = $doctrine->getRepository(CartSlot::class);
            $slotId = $cartslotEntity->findOneBy(['cart_slot_number' => $distributeEntity->getSlotNumber()])->getId();
            $slot = $slotEntity->findOneBy(['number' => $slotId]);
            $entityManger->remove($slot);
            $entityManger->flush();
        }

        return $this->redirectToRoute('app_roulette');
    }
}
