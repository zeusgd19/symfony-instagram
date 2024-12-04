<?php

namespace App\Repository;

use App\Entity\Historia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Historia>
 *
 * @method Historia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Historia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Historia[]    findAll()
 * @method Historia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Historia::class);
    }

    public function findActiveStoriesByUser($user){
        return $this->createQueryBuilder('h')
            ->andWhere('h.usuario = :user')
            ->andWhere('h.fechaExpiracion > :now')
            ->setParameter('user',$user)
            ->setParameter('now',new \DateTime())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Historia[] Returns an array of Historia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Historia
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
