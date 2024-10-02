<?php

declare(strict_types=1);

namespace App\Controller\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionsController extends AbstractController
{
    #[Route(path: '/subscriptions')]
    public function subscriptions(): Response
    {
        return $this->render(view: 'other/subscriptions.html.twig');
    }
}