<?php

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    private const REVIEWS = [
        [
            'content' => 'Un chef-d\'œuvre de la littérature française ! La description de la misère sociale est poignante.',
            'rating' => 5,
            'user' => 'user-regular',
            'book' => 'book-les-miserables'
        ],
        [
            'content' => 'Une analyse fine de la bourgeoisie provinciale. Le style de Flaubert est remarquable.',
            'rating' => 4,
            'user' => 'user-librarian',
            'book' => 'book-madame-bovary'
        ],
        [
            'content' => 'Une fresque sociale impressionnante sur le monde ouvrier. À lire absolument !',
            'rating' => 5,
            'user' => 'user-admin',
            'book' => 'book-germinal'
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::REVIEWS as $reviewData) {
            $review = new Review();
            $review->setContent($reviewData['content'])
                ->setRating($reviewData['rating'])
                ->setUser($this->getReference($reviewData['user']))
                ->setBook($this->getReference($reviewData['book']));
            
            $manager->persist($review);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BookFixtures::class,
        ];
    }
}
