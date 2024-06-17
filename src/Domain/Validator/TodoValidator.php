<?php

namespace App\Domain\Validator;

use App\Domain\Entity\Todo;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class TodoValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(Todo $todo): void
    {
        $errors = $this->validator->validate($todo);
        if (count($errors) > 0) {
            throw new ValidatorException((string) $errors);
        }
    }
}
