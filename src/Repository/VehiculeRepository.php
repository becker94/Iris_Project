<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }


    public function findAvailable(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('v.marque', 'ASC')
            ->getQuery()
            ->getResult();
    }
}