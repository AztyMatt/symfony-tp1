<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/admin/add-films', name: 'admin.add_films')]
    public function adminAddFilms(): Response
    {
        return $this->render(view: 'admin/add_films.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/admin/films', name: 'admin.films')]
    public function adminFilms(): Response
    {
        return $this->render(view: 'admin/films.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/admin/users', name: 'admin.users')]
    public function adminUsers(): Response
    {
        return $this->render(view: 'admin/users.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/admin/upload', name: 'admin.upload')]
    public function upload(): Response
    {
        return $this->render(view: 'admin/upload.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render(view: 'admin/admin.html.twig');
    }
}