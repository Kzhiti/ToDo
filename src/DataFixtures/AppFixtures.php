<?php


namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder) {
        $this->encoder = $encoder;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager)
    {
        // Anonymous User
        $user = new User();
        $user->setId(-1);
        $user->setUsername('anonymous');
        $user->setEmail('anonymous@example.org');
        $user->setPassword($this->encoder->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $this->addReference('user-anonymous', $user);

        // Admin User
        $user = new User();
        $user->setId(1);
        $user->setUsername('admin');
        $user->setEmail('admin@example.org');
        $user->setPassword($this->encoder->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($user);
        $this->addReference('user-admin', $user);

        // Simple User
        $user = new User();
        $user->setId(2);
        $user->setUsername('user');
        $user->setEmail('user@example.org');
        $user->setPassword($this->encoder->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_USER'])
        ;
        $manager->persist($user);
        $this->addReference('user-simple', $user);

        // Tâche crée par utilisateur simple (Seul auteur peut la delete)
        $task = new Task();
        $task->setId(1); // Edition par utilisateur simple
        $task->setTitle('Test utilisateur simple');
        $task->setContent('Tâche utilisé pour les tests');
        $task->setUser($this->getReference('user-simple'));
        $manager->persist($task);

        // Tâche crée par utilisateur simple (Seul auteur peut la delete)
        $task = new Task();
        $task->setId(2); // Suppression par utilisateur simple qui est l'auteur
        $task->setTitle('Test utilisateur simple');
        $task->setContent('Tâche utilisé pour les tests');
        $task->setUser($this->getReference('user-simple'));
        $manager->persist($task);

        // Tâche créer par utilisateur anonyme (Seul admin peut la delete)
        $task = new Task();
        $task->setId(3); // Suppression par utilisateur simple (Doit être refusé)
        $task->setTitle('Test utilisateur anonyme');
        $task->setContent('Tâche utilisé pour les tests');
        $task->setUser($this->getReference('user-anonymous'));
        $manager->persist($task);

        // Tâche créer par admin (Seul l'auteur peut la delete)
        $task = new Task();
        $task->setId(4);
        $task->setTitle('Test admin');
        $task->setContent('Tâche utilisé pour les tests');
        $task->setUser($this->getReference('user-admin'));
        $manager->persist($task);

        // Desactive l'autoincrement des id
        $metadata = $manager->getClassMetadata(Task::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $metadata = $manager->getClassMetadata(User::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $manager->flush();

        // Reactive l'autoincrement des id pour que les actions de creation fonctionnent
        $metadata = $manager->getClassMetadata(Task::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_IDENTITY);

        $metadata = $manager->getClassMetadata(User::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_IDENTITY);
    }
}
