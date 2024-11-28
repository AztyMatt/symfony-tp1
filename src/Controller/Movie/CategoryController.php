<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    #[Route(path: '/category/{name}', name: 'category')]
    public function category(
        string $name,
        CategoryRepository $categoryRepository,

        // Category $category
    ): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render(view: 'movie/category.html.twig', parameters: [
            'name' => $name,
            'categories' => $categories
        ]);
    }
}