<?php

namespace App\Repository;

use App\Entity\Story;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Story>
 *
 * @method Story|null find($id, $lockMode = null, $lockVersion = null)
 * @method Story|null findOneBy(array $criteria, array $orderBy = null)
 * @method Story[]    findAll()
 * @method Story[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }
    public function findActiveStoriesByUser($user){
        return $this->createQueryBuilder('h')
            ->andWhere('h.userStory = :user')
            ->andWhere('h.expireDate > :now')
            ->setParameter('user',$user)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findAllDiferentStories()
    {
        return $this->createQueryBuilder('s')
            ->select('DISTINCT s')
            ->join('s.userStory', 'u')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Story[] Returns an array of Story objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Story
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
