<?php

namespace App\Repository;

use App\Entity\CartSlot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartSlot>
 *
 * @method CartSlot|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartSlot|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartSlot[]    findAll()
 * @method CartSlot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartSlot::class);
    }

    public function add(CartSlot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartSlot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByNumber($value): array
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.cart_number = :val')
            ->setParameter('val', $value)
            ->orderBy('cs.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function addSlot(CartSlot $entity, int $cart, int $row, int $slot, string|int $side): bool
    {
        $check = $this->createQueryBuilder('cs')
            ->andWhere('cs.cart_row = :row')
            ->andWhere('cs.cart_slot_number = :slot')
            ->andWhere('cs.cart_side = :side')
            ->setParameter('row', $row)
            ->setParameter('slot', $slot)
            ->setParameter('side', $side)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null == $check) {
            // insert slot
            try {
                $entity->setCartNumber($cart);
                $entity->setCartRow($row);
                $entity->setCartSlotNumber($slot);
                $entity->setCartSide($side);

                $em = $this->getEntityManager();
                $em->persist($entity);
                $em->flush();
            } catch (ORMException $e) {
                return false;
            }
            
            return true;
        }

        return false;
    }

    public function getCartSlots(int $cart)
    {
        $results = $this->createQueryBuilder('cs')
            ->select('cs.cart_slot_number', 'cs.id', 'cs.cart_row', 'cs.cart_side')
            ->andWhere('cs.cart_number = :cart')
            ->setParameter('cart', $cart)
            ->orderBy('cs.cart_slot_number', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        if (count($results) <= 0) {
            return false;
        }

        // 1) Make array of sides
        // 2) Make array of rows
        // 3) Iterate through results and assign slot => row => side

        foreach ($results as $result) {
            $sides[] = $result['cart_side'];
            $rows[] = $result['cart_row'];
        }
        
        $sides = array_unique($sides);
        $rows = array_unique($rows);
        
        foreach ($sides as $side) {
            foreach ($rows as $row) {
                foreach ($results as $result) {
                    if ($result['cart_side'] == $side) {
                        if ($result['cart_row'] == $row) {
                            $data[$side][$row][] = [$result['cart_slot_number'] => $result['id']];
                        }
                    }
                }
            }
        }

        return $data;
    }
}
