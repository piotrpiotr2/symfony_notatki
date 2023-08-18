<?php
/**
 * Tag controller.
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\TagService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TagController.
 */
#[Route('/tags')]
class TagController extends AbstractController
{
    private TagService $tagService;

    private TranslatorInterface $translator;

    public function __construct(TagService $tagService, TranslatorInterface $translator)
    {
        $this->tagService = $tagService;
        $this->translator = $translator;
    }

    #[Route(
        name: 'tag_index',
        methods: 'GET'
    )]
    public function index(): Response
    {
        $tags = $this->tagService->getAll();

        return $this->render(
            'tag/index.html.twig',
            ['tags' => $tags]
        );
    }

    #[Route(
        '/{id}',
        name: 'tag_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', ['tag' => $tag]);
    }

    #[Route(
        '/create',
        name: 'tag_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(
        '/{id}/edit',
        name: 'tag_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag, ['method' => 'PUT', 'action' => $this->generateUrl('tag_edit', ['id' => $tag->getId()]),]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));
            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/edit.html.twig', [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }

    #[Route(
        '/{id}/delete',
        name: 'tag_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    public function delete(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(FormType::class, $tag, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('tag_delete', ['id' => $tag->getId()]),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));
            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/delete.html.twig', [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }
}