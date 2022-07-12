<?php

namespace App\Repository;

use App\Entity\Distribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Distribute>
 *
 * @method Distribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Distribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Distribute[]    findAll()
 * @method Distribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Distribute::class);
    }

    public function add(Distribute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Distribute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAssetsNotDistributed(): array
    {
        $assetEntity = $this->getEntityManager()
                            ->getRepository('App\Entity\Slot')
                            ->findAll();

        $distributed = $this->findAll();

        $dist = [];
        foreach ($distributed as $d) {
            $dist[] = 
                $d->getAssetTag()
            ;
        }

        $data = [];
        // @todo fix this
        dd($assetEntity);
        foreach ($assetEntity as $asset) {
            if (!in_array($asset->getAssignedAssetId()->getAssetTag(), $dist)) {
                $data[] = [
                    'asset_tag' => $asset->getAssignedAssetId()->getAssetTag(),
                    'person' => $asset->getAssignedPersonId()->getLastName() . ', ' . $asset->getAssignedPersonId()->getFirstName(),
                    'slot_number' => $asset->getNumber()->getCartSlotNumber()
                ];
            }
        }

        return $data;
    }

    public function getAssetsForRoulette(): array
    {
        return (null === $data = $this->findBy(['distributed' => false])) ? [] : $data;
    }

    public function getAssetsDistributed(): array
    {
        return (null === $data = $this->findBy(['distributed' => true])) ? [] : $data;
    }
}
