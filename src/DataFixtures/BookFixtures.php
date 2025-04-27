<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public const BOOKS = [
        [
            'title' => 'Les Misérables',
            'theme' => 'La misère sociale et la rédemption',
            'category' => 'Roman',
            'language' => 'Français',
            'quantity' => 5,
            'author_ref' => 'author-hugo',
            'reference' => 'book-les-miserables'
        ],
        [
            'title' => 'Madame Bovary',
            'theme' => 'La bourgeoisie provinciale et le romantisme',
            'category' => 'Roman',
            'language' => 'Français',
            'quantity' => 3,
            'author_ref' => 'author-flaubert',
            'reference' => 'book-madame-bovary'
        ],
        [
            'title' => 'Germinal',
            'theme' => 'La condition ouvrière et les luttes sociales',
            'category' => 'Roman',
            'language' => 'Français',
            'quantity' => 4,
            'author_ref' => 'author-zola',
            'reference' => 'book-germinal'
        ],
        [
            'title' => 'Le Père Goriot',
            'theme' => 'L\'amour paternel et l\'ingratitude',
            'category' => 'Roman',
            'language' => 'Français',
            'quantity' => 2,
            'author_ref' => 'author-balzac',
            'reference' => 'book-pere-goriot'
        ],
        [
            'title' => 'Du côté de chez Swann',
            'theme' => 'La mémoire et le temps qui passe',
            'category' => 'Roman',
            'language' => 'Français',
            'quantity' => 3,
            'author_ref' => 'author-proust',
            'reference' => 'book-swann'
        ],
        [
            'title' => 'L\'Étranger',
            'theme' => 'L\'absurdité de l\'existence',
            'category' => 'Roman',
            'language' => 'Français',
            'quantity' => 6,
            'author_ref' => 'author-camus',
            'reference' => 'book-etranger'
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::BOOKS as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title'])
                ->setTheme($bookData['theme'])
                ->setCategory($bookData['category'])
                ->setLanguage($bookData['language'])
                ->setQuantity($bookData['quantity'])
                ->setAuthor($this->getReference($bookData['author_ref']));

            $manager->persist($book);
            $this->addReference($bookData['reference'], $book);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AuthorFixtures::class,
        ];
    }
}
