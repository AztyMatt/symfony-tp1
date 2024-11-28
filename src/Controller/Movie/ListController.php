<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

class ListController extends AbstractController
{
    #[Route(path: '/discover', name: 'discover')]
    public function discover(
        CategoryRepository $categoryRepository,
    ): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render(view: 'movie/discover.html.twig', parameters: [
            'categories' => $categories
        ]);
    }
}