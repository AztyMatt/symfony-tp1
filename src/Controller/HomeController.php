<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MovieRepository;

class HomeController extends AbstractController
{
   #[Route(path: '/', name: 'home')]
    public function index(
        MovieRepository $movieRepository
    ): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render(view: 'index.html.twig', parameters: [
            'movies' => $movies
        ]);
    }
}
