<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MediaRepository;
use App\Entity\Movie;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(MediaRepository $mediaRepository): Response
    {
        $medias = $mediaRepository->findMostPopular();
        
        return $this->render(view: 'index.html.twig', parameters: [
            'medias' => $medias
        ]);
    }
}
