<?php

namespace App\Repository;

use App\Entity\Location;

use App\Entity\Measurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Measurement>
 */
class MeasurementRepository extends ServiceEntityRepository
{
    /** @return Measurement[] */
    public function findAllWithLocation(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.location', 'location')
            ->addSelect('location')
            ->orderBy('m.date', 'DESC')
            ->addOrderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithLocation(int $id): ?Measurement
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.location', 'location')
            ->addSelect('location')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByLocation(Location $location) {
        $gb = $this->createQueryBuilder('m');
        $gb->where('m.location = :location')
            ->setParameter('location', $location)
            ->andWhere('m.date > :now')
            ->setParameter('now', date('Y-m-d'));

        $query = $gb->getQuery();
        $results = $query->getResult();
        return $results;
    }


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Measurement::class);
    }

//    /**
//     * @return Measurement[] Returns an array of Measurement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Measurement
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
