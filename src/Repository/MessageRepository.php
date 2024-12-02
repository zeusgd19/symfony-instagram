<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findConversationsForUser(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT s.id, s.username, s.photo') // Selecciona campos necesarios
            ->innerJoin('m.sender', 's')
            ->where('m.receiver = :userId') // Condición
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }


    public function findMessagesBetweenUsers(int $userId, int $otherUserId): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :userId AND m.receiver = :otherUserId) OR (m.sender = :otherUserId AND m.receiver = :userId)')
            ->setParameters([
                'userId' => $userId,
                'otherUserId' => $otherUserId,
            ])
            ->orderBy('m.id', 'ASC') // Orden cronológico
            ->getQuery()
            ->getResult();
    }
}
