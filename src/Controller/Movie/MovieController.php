<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MovieRepository;

class MovieController extends AbstractController
{
    #[Route(path: '/detail-movie/{title}', name: 'detail_movie')]
    public function detailMovie(
        string $title,
        MovieRepository $movieRepository,
    ): Response
    {
        $movie = $movieRepository->findOneBy(['title' => $title]);

        return $this->render(view: 'movie/detail_movie.html.twig', parameters: [
            'movie' => $movie
        ]);
    }

    #[Route(path: '/detail-serie', name: 'detail_serie')]
    public function detailSerie(): Response
    {
        return $this->render(view: 'movie/detail_serie.html.twig');
    }
}