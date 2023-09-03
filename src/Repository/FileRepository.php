<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Users;


/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    /**
    * @return File[] Returns an array of User objects
    */
    public function getFiles(): array
    {
        return $this->findAll();
    }

    public function deleteFilesForUser(Users $user)
    {
        $this->createQueryBuilder('f')
            ->delete()
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function countFilesForUser($user)
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFilesByClient($clientId)
    {
        return $this->createQueryBuilder('f')
            ->where('f.user = :clientId')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    public function findAllClientFiles()
    {
        $qb = $this->createQueryBuilder('f');
        $qb->leftJoin('f.user', 'u');

        return $qb->where($qb->expr()->notLike('u.roles', ':role'))
                  ->setParameter('role', '%ROLE_ADMIN%')
                  ->getQuery()
                  ->getResult();
    }

    public function getTotalFiles()
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFilesUploadedToday()
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->where('f.date > :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFilesCountByUsers()
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->select('u.first_name, u.last_name, COUNT(f.id) as fileCount')
            ->join('f.user', 'u')  // ici, u représente l'entité Users
            ->groupBy('u.id, u.first_name, u.last_name');

        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return File[] Returns an array of File objects
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

//    public function findOneBySomeField($value): ?File
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
