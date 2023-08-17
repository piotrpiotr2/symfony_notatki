<?php
/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\NoteRepository;
use App\Repository\CategoryRepository;
use Exception;

class CategoryService
{
    /**
     * Category repository.
     */
    private CategoryRepository $categoryRepository;

    /**
     * Article repository.
     */
    private NoteRepository $noteRepository;

    public function __construct(CategoryRepository $categoryRepository, NoteRepository $noteRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->noteRepository = $noteRepository;
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     */
    public function delete(Category $category): bool
    {
        if (!$this->canBeDeleted($category)) {
            return false;
        }

        $this->categoryRepository->delete($category);
        return true;
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            return !($this->noteRepository->countByCategory($category) > 0);
        } catch (Exception) {
            return false;
        }
    }

    public function getAllList(): array
    {
        return $this->categoryRepository->queryAll();
    }
}