<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class UserTest extends TestCase
{
    public function testGetId()
    {
        $user = new User();
        static::assertEquals($user->getId(), null);
    }

    public function testGetSetUsername()
    {
        $user = new User();
        $user->setUsername('Test username');
        static::assertEquals($user->getUsername(), 'Test username');
    }

    public function testGetSetPassword()
    {
        $user = new User();
        $user->setPassword('Test password');
        static::assertEquals($user->getPassword(), 'Test password');
    }

    public function testGetSetEmail()
    {
        $user = new User();
        $user->setEmail('Test@email.com');
        static::assertEquals($user->getEmail(), 'Test@email.com');
    }

    public function testGetSalt()
    {
        $user = new User();
        static::assertEquals($user->getSalt(), null);
    }

    public function testGetAddTask()
    {
        $user = new User();
        static::assertInstanceOf(User::class, $user->AddTask(new Task()));
        static::assertInstanceOf(ArrayCollection::class, $user->getTasks());
        static::assertContainsOnlyInstancesOf(Task::class, $user->getTasks());
    }

    public function testRemoveTask()
    {
        $user = new User();
        // If there is not the Task in the ArrayCollection
        static::assertInstanceOf(User::class, $user->removeTask(new Task()));
        static::assertEmpty($user->getTasks());

        // If there is the Task in the ArrayCollection
        $task = new Task();
        $user->addTask($task);
        $user->removeTask($task);
        static::assertEmpty($user->getTasks());
        static::assertInstanceOf(User::class, $user->removeTask(new Task()));
    }

    public function testGetSetRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        static::assertEquals($user->getRoles(), ['ROLE_ADMIN', 'ROLE_USER']);
    }

    public function testEraseCredentials()
    {
        $user = new User();
        static::assertEquals($user->eraseCredentials(), null);
    }
}
