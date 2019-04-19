<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Récuperer les articles du spotlight
     * Uniquement les 5 derniers
     * Trier par ordre décroissant
     */
    public function findBySpotlight()
    {
        # SELECT * FROM article as a
        # WHERE a.spotlight = 1
        return $this->createQueryBuilder('a')
                ->where('a.spotlight = 1')
                ->orderBy('a.id', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();
    }

    /**
     * Récuperer les articles à la position special
     * Uniquement les 5 derniers
     * Trier par ordre décroissant
     */
    public function findBySpecial()
    {
        return $this->createQueryBuilder('a')
            ->where('a.special = 1')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer les 5 derniers articles de la BDD
     */
    public function findByLatest()
    {
        return $this->createQueryBuilder('a')
                ->orderBy('a.id', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
