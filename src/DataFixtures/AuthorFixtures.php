<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public const AUTHORS = [
        [
            'name' => 'Victor Hugo',
            'reference' => 'author-hugo'
        ],
        [
            'name' => 'Gustave Flaubert',
            'reference' => 'author-flaubert'
        ],
        [
            'name' => 'Émile Zola',
            'reference' => 'author-zola'
        ],
        [
            'name' => 'Honoré de Balzac',
            'reference' => 'author-balzac'
        ],
        [
            'name' => 'Marcel Proust',
            'reference' => 'author-proust'
        ],
        [
            'name' => 'Albert Camus',
            'reference' => 'author-camus'
        ],
        [
            'name' => 'Jean-Paul Sartre',
            'reference' => 'author-sartre'
        ],
        [
            'name' => 'Simone de Beauvoir',
            'reference' => 'author-beauvoir'
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::AUTHORS as $authorData) {
            $author = new Author();
            $author->setName($authorData['name']);
            
            $manager->persist($author);
            $this->addReference($authorData['reference'], $author);
        }

        $manager->flush();
    }
}
