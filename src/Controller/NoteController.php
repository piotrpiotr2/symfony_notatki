<?php
/**
 * Note controller
 */

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

/**
 * Class NoteController
 */
#[Route('/notes')]
class NoteController extends AbstractController
{
    /**
     * Note service
     *
     * @var NoteService
     */
    private NoteService $noteService;

    /**
     * Translator interface
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * Constructor
     *
     * @param NoteService $noteService
     * @param TranslatorInterface $translator
     */
    public function __construct(NoteService $noteService, TranslatorInterface $translator)
    {
        $this->noteService = $noteService;
        $this->translator = $translator;
    }

    /**
     * Index action
     *
     * @param Request $request
     * @return Response
     */
    #[Route(
        name: 'note_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $filters = $this->filters($request);
        $page = $request->query->getInt('page', 1);
        $pagination = $this->noteService->getPaginatedList($page, $user, $filters);

        return $this->render(
            'note/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action
     *
     * @param Note $note
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'note_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Note $note): Response
    {
        if ($note->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/show.html.twig', [ 'note' => $note ]);
    }

    /**
     * Create action
     *
     * @param Request $request
     * @return Response
     */
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
            $note->setAuthor($this->getUser());
            $this->noteService->save($note);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action
     *
     * @param Request $request
     * @param Note $note
     * @return Response
     */
    #[Route(
        '/{id}/edit',
        name: 'note_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Note $note): Response
    {
        if ($note->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('note_index');
        }

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

    /**
     * Delete action
     *
     * @param Request $request
     * @param Note $note
     * @return Response
     */
    #[Route('/{id}/delete', name: 'note_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Note $note): Response
    {
        if ($note->getAuthor()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', $this->translator->trans('message.no_permission'));
            return $this->redirectToRoute('note_index');
        }

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

    /**
     * Filters action
     *
     * @param Request $request
     * @return array
     */
    private function filters(Request $request): array
    {
        return [
            'tag_id' => $request->query->getInt('filters_tag_id'),
            'category_id' => $request->query->getInt('filters_category_id')
        ];
    }
}