<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class HomeAndBookTest extends WebTestCase
{
    private function loginAs($client, $email = 'admin@biblioconnect.fr')
    {   
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneByEmail($email);

        $client->loginUser($user);
    }

    public function testHomeAndBookShow()
    {
        $client = static::createClient();   
        $this->loginAs($client, 'admin@biblioconnect.fr');

        $client->request('GET', '/book/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1'); 
        
        $client->request('GET', '/book/1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card-title'); 
    }
}
