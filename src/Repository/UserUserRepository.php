<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserUser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<UserUser>
 *
 * @method UserUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserUser[]    findAll()
 * @method UserUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserUser::class);
    }

//    /**
//     * @return UserUser[] Returns an array of UserUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserUser
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findFriendsByUser(User $user): array
{
    return $this->createQueryBuilder('uu')
        ->where('uu.user_source = :user OR uu.user_target = :user')
        ->andWhere('uu.Etat = :accepted') // Assuming you have an 'accepted' state for friendships
        ->setParameter('user', $user)
        ->setParameter('accepted', 'ami')
        ->getQuery()
        ->getResult();
}
}
