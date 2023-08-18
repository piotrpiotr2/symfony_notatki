<?php
/**
 * TodoList fixtures.
 */

namespace App\DataFixtures;

use App\Entity\TodoList;

/**
 * Class TodoListFixtures.
 */
class TodoListFixtures extends AbstractBaseFixtures
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

            return $todoList;
        });

        $this->manager->flush();
    }
}