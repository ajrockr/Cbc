<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Entity\CartSlot;
use App\Entity\Distribute;
use Monolog\DateTimeImmutable;
use App\Service\DataTableService;
use App\Repository\ConfigRepository;
use App\Repository\DistributeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        if ($request->isMethod('POST') && (null !== $request->request->get('distribute'))) {
            $data = explode(':', $request->request->get('distribute'));
            $input['asset_tag'] = $data[0];
            $input['person'] = $data[2];
            $input['slot_number'] = $data[1];

            $entityManger = $doctrine->getManager();
            $distributeEntity = $entityManger->getRepository(Distribute::class)
                                            ->findOneBy([
                                                'assetTag' => $input['asset_tag'],
                                            ])
            ;

            if (null === $distributeEntity) {
                $distributeEntity = new Distribute();
                $distributeEntity->setAssetTag($input['asset_tag'])
                    ->setSlotNumber($input['slot_number'])
                    ->setDistributed(false)
                    ->setCreatedAt(new \DateTimeImmutable("now"))
                    ->setDistributedBy($this->getUser()->getUserIdentifier())
                    ->setAssignedPerson($input['person']);
                $entityManger->persist($distributeEntity);
                $entityManger->flush();
            }

            $distributeEntity->setAssetTag($input['asset_tag'])
                ->setSlotNumber($input['slot_number'])
                ->setDistributed(false)
                ->setCreatedAt(new \DateTimeImmutable("now"))
                ->setAssignedPerson($input['person'])
                ->setDistributedBy($this->getUser()->getUserIdentifier());
            $entityManger->flush();

            return $this->redirectToRoute('app_distribution');
        }

        return $this->render('distribution/distribute.html.twig', [
            'toDistribute' => $toDistribute,
        ]);
    }

    #[Route('/distribute/ajax', name: 'app_distribute_ajax', methods: ['GET', 'POST'])]
    public function table(DataTableService $service, Request $request, DistributeRepository $distributeRepository) {
 
        $response = $service->getData($request, $distributeRepository);
 
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);
         
        return $returnResponse;       
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
            $slotId = $cartslotEntity->findOneBy(['cart_slot_number' => $distributeEntity->getSlotNumber()])->getId();
            $slotEntity = $doctrine->getRepository(Slot::class)
                ->createQueryBuilder('s')
                ->delete()
                ->where('id = :slotid')
                ->setParameter('slotid', $slotId)
                ->getQuery()
                ->execute();
            // $cartslotEntity = $doctrine->getRepository(CartSlot::class);
            // $slot = $slotEntity->findOneBy(['number' => $slotId]);
            // // $entityManger->remove($slot);
            // $entityManger->flush();
        }

        return $this->redirectToRoute('app_roulette');
    }
}
