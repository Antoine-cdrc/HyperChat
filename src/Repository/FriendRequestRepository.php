<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FriendRequest>
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }

    /**
     * Récupère tous les amis d'un utilisateur
     */
    public function findFriends(User $user): array
    {
        $qb = $this->createQueryBuilder('fr')
            ->where('fr.status = :status')
            ->setParameter('status', 'accepted');

        // Amis où l'utilisateur est l'expéditeur
        $qb1 = clone $qb;
        $friends1 = $qb1->andWhere('fr.sender = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        // Amis où l'utilisateur est le destinataire
        $qb2 = clone $qb;
        $friends2 = $qb2->andWhere('fr.receiver = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $friends = [];
        foreach ($friends1 as $request) {
            $friends[] = $request->getReceiver();
        }
        foreach ($friends2 as $request) {
            $friends[] = $request->getSender();
        }

        return $friends;
    }

    //    /**
    //     * @return FriendRequest[] Returns an array of FriendRequest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FriendRequest
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
