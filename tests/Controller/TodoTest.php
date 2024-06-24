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

        $urlGenerator = $client->getContainer()->get('router');
        $url = $urlGenerator->generate('todo_create');

        $createdAt = (new DateTimeImmutable())->format(DATE_ATOM); 
        $todoData = [
            'title' => 'Test new Post Title',
            'description' => 'This is the content of the test post.',
            'isCompleted' => false,
            'createdAt' => $createdAt
        ];

        $client->request(
            Request::METHOD_POST,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($todoData)
        );

        $responseContent = $client->getResponse()->getContent();
        $responseData = json_decode($responseContent, true);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->assertEquals($todoData['title'], $responseData['title']);
        $this->assertEquals($todoData['description'], $responseData['description']);
        $this->assertEquals($todoData['isCompleted'], $responseData['completed']);
        $this->assertEquals($todoData['createdAt'], $responseData['createdAt']);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('updatedAt', $responseData);
        $this->assertNull($responseData['updatedAt']);
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
        $todo = $entityManager->find(Todo::class, 44);
        
        if (!$todo) {
            $this->fail('Todo with ID 44 not found.');
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
        $todo = $entityManager->find(Todo::class, 43);
        
        if (!$todo) {
            $this->fail('Todo with ID 43 not found.');
        }
        
        $updatedAt = (new DateTimeImmutable())->format(DATE_ATOM);
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'isCompleted' => true,
            'updatedAt' => $updatedAt
        ];
        
        $client->request(
            'PUT', 
            '/api/todos/' . $todo->getId(),
            [],
            [], 
            ['CONTENT_TYPE' => 'application/json'], 
            json_encode($updatedData)
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $entityManager->refresh($todo);
        
        $this->assertSame('Updated Title', $todo->getTitle());
        $this->assertSame('Updated Description', $todo->getDescription());
        $this->assertSame(true, $todo->isCompleted());
        $this->assertSame($updatedAt, $todo->getUpdatedAt()->format(DATE_ATOM));
    }


    // TODOS DELETE BY ID
    public function testIfTodoDeletedIsSuccessful(): void 
    {
        $client = static::createClient();
        
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $todo = $entityManager->find(Todo::class, 45);
        
        if (!$todo) {
            $this->fail('Todo with ID 45 not found.');
        }
        
        $client->request('DELETE', '/api/todos/' . $todo->getId());
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        
        $deletedTodo = $entityManager->find(Todo::class, 45);
        $this->assertNull($deletedTodo, 'Todo should be deleted');
    }
}
