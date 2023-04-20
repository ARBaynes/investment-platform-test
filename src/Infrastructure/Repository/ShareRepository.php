<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Share;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Share>
 *
 * @method Share|null find($id, $lockMode = null, $lockVersion = null)
 * @method Share|null findOneBy(array $criteria, array $orderBy = null)
 * @method Share[]    findAll()
 * @method Share[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Share::class);
    }

    public function save(Share $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Share $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
