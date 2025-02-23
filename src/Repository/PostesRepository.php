<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Postes;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Postes>
 *
 * @method Postes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postes[]    findAll()
 * @method Postes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postes::class);
    }

//    /**
//     * @return Postes[] Returns an array of Postes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Postes
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
