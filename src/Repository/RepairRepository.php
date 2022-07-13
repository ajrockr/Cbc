<?php

namespace App\Repository;

use App\Entity\Repair;
use App\Entity\RepairItem;
use App\Repository\RepairItemRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Repair>
 *
 * @method Repair|null find($id, $lockMode = null, $lockVersion = null)
 * @method Repair|null findOneBy(array $criteria, array $orderBy = null)
 * @method Repair[]    findAll()
 * @method Repair[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Repair::class);
    }

    public function add(Repair $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Repair $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getActiveRepairs()
    {
        $repairs = $this->createQueryBuilder('r')
            ->select('r.id', 'r.assetid as assettag', 'a.id as assetid', 'p.first_name', 'p.last_name', 'u.email', 'r.createdAt', 'r.modifiedAt', 'cs.cart_slot_number', 'r.notes', 'r.items')
            ->leftJoin('App\Entity\Asset', 'a', 'WITH', 'r.assetid = a.asset_tag')
            ->leftJoin('App\Entity\Person', 'p', 'WITH', 'r.personid = p.id')
            ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 'r.cartSlotId = cs.id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'r.technicianid = u.id')
            ->andWhere('r.active = 1')
            ->getQuery()
            ->getResult();

        $repairitemEntity = $this->getEntityManager()->getRepository('App\Entity\RepairItem');

        $repairItems = $repairitemEntity->createQueryBuilder('ri')
            ->select('ri.id', 'ri.name')
            ->getQuery()
            ->getResult();

        foreach($repairItems as $item) {
            $ri[$item['id']] = $item['name'];
        }

        foreach ($repairs as $repair) {
            $data[$repair['id']] = [
                'assetId' => $repair['assetid'],
                'assetTag' => $repair['assettag'],
                'assignedPerson' => $repair['last_name'] . ', ' . $repair['first_name'],
                'technician' => strstr($repair['email'], '@', true),
                'slot' => $repair['cart_slot_number'],
                'notes' => $repair['notes'],
                'createdAt' => $repair['createdAt'],
                'modifiedAt' => $repair['modifiedAt'],
                'items' => $this->combineArray($ri, array_flip(explode(', ', implode(', ', $repair['items'])))), 
                // 'items' => array_flip(explode(', ', implode(', ', $repair['items'])))
            ];
        }

        return $data;
    }

    // @todo function works, make look better
    private function combineArray(array $arr1, array $arr2) {
        foreach ($arr2 as $k => $v) {
            $newArr[$k] = $arr1[$k];
        }

        return $newArr;
    }
}
