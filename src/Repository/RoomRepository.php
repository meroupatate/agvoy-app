<?php

namespace App\Repository;

use App\Entity\Region;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @param Region $region
     * @return Room[]
     */
    public function findAllInRegion(Region $region): array
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository('App\Entity\Room');
        $query = $repository->createQueryBuilder('room')
            ->leftJoin('room.regions', 'region')
            ->where('region.id = :region_id')
            ->orderBy('room.id', 'DESC')
            ->setParameter('region_id', $region->getId())
            ->getQuery();

        // returns an array of Product objects
        return $query->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Room
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
