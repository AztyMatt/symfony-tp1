<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route(path: '/admin-add-films')]
    public function adminAddFilms(): Response
    {
        return $this->render(view: 'admin/admin-add-films.html.twig');
    }

    #[Route(path: '/admin-films')]
    public function adminFilms(): Response
    {
        return $this->render(view: 'admin/admin-films.html.twig');
    }

    #[Route(path: '/admin-users')]
    public function adminUsers(): Response
    {
        return $this->render(view: 'admin/admin-users.html.twig');
    }

    #[Route(path: '/admin')]
    public function admin(): Response
    {
        return $this->render(view: 'admin/admin.html.twig');
    }
}