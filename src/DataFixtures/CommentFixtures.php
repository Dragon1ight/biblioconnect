<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    private const COMMENTS = [
        [
            'content' => 'Un chef-d\'œuvre de la littérature française ! La description de la misère sociale est poignante.',
            'rating' => 5,
            'user' => 'user-regular',
            'book' => 'book-les-miserables',
            'isApproved' => true
        ],
        [
            'content' => 'Une analyse fine de la bourgeoisie provinciale. Le style de Flaubert est remarquable.',
            'rating' => 4,
            'user' => 'user-librarian',
            'book' => 'book-madame-bovary',
            'isApproved' => true
        ],
        [
            'content' => 'Une fresque sociale impressionnante sur le monde ouvrier. À lire absolument !',
            'rating' => 5,
            'user' => 'user-admin',
            'book' => 'book-germinal',
            'isApproved' => true
        ],
        [
            'content' => 'Je n\'ai pas vraiment apprécié ce livre, l\'histoire est trop longue.',
            'rating' => 2,
            'user' => 'user-regular',
            'book' => 'book-pere-goriot',
            'isApproved' => false
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::COMMENTS as $commentData) {
            $comment = new Comment();
            $comment->setContent($commentData['content'])
                ->setRating($commentData['rating'])
                ->setUser($this->getReference($commentData['user']))
                ->setBook($this->getReference($commentData['book']))
                ->setIsApproved($commentData['isApproved']);
            
            $manager->persist($comment);
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