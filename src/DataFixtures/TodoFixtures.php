<?php
/**
 * Todos fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Todo;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TodoFixtures.
 */
class TodoFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
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

        $this->createMany(300, 'todos', function (int $i) {
            $todo = new Todo();
            $todo->setName($this->faker->sentence);
            $todo->setDescription($this->faker->sentence);
            $todoList = $this->getRandomReference('todo_lists');
            $todo->setDone($this->faker->boolean());
            $todo->setTodoList($todoList);

            return $todo;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: TodoListFixtures::class}
     */
    public function getDependencies(): array
    {
        return [TodoListFixtures::class];
    }
}
