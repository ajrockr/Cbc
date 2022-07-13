<?php

namespace App\Repository;

use App\Entity\Distribute;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $qb = $this->createQueryBuilder('di')
            ->select('di.assetTag')
            ->getDQL();

        $assetEntity = $this->getEntityManager()->getRepository('App\Entity\Slot')->createQueryBuilder('s')
            ->select('p.id', 'p.last_name', 'p.first_name', 'a.asset_tag', 'cs.cart_slot_number')
            ->leftJoin('App\Entity\Person', 'p', 'WITH', 's.assignedPersonId = p.id')
            ->leftJoin('App\Entity\Asset', 'a', 'WITH', 's.assignedAssetId = a.id')
            ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 's.number = cs.id')
            ->leftJoin('App\Entity\Distribute', 'd', 'WITH', 'a.asset_tag = d.assetTag')
            ->where($expr->notIn('a.asset_tag', $qb))
            // ->where('a.asset_tag = d.assetTag')
            ->getQuery()
            ->getResult();

        return $assetEntity;
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
