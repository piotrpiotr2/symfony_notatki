<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    private CategoryService $categoryService;

    private TranslatorInterface $translator;

    public function __construct(CategoryService $categoryService, TranslatorInterface $translator)
    {
        $this->categoryService = $categoryService;
        $this->translator = $translator;
    }

    #[Route(
        name: 'category_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        $categories = $this->categoryService->getAllList($this->getUser());

        return $this->render(
            'category/index.html.twig',
            ['categories' => $categories]
        );
    }

    #[Route(
        '/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Category $category): Response
    {
        if ($category->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/show.html.twig', [ 'category' => $category ]);
    }

    #[Route(
        '/create',
        name: 'category_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setAuthor($this->getUser());
            $this->categoryService->save($category);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(
        '/{id}/edit',
        name: 'category_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Category $category): Response
    {
        if ($category->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(
            CategoryType::class, $category,
            ['method' => 'PUT', 'action' => $this->generateUrl('category_edit', ['id' => $category->getId()])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            ['form' => $form->createView(), 'category' => $category,]
        );
    }

    #[Route('/{id}/delete', name: 'category_delete', methods: 'GET|DELETE')]
    public function delete(Category $category, Request $request): Response
    {
        if ($category->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(FormType::class, $category, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('category_delete', ['id' => $category->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/delete.html.twig', ['form' => $form->createView(), 'category' => $category,]);
    }
}