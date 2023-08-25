<?php
/**
 * TodoList fixtures.
 */

namespace App\DataFixtures;

use App\Entity\TodoList;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TodoListFixtures.
 */
class TodoListFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'todo_lists', function (int $i) {
            $todoList = new TodoList();
            $todoList->setTitle($this->faker->sentence);

            $user = $this->getRandomReference('users');
            $todoList->setAuthor($user);

            return $todoList;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
