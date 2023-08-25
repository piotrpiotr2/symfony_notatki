<?php
/**
 * Index controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController.
 */
#[Route('/')]
class IndexController extends AbstractController
{
    /**
     * Index action.
     *
     * @return RedirectResponse
     */
    #[Route(
        name: 'empty_index',
        methods: 'GET'
    )]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('note_index');
    }
}
