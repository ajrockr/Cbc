<?php

namespace App\Controller;

use App\Entity\Repair;
use App\Repository\RepairRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RepairController extends AbstractController
{
    #[Route('/repair', name: 'app_repair')]
    public function index(Request $request, RepairRepository $repairRepository, ManagerRegistry $doctrine): Response
    {
        // if (null !== $repairs = $repairRepository->findAll()) {
        $entityManager = $doctrine->getManager();
        $repairs = $entityManager->getRepository(Repair::class)
            ->findAll();

        $repairList = [];
        if (null !== $repairs) {
            foreach ($repairs as $repair) {
                $repairList[] = [
                    'id' => $repair->getId(),
                    'assetAssetTag' => (!empty($repair->getAssetid()->toArray()[0]->getAssetTag())) ? $repair->getAssetid()->toArray()[0]->getAssetTag() : null,
                    'assetSlotNumber' => (!empty($repair->getCartSlotId()->toArray()[0]->getNumber()->getCartSlotNumber())) ? $repair->getCartSlotId()->toArray()[0]->getNumber()->getCartSlotNumber() : null,
                    'assignedPerson' => (!empty($repair->getCartSlotId()->toArray()[0]->getAssignedPersonId())) ? $repair->getCartSlotId()->toArray()[0]->getAssignedPersonId()->getLastName() . ', ' . $repair->getCartSlotId()->toArray()[0]->getAssignedPersonId()->getFirstName() : null,
                    'createdAt' => $repair->getCreatedAt(),
                    'modifiedAt' => $repair->getModifiedAt(),
                    'technician' => (!empty($repair->getTechnicianid()->toArray()[0]->getEmail())) ? strstr($repair->getTechnicianid()->toArray()[0]->getEmail(), '@', true) : null,
                    'active' => $repair->isActive(),
                    'closedAt' => $repair->getClosedAt(),
                    'notes' => $repair->getNotes(),
                    'item' => $repair->getItems(),
                ];
            }
        }

        return $this->render('repair/index.html.twig', [
            'repairList' => $repairList,
        ]);
    }
}
