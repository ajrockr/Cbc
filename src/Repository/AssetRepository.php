<?php

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Asset>
 *
 * @method Asset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asset[]    findAll()
 * @method Asset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    public function add(Asset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Asset $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addAsset(Asset $entity, array $data)
    {
        $check = $this->createQueryBuilder('a')
            ->andWhere('a.asset_tag = :at')
            ->setParameter('at', $data['asset_tag'])
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null == $check) {
            // insert slot
            try {
                $entity->setAssetTag($data['asset_tag']);
                $entity->setSerialNumber($data['serial_number']);

                $em = $this->getEntityManager();
                $em->persist($entity);
                $em->flush();
            } catch(ORMException $e) {
                return false;
            }

            return true;
        }

        return false;
    }
//    /**
//     * @return Asset[] Returns an array of Asset objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Asset
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
