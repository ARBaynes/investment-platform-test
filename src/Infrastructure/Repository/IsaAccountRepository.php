<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\IsaAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * @extends ServiceEntityRepository<IsaAccount>
 *
 * @method IsaAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method IsaAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method IsaAccount[]    findAll()
 * @method IsaAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IsaAccountRepository extends ServiceEntityRepository
{
    private SerializerInterface $serializer;
    private string $mock;

    public function __construct(ManagerRegistry $registry)
    {
        $this->mock = __DIR__ .'/Mock/isa.json';
        $this->serializer = SerializerBuilder::create()->build();
        parent::__construct($registry, IsaAccount::class);
    }

    public function save(IsaAccount $entity, bool $flush = false): void
    {
        $file = fopen($this->mock, "wb");
        fwrite($file, $this->serializer->serialize($entity, 'json'));
        fclose($file);
    }

    public function remove(IsaAccount $entity, bool $flush = false): void
    {
        $file = fopen($this->mock, "wb");
        fwrite($file, '');
        fclose($file);
    }

    public function findOneByAccountHolder(string $accountHolder): ?IsaAccount
    {
        $file = fopen($this->mock, 'rb');
        $data = fread($file, 255);
        fclose($file);
        return $this->serializer->deserialize($data, IsaAccount::class, 'json');
    }
}
