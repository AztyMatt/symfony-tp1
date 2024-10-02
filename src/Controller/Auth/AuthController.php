<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route(path: '/confirm')]
    public function confirm(): Response
    {
        return $this->render(view: 'auth/confirm.html.twig');
    }

    #[Route(path: '/forgot')]
    public function forgot(): Response
    {
        return $this->render(view: 'auth/forgot.html.twig');
    }

    #[Route(path: '/login')]
    public function login(): Response
    {
        return $this->render(view: 'auth/login.html.twig');
    }

    #[Route(path: '/register')]
    public function register(): Response
    {
        return $this->render(view: 'auth/register.html.twig');
    }

    #[Route(path: '/reset')]
    public function reset(): Response
    {
        return $this->render(view: 'auth/reset.html.twig');
    }
}