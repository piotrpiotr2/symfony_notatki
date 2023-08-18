<?php
/**
 * Todo fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Todo;

/**
 * Class TodoFixtures.
 */
class TodoFixtures extends AbstractBaseFixtures
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

        $this->createMany(100, 'todos', function (int $i) {
            $todoList = new Todo();
            $todoList->setName($this->faker->sentence);
            $todoList->setDescription($this->faker->sentence);

            return $todoList;
        });

        $this->manager->flush();
    }
}