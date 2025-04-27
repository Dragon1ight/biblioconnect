<?php

namespace App\DataFixtures;

use App\Entity\BookAvailability;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookAvailabilityFixtures extends Fixture implements DependentFixtureInterface
{
    private const AVAILABILITIES = [
        [
            'book' => 'book-les-miserables',
            'available' => true,
            'dueDays' => null
        ],
        [
            'book' => 'book-madame-bovary',
            'available' => false,
            'dueDays' => 7
        ],
        [
            'book' => 'book-germinal',
            'available' => true,
            'dueDays' => null
        ],
        [
            'book' => 'book-pere-goriot',
            'available' => false,
            'dueDays' => 14
        ],
        [
            'book' => 'book-swann',
            'available' => true,
            'dueDays' => null
        ],
        [
            'book' => 'book-etranger',
            'available' => false,
            'dueDays' => 21
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::AVAILABILITIES as $availabilityData) {
            $availability = new BookAvailability();
            $dueDate = $availabilityData['dueDays'] ? 
                (new \DateTimeImmutable())->modify('+' . $availabilityData['dueDays'] . ' days') : 
                null;
            
            $availability->setBook($this->getReference($availabilityData['book']))
                ->setAvailable($availabilityData['available'])
                ->setDueDate($dueDate);
            
            $manager->persist($availability);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BookFixtures::class,
        ];
    }
}
