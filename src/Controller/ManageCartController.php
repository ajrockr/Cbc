<?php

namespace App\Controller;

use App\Repository\SlotRepository;
use App\Repository\CartSlotRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManageCartController extends AbstractController
{
    #[Route('/manage/cart', name: 'app_manage_cart')]
    public function index(Request $request, SlotRepository $slotRepository, CartSlotRepository $cartslotRepository): Response
    {
        $clearSlotForm = $this->createFormBuilder()
            ->add('clearslotconfirm', CheckboxType::class, [
                'label' => 'Confirm: ',
                'attr' => [
                    'id' => 'clearslotconfirm'
                ]
            ])
            ->add('clearslotlink', SubmitType::class, [
                'label' => 'Clear All Slot Information',
                'attr' => [
                    'class' => 'btn btn-danger disabled',
                    'id' => 'clearslotlink'
                ]
            ])
            ->getForm()
        ;

        $success = '';
        $error = '';
        $clearSlotForm->handleRequest($request);
        if ($clearSlotForm->isSubmitted() && $clearSlotForm->isValid()) {
            if ($this->clearSLot($slotRepository, $cartslotRepository)) {
                $success = 'All Slots Cleared';
            } else {
                $error = 'All Slots Cleared';
            }
        }

        return $this->render('manage_cart/index.html.twig', [
            'clearSlotForm' => $clearSlotForm->createView(),
            'error' => $error,
            'success' => $success,
        ]);
    }

    private function clearSlot(SlotRepository $slotRepository, CartSlotRepository $cartslotRepository): bool
    {
        $slots = $cartslotRepository->findAll();
        foreach ($slots as $slot) {
            $slotIds[] = $slot->getId();
        }
        
        try {
            foreach ($slotIds as $slotId) {
                $slotRepository->clearSlot($slotId);
            }
        } catch(\Exception $e) {
            return false;
        }

        return true;
    }
}
