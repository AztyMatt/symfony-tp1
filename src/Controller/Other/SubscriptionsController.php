<?php

declare(strict_types=1);

namespace App\Controller\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubscriptionsController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(path: '/subscriptions', name: 'subscriptions')]
    public function subscriptions(
        subscriptionRepository $subscriptionRepository,
    ): Response
    {
        // $user = $this->getUser();

        // $subscription = $user->getCurrentSubscription();
        $subscriptions = $subscriptionRepository->findAll();

        return $this->render(view: 'other/subscriptions.html.twig', parameters: [
            // 'subscription' => $subscription,
            'subscriptions' => $subscriptions
        ]);
    }
}