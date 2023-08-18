<?php

namespace App\Service;

use App\Entity\Note;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;

class NoteService
{
    private NoteRepository $noteRepository;

    private PaginatorInterface $paginator;

    private TagService $tagService;

    private CategoryService $categoryService;

    public function __construct(NoteRepository $noteRepository, PaginatorInterface $paginator, TagService $tagService, CategoryService $categoryService)
    {
        $this->noteRepository = $noteRepository;
        $this->paginator = $paginator;
        $this->tagService = $tagService;
        $this->categoryService = $categoryService;
    }

    public function save(Note $note): void
    {
        $this->noteRepository->save($note);
    }

    public function delete(Note $note): void
    {
        $this->noteRepository->delete($note);
    }

    public function getPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);
        return $this->paginator->paginate(
            $this->noteRepository->queryAll($filters), $page, NoteRepository::PAGINATOR_ITEMS_PER_PAGE);
    }

    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];

        if (!empty($filters['category_id'])) {
            $category = $this->categoryService->findOneById($filters['category_id']);
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        if (!empty($filters['tag_id'])) {
            $tag = $this->tagService->findOneById($filters['tag_id']);
            if (null !== $tag) {
                $resultFilters['tag'] = $tag;
            }
        }

        return $resultFilters;
    }
}