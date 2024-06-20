<?php

namespace App\Tests\Controller;

use App\Domain\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoTest extends WebTestCase
{
    // private $entityManager;
    // public function __construct(
    //     EntityManagerInterface $entityManager
    // )
    // {
    //     $this->entityManager = $entityManager;
    // }
    public function testIfCreateTodoIsSuccessful(): void
    {
        $client = static::createClient();

        // In case you need to simulate a logged in user :
        // $userRepository = static::getContainer()->get(UserRepository::class);
        // $testUser = $userRepository->findOneByEmail('john.doe@example.com');
        // $client->loginUser($testUser);

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $todo = $entityManager->find(Todo::class, 2);
        // // $todo = $this->entityManager->find(Todo::class, 2);

        // $client->request(Request::METHOD_POST, $urlGenerator->generate('todo_create'), [
        //     'title' => $todo->getTitle(),
        //     'description' => $todo->getDescription(),
        // ]);

        $crawler = $client->request('POST', '/api/todos');
        // dd($crawler);

        // $crawler = $client->request(Request::METHOD_POST, $urlGenerator->generate('todo_create'));

        // $form = $crawler->filter('form[name=todo]')->form([
        //     'todo[title]' => $todo->getTitle(),
        //     'todo[description]' => $todo->getDescription(),
        //     'todo[isCompleted]' => $todo->isCompleted(),
        //     'todo[createdAt]' => $todo->getCreatedAt(),
        // ]);

        // $client->submit($form);

        // $responseContent = $client->getResponse()->getContent();
        // $responseData = json_decode($responseContent, true);

        $this->assertResponseIsSuccessful();
        // $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        // $this->assertEquals('Test Post Title', $responseData['title']);
        // $this->assertEquals('This is the content of the test post.', $responseData['content']);
    }
    
    public function testIfTodoListIsSuccessful(): void 
    {
        $client = static::createClient();
        
        // $urlGenerator = $client->getContainer()->get('router');
        
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        
        $client->request('GET', '/api/todos');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testIfTodoByIdIsSuccessful(): void 
    {
        $client = static::createClient();
        
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $todo = $entityManager->find(Todo::class, 2);
        
        if (!$todo) {
            $this->fail('Todo with ID 2 not found.');
        }
        
        $client->request('GET', '/api/todos/' . $todo->getId());
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
