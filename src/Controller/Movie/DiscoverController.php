<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;

class DiscoverController extends AbstractController
{
    // #[IsGranted('ROLE_USER')]
    #[Route(path: '/discover', name: 'discover')] // A mettre dans un autre Controller et remplacer le path lists
    public function discover(
        CategoryRepository $categoryRepository,
        MediaRepository $mediaRepository,
        Request $request
    ): Response
    {
        $type = $request->query->get('type', 'movie');
        $categories = $categoryRepository->findAll();
        $mostPopularMedias = $mediaRepository->findMostPopular(3, $type);

        return $this->render(view: 'movie/discover.html.twig', parameters: [
            'categories' => $categories,
            'mostPopularMedias' => $mostPopularMedias,
            'type' => $type,
        ]);
    }
}
