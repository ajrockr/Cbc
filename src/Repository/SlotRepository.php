<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Slot>
 *
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    public function add(Slot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Slot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkAssignedPerson(int $personId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.assigned_person_id_id = :ap')
            ->setParameter('ap', $personId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function clearSlot(int $slotId)
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->andWhere('s.number = :sid')
            ->setParameter('sid', $slotId)
            ->getQuery()
            ->execute()
        ;
    }

    public function findSlots(int|null $cartNumber = null)
    {
        if (null === $cartNumber) {
            return $this->createQueryBuilder('s')
                ->select('a.id as assetid', 'a.asset_tag', 'a.NeedsRepair', 'p.first_name', 'p.id as personid', 'p.last_name', 'p.graduation_year', 'p.email', 'cs.id as slotid', 'cs.cart_slot_number', 's.IsFinished')
                ->leftJoin('App\Entity\Asset', 'a', 'WITH', 's.assignedAssetId = a.id')
                ->leftJoin('App\Entity\Person', 'p', 'WITH', 's.assignedPersonId = p.id')
                ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 's.number = cs.id')
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('s')
            ->select('a.id as assetid', 'a.asset_tag', 'a.NeedsRepair', 'p.first_name', 'p.id as personid', 'p.last_name', 'p.graduation_year', 'p.email', 'cs.id as slotid', 'cs.cart_slot_number', 's.IsFinished')
            ->leftJoin('App\Entity\Asset', 'a', 'WITH', 's.assignedAssetId = a.id')
            ->leftJoin('App\Entity\Person', 'p', 'WITH', 's.assignedPersonId = p.id')
            ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 's.number = cs.id')
            ->where('cs.cart_number = :cartnumber')
            ->setParameter('cartnumber', $cartNumber)
            ->getQuery()
            ->getResult();
    }
}
