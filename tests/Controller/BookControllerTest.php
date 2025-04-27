<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class BookControllerTest extends WebTestCase
{
    private function loginAs($client, $email = 'admin@biblioconnect.fr')
    {
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneByEmail($email);

        $client->loginUser($user);
    }

    public function testLoginAndAddBook()
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@biblioconnect.fr');

        $crawler = $client->request('GET', '/book/new');
        $this->assertResponseIsSuccessful();

      
        $form = $crawler->selectButton('Enregistrer')->form([
           'book[title]' => 'Test Book',
            'book[author]' => 1, 
            'book[category]' => 'ROMAN',
            'book[theme]' => 'Test Theme',
            'book[language]' => 'fr',
            'book[quantity]' => 5,
        ]);
        $client->submit($form);
        $client->followRedirect();

        $this->assertSelectorTextContains('.alert-success', 'Le livre a été ajouté avec succès.');
    }
}
