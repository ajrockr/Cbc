<?php

namespace App\Repository;

use App\Entity\RepairItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RepairItem>
 *
 * @method RepairItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method RepairItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method RepairItem[]    findAll()
 * @method RepairItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepairItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RepairItem::class);
    }

    public function add(RepairItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RepairItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
