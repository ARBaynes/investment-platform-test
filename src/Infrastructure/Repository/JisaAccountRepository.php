<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\JisaAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JisaAccount>
 *
 * @method JisaAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method JisaAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method JisaAccount[]    findAll()
 * @method JisaAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JisaAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JisaAccount::class);
    }

    public function save(JisaAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JisaAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return JISAAccount[] Returns an array of JISAAccount objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JISAAccount
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
