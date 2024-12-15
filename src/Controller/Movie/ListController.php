<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PlaylistRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Playlist;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class ListController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/lists', name: 'lists')]
    public function lists(
        PlaylistRepository $playlistRepository,
        Request $request,
        ManagerRegistry $doctrine
    ): Response
    {
        $user = $this->getUser();
        $selectedPlaylistId = $request->query->get('playlist');

        if (!$selectedPlaylistId) {
            $firstPlaylist = $playlistRepository->findOneBy(['creator' => $user]);
            $selectedPlaylistId = $firstPlaylist ? $firstPlaylist->getId() : null;
        }

        $playlist = $doctrine->getRepository(Playlist::class)
            ->findOneBy(['id' => $selectedPlaylistId, 'creator' => $user]);

        $movies = $playlist ? $playlist->getPlaylistMedia() : [];

        return $this->render('movie/lists.html.twig', [
            'movies' => $movies,
            'selected_playlist_id' => $selectedPlaylistId,
        ]);
    }

    // #[IsGranted('ROLE_USER')]
    #[Route(path: '/discover', name: 'discover')] // A mettre dans un autre Controller et remplacer le path lists
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