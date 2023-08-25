<?php
/**
 * Category controller.
 */

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

/**
 * Class CategoryController.
 */
#[Route('/categories')]
class CategoryController extends AbstractController
{
    /**
     * Category service.
     */
    private CategoryService $categoryService;

    /**
     * Translator interface.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param CategoryService     $categoryService
     * @param TranslatorInterface $translator
     */
    public function __construct(CategoryService $categoryService, TranslatorInterface $translator)
    {
        $this->categoryService = $categoryService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @return Response
     */
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

    /**
     * Show action.
     *
     * @param Category $category
     *
     * @return Response
     */
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

        return $this->render('category/show.html.twig', ['category' => $category]);
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     */
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

    /**
     * Edit action.
     *
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     */
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
            CategoryType::class,
            $category,
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
            ['form' => $form->createView(), 'category' => $category]
        );
    }

    /**
     * Delete action.
     *
     * @param Category $category
     * @param Request  $request
     *
     * @return Response
     */
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

        return $this->render('category/delete.html.twig', ['form' => $form->createView(), 'category' => $category]);
    }
}
