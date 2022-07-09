<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 *
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function add(Person $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Person $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addPerson(Person $entity, array $data)
    {
        $check = $this->createQueryBuilder('p')
            ->andWhere('p.last_name = :ln')
            ->andWhere('p.first_name = :fn')
            ->andWhere('p.middle_name = :mn')
            ->andWhere('p.graduation_year = :gy')
            ->setParameter('ln', $data['last_name'])
            ->setParameter('fn', $data['first_name'])
            ->setParameter('mn', $data['middle_name'])
            ->setParameter('gy', $data['graduation_year'])
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null == $check) {
            // insert slot
            try {
                $entity->setFirstName($data['first_name']);
                $entity->setLastName($data['last_name']);
                $entity->setMiddleName($data['middle_name']);
                $entity->setGraduationYear($data['graduation_year']);

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

    public function getId(string $lastName, string $firstName, int $graduationYear, ?string $middleName = null)
    {
        $result = $this->findOneBy([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'graduation_year' => $graduationYear
        ]);

        return (is_object($result)) ? $result->getId() : null;
    }

    /**
     * @todo Not needed right now. Will keep in case of future use...
     *
     * @param string $searchTerm
     * @return void
     */
    public function search(string $searchTerm)
    {
        return $this->createQueryBuilder('p')
            ->orWhere('p.last_name LIKE :st')
            ->orWhere('p.first_name LIKE :st')
            ->orWhere('p.middle_name LIKE :st')
            ->setParameter('st', $searchTerm)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
