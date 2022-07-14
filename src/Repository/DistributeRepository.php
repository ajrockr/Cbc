<?php

namespace App\Repository;

use App\Entity\Distribute;
use App\Service\DataTableService;
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
    private DataTableService $service;
    public function __construct(ManagerRegistry $registry, DataTableService $service)
    {
        parent::__construct($registry, Distribute::class);
        $this->service = $service;
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

    // Get the total number of entries
    public function countObjects() {
        return $this
            ->createQueryBuilder('object')
            ->select("count(object.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }
 
    public function getTableData($start, $length, $orders, $search, $columns, $conditions) {
 
        /***********************************************************
                            Define Variables
        ************************************************************/
        $table = 'distribute';
 
        /***********************************************************
                            Create queries
        ************************************************************/
        // Normal Query
        // $query = $this->createQueryBuilder($table);
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $qb = $this->createQueryBuilder('di')
            ->select('di.assetTag')
            ->getDQL();

        $query = $this->getEntityManager()->getRepository('App\Entity\Slot')->createQueryBuilder('s')
            ->select('p.id', 'p.last_name', 'p.first_name', 'a.asset_tag', 'cs.cart_slot_number')
            ->leftJoin('App\Entity\Person', 'p', 'WITH', 's.assignedPersonId = p.id')
            ->leftJoin('App\Entity\Asset', 'a', 'WITH', 's.assignedAssetId = a.id')
            ->leftJoin('App\Entity\CartSlot', 'cs', 'WITH', 's.number = cs.id')
            ->leftJoin('App\Entity\Distribute', 'd', 'WITH', 'a.asset_tag = d.assetTag')
            ->where($expr->notIn('a.asset_tag', $qb));
         
        // Create Count Query
        $countQuery = $this->createQueryBuilder($table);
 
        $this->service->countObjectsInTable($countQuery,$table);
 
        /***********************************************************
                            Exception: DataTable show All
        ************************************************************/
        //Execute Count query already here to update length if it is a negative value due to pageLength = All
        $this->service->setLength($countQuery, $length);
 
 
        /***********************************************************
                            Create Joins
        ************************************************************/
        //EXAMPLE ARRAY TO PASS
        //  $joins = array (
        //      array("table0","field0","alias0"),
        //      array("table1","field1","alias1")
        //  );
 
        //$this->service->addJoins($query, $joins);
         
        /***********************************************************
                            Add specific WHERE Clauses
        ************************************************************/ 
        $this->service->addConditions($query, $conditions);
        
        /***********************************************************
                            Perform search
        ************************************************************/
        if ($search['value'] != "") {
            $this->service->performSearch($query,$countQuery,$table, $columns, $search);
        }
         
        /***********************************************************
                            Add limits
        ************************************************************/ 
        $this->service->addLimits($query, $start, $length);
         
        /***********************************************************
                            Perform ordering
        ************************************************************/
        $this->service->performOrdering($query, $orders, $table);
 
        /***********************************************************
                            Execute Query
        ************************************************************/
        $results = $query->getQuery()->getResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();       
         
        /***********************************************************
                            Return Results
        ************************************************************/
        return array(
            "results"       => $results,
            "countResult"   => $countResult
        );
    }
}

