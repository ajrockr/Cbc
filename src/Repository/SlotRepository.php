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

    public function findSlots(int|null $cartNumber = null): array
    {
        $cartslotEntity = $this->getEntityManager()->getRepository('App\Entity\CartSlot');

        if (null === $cartNumber) {
            $results =  $this->createQueryBuilder('s')
                ->select('a.id as assetid', 'a.asset_tag', 'a.NeedsRepair', 'p.first_name', 'p.id as personid', 'p.last_name', 'p.graduation_year', 'p.email', 'cs.id as slotid', 'cs.cart_slot_number', 'cs.cart_number', 'cs.cart_row', 'cs.cart_side', 's.IsFinished', 'a.notes')
                ->leftJoin('App\Entity\Asset', 'a', 'WITH', 's.assignedAssetId = a.id')
                ->leftJoin('App\Entity\Person', 'p', 'WITH', 's.assignedPersonId = p.id')
                ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 's.number = cs.id')
                ->addOrderBy('cs.cart_number', 'ASC')
                ->addOrderBy('cs.cart_side', 'ASC')
                ->addOrderBy('cs.cart_row', 'ASC')
                ->addOrderBy('cs.cart_slot_number', 'ASC')
                ->getQuery()
                ->getResult();

            $qb = $cartslotEntity->createQueryBuilder('cs')
                ->select('cs.cart_slot_number', 'cs.cart_number', 'cs.cart_side', 'cs.cart_row', 'cs.id')
                ->addOrderBy('cs.cart_number', 'ASC')
                ->addOrderBy('cs.cart_side', 'ASC')
                ->addOrderBy('cs.cart_row', 'ASC')
                ->addOrderBy('cs.cart_slot_number', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            $results =  $this->createQueryBuilder('s')
                ->select('a.id as assetid', 'a.asset_tag', 'a.NeedsRepair', 'p.first_name', 'p.id as personid', 'p.last_name', 'p.graduation_year', 'p.email', 'cs.id as slotid', 'cs.cart_slot_number', 'cs.cart_number', 'cs.cart_row', 'cs.cart_side', 's.IsFinished', 'a.notes')
                ->leftJoin('App\Entity\Asset', 'a', 'WITH', 's.assignedAssetId = a.id')
                ->leftJoin('App\Entity\Person', 'p', 'WITH', 's.assignedPersonId = p.id')
                ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 's.number = cs.id')
                ->addOrderBy('cs.cart_number', 'ASC')
                ->addOrderBy('cs.cart_side', 'ASC')
                ->addOrderBy('cs.cart_row', 'ASC')
                ->addOrderBy('cs.cart_slot_number', 'ASC')
                ->where('cs.cart_number = :cartnumber')
                ->setParameter('cartnumber', $cartNumber)
                ->getQuery()
                ->getResult();

            $qb = $cartslotEntity->createQueryBuilder('cs')
                ->select('cs.cart_slot_number', 'cs.cart_number', 'cs.cart_side', 'cs.cart_row', 'cs.id')
                ->addOrderBy('cs.cart_number', 'ASC')
                ->addOrderBy('cs.cart_side', 'ASC')
                ->addOrderBy('cs.cart_row', 'ASC')
                ->addOrderBy('cs.cart_slot_number', 'ASC')
                ->where('cs.cart_number = :cartnumber')
                ->setParameter('cartnumber', $cartNumber)
                ->getQuery()
                ->getResult();
        }

        

        foreach ($qb as $slot) {
            // $dataSlots[$slot['cart_number']][$slot['cart_side']][$slot['cart_row']] = [];
            $dataSlots[$slot['cart_number']][$slot['cart_side']][$slot['cart_row']][$slot['cart_slot_number']] = [
                'slotNumber' => $slot['cart_slot_number'],
                'slotId' => $slot['id']
            ];
        }

        foreach ($results as $result) {
            $dataSlots[$result['cart_number']][$result['cart_side']][$result['cart_row']][$result['cart_slot_number']] = [
                'assetId' => $result['assetid'],
                'assetTag' => $result['asset_tag'],
                'needsRepair' => $result['NeedsRepair'],
                'firstName' => $result['first_name'],
                'lastName' => $result['last_name'],
                'personId' => $result['personid'],
                'graduationYear' => $result['graduation_year'],
                'email' => $result['email'],
                'isFinished' => $result['IsFinished'],
                'notes' => $result['notes'],
                'slotId' => $result['slotid'],
                'slotNumber' => $result['cart_slot_number']
            ];
        }

        // dd($dataSlots);
        return $dataSlots;
    }
}
