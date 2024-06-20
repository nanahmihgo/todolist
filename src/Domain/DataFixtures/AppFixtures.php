<?php

namespace App\Domain\DataFixtures;

use App\Domain\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $todo = new Todo();
            $todo->setTitle('todo '.$i);
            $todo->setDescription('todoblablabla '.$i);
            $manager->persist($todo);
        }

        $manager->flush();
    }
}
