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
use App\Repository\MediaRepository;
use App\Entity\Movie;
use App\Entity\Serie;

class ListController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/lists', name: 'lists')]
    public function lists(
        PlaylistRepository $playlistRepository,
        Request $request,
        ManagerRegistry $doctrine,
        MediaRepository $mediaRepository
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

        $playlistMedias = $playlist ? $playlist->getPlaylistMedia() : [];
        $movies = $playlistMedias->filter(function ($media) {
            return $media->getMedia() instanceof Movie;
        });
        $series = $playlistMedias->filter(function ($media) {
            return $media->getMedia() instanceof Serie;
        });

        return $this->render('movie/lists.html.twig', [
            'movies' => $movies,
            'series' => $series,
            'selected_playlist_id' => $selectedPlaylistId,
            // 'playlistMedias' => $playlistMedias,
        ]);
    }
}