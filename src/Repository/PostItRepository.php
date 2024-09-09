<?php

// src/Repository/PostItRepository.php

namespace App\Repository;

use App\Entity\PostIt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostItRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostIt::class);
    }

}
