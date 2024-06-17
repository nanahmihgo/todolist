<?php

namespace App\Presentation\Controller;

use App\Application\Service\TodoService;
use App\Domain\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    private $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    #[Route('/api/todos', name: 'todo_list', methods: ['GET'])]
    public function list(): Response
    {
        $todos = $this->todoService->getAllTodos();
        return $this->json($todos);
    }

    #[Route('/api/todos/{id}', name: 'todo_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $todo = $this->todoService->getTodoById($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($todo);
    }

    #[Route('/api/todos', name: 'todo_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $todo = $this->todoService->createTodo(
            $data['title'],
            $data['description']
        );
        return $this->json($todo, Response::HTTP_CREATED);
    }

    #[Route('/api/todos/{id}', name: 'todo_update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        $todo = $this->todoService->getTodoById($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $updatedTodo = $this->todoService->updateTodo(
            $todo,
            $data['title'],
            $data['description'],
            $data['isCompleted']
        );
        return $this->json($updatedTodo);
    }

    #[Route('/api/todos/{id}', name: 'todo_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $todo = $this->todoService->getTodoById($id);
        if (!$todo) {
            return $this->json(['error' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $this->todoService->deleteTodo($todo);
        return $this->json(['status' => 'Todo deleted'], Response::HTTP_NO_CONTENT);
    }
}

