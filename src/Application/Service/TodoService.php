<?php


namespace App\Application\Service;


use App\Domain\Entity\Todo;
use App\Infrastructure\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class TodoService
{
    private $todoRepository;
    private $entityManager;
    private $validator;

    public function __construct(TodoRepository $todoRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->todoRepository = $todoRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createTodo($title, $description, $isCompleted, $createdAt = new \DateTimeImmutable()): Todo
    {
        $todo = new Todo();
        $todo->setTitle($title);
        $todo->setDescription($description);
        $todo->setCompleted($isCompleted);
        $todo->setCreatedAt($createdAt);

        $errors = $this->validator->validate($todo);
        if (count($errors) > 0) {
            throw new ValidatorException((string) $errors);
        }

        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        return $todo;
    }

    public function updateTodo(Todo $todo, $title, $description, $isCompleted): Todo
    {
        $todo;
        $todo->setTitle($title);
        $todo->setDescription($description);
        $todo->setCompleted($isCompleted);
        $todo->setUpdatedAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($todo);
        if (count($errors) > 0) {
            throw new ValidatorException((string) $errors);
        }

        $this->entityManager->flush();

        return $todo;
    }

    public function deleteTodo(Todo $todo): void
    {
        $this->entityManager->remove($todo);
        $this->entityManager->flush();
    }

    public function getTodoById($id): ?Todo
    {
        return $this->todoRepository->find($id);
    }

    public function getAllTodos(): array
    {
        return $this->todoRepository->findAll();
    }
}
