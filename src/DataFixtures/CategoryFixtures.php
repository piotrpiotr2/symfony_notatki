<?php
/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CategoryFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CategoryFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(20, 'categories', function (int $i) {
            $category = new Category();
            $category->setName($this->faker->unique()->word);

            $user = $this->getRandomReference('users');
            $category->setAuthor($user);

            return $category;
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