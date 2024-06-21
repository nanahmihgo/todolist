<?php

namespace App\Tests\Controller;

use App\Domain\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\Repository\TodoRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoTest extends WebTestCase
{

    // TODOS CREATE
    public function testIfCreatedTodoIsSuccessful(): void
    {
        $client = static::createClient();

        // In case you need to simulate a logged in user :
        // $userRepository = static::getContainer()->get(UserRepository::class);
        // $testUser = $userRepository->findOneByEmail('john.doe@example.com');
        // $client->loginUser($testUser);
        
        $todo = new Todo();
        $urlGenerator = $client->getContainer()->get('router');
        
        $url = $urlGenerator->generate('todo_create');
        // $createdAt = new DateTimeImmutable();
        // dd($createdAt);
        
        $client->request(
            Request::METHOD_POST,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test Post Title',
                'description' => 'This is the content of the test post.',
                'completed' => true,
                // 'createdAt' => $createdAt
            ])
        );

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);
        // dd($responseData);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals('Test Post Title', $responseData['title']);
        $this->assertEquals('This is the content of the test post.', $responseData['description']);
        $this->assertEquals(true, $responseData['completed']);
        // $this->assertEquals($createdAt, $responseData['createdAt']);
    }
    

    // TODOS LIST
    public function testIfTodoListIsSuccessful(): void 
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/todos');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    // TODOS SHOW BY ID
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


    // TODOS UPDATE BY ID
    public function testIfTodoUpdatedIsSuccessful(): void 
    {
        $client = static::createClient();
        
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $todo = $entityManager->find(Todo::class, 1);
        
        if (!$todo) {
            $this->fail('Todo with ID 1 not found.');
        }
        
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'isCompleted' => $todo->isCompleted(),
            'updatedAt' => $todo->getUpdatedAt()
        ];
        
        $client->request('PUT', '/api/todos/' . $todo->getId(), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($updatedData));
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $entityManager->refresh($todo);
        
        $this->assertSame('Updated Title', $todo->getTitle());
        $this->assertSame('Updated Description', $todo->getDescription());
    }


    // TODOS DELETE BY ID
    public function testIfTodoDeletedIsSuccessful(): void 
    {
        $client = static::createClient();
        
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $todo = $entityManager->find(Todo::class, 1);
        
        if (!$todo) {
            $this->fail('Todo with ID 2 not found.');
        }
        
        $client->request('DELETE', '/api/todos/' . $todo->getId());
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        
        $deletedTodo = $entityManager->find(Todo::class, 1);
        $this->assertNull($deletedTodo, 'Todo should be deleted');
    }
}
