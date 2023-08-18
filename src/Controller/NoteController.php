<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Service\NoteService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/notes')]
class NoteController extends AbstractController
{
    private NoteService $noteService;

    private TranslatorInterface $translator;

    public function __construct(NoteService $noteService, TranslatorInterface $translator)
    {
        $this->noteService = $noteService;
        $this->translator = $translator;
    }

    #[Route(
        name: 'note_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $filters = $this->filters($request);
        $page = $request->query->getInt('page', 1);
        $pagination = $this->noteService->getPaginatedList($page, $filters);

        return $this->render(
            'note/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    #[Route(
        '/{id}',
        name: 'note_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Note $note): Response
    {
        return $this->render('note/show.html.twig', [ 'note' => $note ]);
    }

    #[Route(
        '/create',
        name: 'note_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteService->save($note);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(
        '/{id}/edit',
        name: 'note_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Note $note): Response
    {
        $form = $this->createForm(
            NoteType::class,
            $note,
            ['method' => 'PUT', 'action' => $this->generateUrl('note_edit', ['id' => $note->getId()])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteService->save($note);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('note_index');
        }

        return $this->render(
            'note/edit.html.twig',
            ['form' => $form->createView(), 'note' => $note,]
        );
    }

    #[Route('/{id}/delete', name: 'note_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Note $note): Response
    {
        $form = $this->createForm(FormType::class, $note, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('note_delete', ['id' => $note->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteService->delete($note);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('note_index');
        }

        return $this->render(
            'note/delete.html.twig',
            ['form' => $form->createView(), 'note' => $note,]
        );
    }

    private function filters(Request $request): array
    {
        return [
            'tag_id' => $request->query->getInt('filters_tag_id'),
            'category_id' => $request->query->getInt('filters_category_id')
        ];
    }
}