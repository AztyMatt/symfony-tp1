<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;
use App\Entity\Movie;

class CategoryController extends AbstractController
{
    #[Route(path: '/category/{name}', name: 'category')]
    public function category(
        string $name,
        CategoryRepository $categoryRepository,
    ): Response
    {
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->findOneBy(['label' => $name]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $movies = $category->getMedia()->filter(function ($media) {
            return $media instanceof Movie;
        });

        return $this->render('movie/category.html.twig', [
            'name' => $name,
            'categories' => $categories,
            'category' => $category,
            'movies' => $movies
        ]);
    }
}
