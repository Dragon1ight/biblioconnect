<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Recherche des livres par titre, auteur ou thème 
     */
    public function findBySearch(string $search): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->where('LOWER(b.title) LIKE LOWER(:search)')
            ->orWhere('LOWER(a.name) LIKE LOWER(:search)')
            ->orWhere('LOWER(b.theme) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les livres avec un stock faible
     */
    public function findLowStock(int $threshold = 3): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.quantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->orderBy('b.quantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les catégories uniques
     */
    public function findUniqueCategories(): array
    {
        return $this->createQueryBuilder('b')
            ->select('DISTINCT b.category')
            ->orderBy('b.category', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Récupère toutes les langues uniques
     */
    public function findUniqueLanguages(): array
    {
        return $this->createQueryBuilder('b')
            ->select('DISTINCT b.language')
            ->orderBy('b.language', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findByAuthorOrTitle(string $search): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.title LIKE :search')
            ->orWhere('b.author LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    public function findLowStockBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.quantity <= 5')
            ->orderBy('b.quantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countByCategory(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.category, COUNT(b.id) as count')
            ->groupBy('b.category')
            ->getQuery()
            ->getResult();
    }

    public function countByLanguage(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.language, COUNT(b.id) as count')
            ->groupBy('b.language')
            ->getQuery()
            ->getResult();
    }
}
