<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Share;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerBuilder;

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
    private \JMS\Serializer\Serializer $serializer;
    private string $mock;

    public function __construct(ManagerRegistry $registry)
    {
        $this->mock = __DIR__ .'/Mock/shares.json';
        $this->serializer = SerializerBuilder::create()->build();
        parent::__construct($registry, Share::class);
    }

    public function save(Share $entity, bool $flush = false): void
    {
        $shares = $this->unpackShares();
        $modifiedShares = $this->update($shares, $entity);
        $file = fopen($this->mock, 'wb');
        fwrite($file, $this->serializer->serialize($modifiedShares, 'json'));
        fclose($file);
    }

    public function remove(Share $entity, bool $flush = false): void
    {
        $file = fopen($this->mock, "wb");
        fwrite($file, '');
        fclose($file);
    }

    public function findAllShares(): array
    {
        return $this->unpackShares()->toArray();
    }

    public function findOneBySlug(string $slug): ?Share
    {
        $data = $this->unpackShares();
        /** @var Share $share */
        foreach ($data as $share) {
            if ($share->getSlug() === $slug) {
                return $share;
            }
        }
        return null;
    }

    public function findAllByAccount(string $accountHolder): array
    {
        $data = $this->unpackShares();
        $shares = [];
        /** @var Share $share */
        foreach ($data as $share) {
            if ($share->isOwned() &&
                $share->getOwner() === $accountHolder) {
                $shares[] = $share;
            }
        }
        return $shares;
    }

    /** @return ArrayCollection<Share> */
    private function unpackShares(): ArrayCollection
    {
        $file = fopen($this->mock, "rb");
        $data = fread($file, 255);
        fclose($file);
        try {
            $shares = $this->serializer->deserialize(
                $data,
                'ArrayCollection<'.Share::class.'>',
                'json'
            );
        } catch (\JMS\Serializer\Exception\Exception) {
            return new ArrayCollection();
        }
        return $shares;
    }

    private function update(ArrayCollection $shares, Share $entity): ArrayCollection
    {
        foreach ($shares as $share) {
            if ($share->getSlug() === $entity->getSlug()){
                $shares->removeElement($share);
                $shares->add($entity);
                return $shares;
            }
        }
        $shares[] = $entity;
        return $shares;
    }
}
