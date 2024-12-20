<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\WatchHistoryRepository;
use App\Repository\MediaRepository;
use App\Entity\Movie;
use App\Entity\Serie;

class MovieController extends AbstractController
{
    #[Route(path: '/detail-movie/{title}', name: 'detail_movie')]
    public function detailMovie(
        string $title,
        WatchHistoryRepository $watchHistoryRepository,
        MediaRepository $mediaRepository
    ): Response
    {
        $media = $mediaRepository->findOneBy(['title' => $title]);
        $movie = $media instanceof Movie ? $media : null;
        $user = $this->getUser();

        $numberOfViews = 0;

        if ($user && $media) {
            $watchHistory = $watchHistoryRepository->findOneBy([
                'watcher' => $user,
                'media' => $movie
            ]);

            if ($watchHistory) {
                $numberOfViews = $watchHistory->getNumberOfViews();
            }
        }

        return $this->render('movie/detail_movie.html.twig', [
            'movie' => $movie,
            'numberOfViews' => $numberOfViews
        ]);
    }

    #[Route(path: '/detail-serie/{title}', name: 'detail_serie')]
    public function detailSerie(
        string $title,
        MediaRepository $mediaRepository,
        WatchHistoryRepository $watchHistoryRepository
    ): Response
    {
        $media = $mediaRepository->findOneBy(['title' => $title]);
        $serie = $media instanceof Serie ? $media : null;
        $user = $this->getUser();

        $numberOfViews = 0;

        if ($user && $media) {
            $watchHistory = $watchHistoryRepository->findOneBy([
                'watcher' => $user,
                'media' => $serie
            ]);

            if ($watchHistory) {
                $numberOfViews = $watchHistory->getNumberOfViews();
            }
        }

        return $this->render('movie/detail_serie.html.twig', [
            'serie' => $serie,
            'numberOfViews' => $numberOfViews
        ]);
    }
}
