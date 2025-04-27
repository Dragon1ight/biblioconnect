<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class WebTestCaseWithLogin extends WebTestCase
{
    protected function loginAsUser($client, string $email)
    {
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneByEmail($email);

        $client->loginUser($user);
    }
}
