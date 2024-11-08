<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route(path: '/detail_serie')]
    public function register(): Response
    {
        return $this->render(view: 'movie/detail_serie.html.twig');
    }

    #[Route(path: '/detail')]
    public function detail(): Response
    {
        return $this->render(view: 'movie/detail.html.twig');
    }
}