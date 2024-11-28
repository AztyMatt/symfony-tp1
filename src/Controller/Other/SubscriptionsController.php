<?php

declare(strict_types=1);

namespace App\Controller\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SubscriptionRepository;

class SubscriptionsController extends AbstractController
{
    #[Route(path: '/subscriptions', name: 'subscriptions')]
    public function subscriptions(
        subscriptionRepository $subscriptionRepository,
    ): Response
    {
        $subscriptions = $subscriptionRepository->findAll();

        return $this->render(view: 'other/subscriptions.html.twig', parameters: [
            'subscriptions' => $subscriptions
        ]);
    }
}