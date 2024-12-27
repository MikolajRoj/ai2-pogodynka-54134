<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function findByCityAndCountry(string $city, ?string $country = null): ?Location
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.city = :city')
            ->setParameter('city', $city);

        if ($country) {
            $qb->andWhere('l.country = :country')
                ->setParameter('country', $country);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    //    /**
    //     * @return Location[] Returns an array of Location objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Location
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
