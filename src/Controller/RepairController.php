<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use App\Repository\RepairRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RepairController extends AbstractController
{
    #[Route('/repair', name: 'app_repair')]
    public function index(Request $request, RepairRepository $repairRepository, ManagerRegistry $doctrine): Response
    {
        $repairList = $repairRepository->getActiveRepairs();

        return $this->render('repair/index.html.twig', [
            'repairList' => $repairList,
        ]);
    } 

    #[Route('/repair/add', name: 'app_repair_add')]
    public function add(Request $request, ManagerRegistry $doctrine, Security $security): Response {
        $form = $this->createFormBuilder()
            ->add('technicianId', EntityType::class, [
                // @todo idk, wtf. not setting class gives error. class is needed. anything I put into class throws error that it's not mapped
                'class' => User::class,
                'query_builder' => function (EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('u')
                        ->select('u.id', 'u.email')
                        ->where('u.Enabled = 1')
                        ->getQuery()
                        ->getResult();

                    dump($qb);
                }
            ])
            ->getForm();

        return $this->render('repair/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
