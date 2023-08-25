<?php
/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

/**
 * Class TagService.
 */
class TagService
{
    /**
     * Tag repository.
     */
    private TagRepository $tagRepository;

    /**
     * Constructor
     *
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Save
     *
     * @param Tag $tag
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag);
    }

    /**
     * Delete
     *
     * @param Tag $tag
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }

    /**
     * Find one
     *
     * @param int $id
     *
     * @return Tag|null
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->findOneById($id);
    }

    /**
     * Get all.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->tagRepository->findAll();
    }
}
