<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\JisaAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

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
    private SerializerInterface $serializer;
    private string $mock;

    public function __construct(ManagerRegistry $registry)
    {
        $this->mock = __DIR__ .'/Mock/jisa.json';
        $this->serializer = SerializerBuilder::create()->build();
        parent::__construct($registry, JisaAccount::class);
    }

    public function save(JisaAccount $entity, bool $flush = false): void
    {
        $file = fopen($this->mock, "wb");
        fwrite($file, $this->serializer->serialize($entity, 'json'));
        fclose($file);
    }

    public function remove(JisaAccount $entity, bool $flush = false): void
    {
        $file = fopen($this->mock, "wb");
        fwrite($file, '');
        fclose($file);
    }

    public function findOneByAccountHolder(string $accountHolder): ?JisaAccount
    {
        $file = fopen($this->mock, 'rb');
        $data = fread($file, 255);
        fclose($file);
        return $this->serializer->deserialize($data, JisaAccount::class, 'json');
    }
}
