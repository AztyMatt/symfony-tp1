<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route(path: '/confirm', name: 'confirm')]
    public function confirm(): Response
    {
        return $this->render(view: 'auth/confirm.html.twig');
    }

    #[Route(path: '/forgot', name: 'forgot')]
    public function forgot(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            // Needs to check if reset token is already set !
            $email = $request->get('_email');
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('forgot');
            } else {
                // Generate a reset token
                $resetToken = Uuid::v4();
                $user->setResetToken($resetToken);
                $entityManager->persist($user);
                $entityManager->flush();

                // Create the email
                $resetUrl = $this->generateUrl('reset', ['token' => $resetToken], 0); // 0 means to ignore the domain name in the URL
                $emailMessage = (new TemplatedEmail())
                    ->from('no-reply@streemi.com')
                    ->to($email)
                    ->subject('Password Reset Request')
                    ->htmlTemplate('email/reset.html.twig')
                    ->context([
                        'resetToken' => $resetToken,
                        '_email' => $email
                    ]);

                // Send the email
                $mailer->send($emailMessage);

                $this->addFlash('success', 'A reset email has been sent.');
                return $this->redirectToRoute('forgot');
            }
        }

        return $this->render(view: 'auth/forgot.html.twig');
    }

    #[Route(path: '/register', name: 'register')]
    public function register(): Response
    {
        return $this->render(view: 'auth/register.html.twig');
    }

    #[Route(path: '/reset/{token}', name: 'reset')]
    public function reset(string $token, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!Uuid::isValid($token)) {
            $this->addFlash('error', 'Invalid reset token.');
            return $this->redirectToRoute('forgot');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Invalid or expired reset token.');
            return $this->redirectToRoute('forgot');
        }

        if ($request->isMethod('POST')) {
            $password = $request->get('password');
            $repeatPassword = $request->get('repeat-password');

            if (empty($password) || empty($repeatPassword)) {
                $this->addFlash('error', 'Password cannot be empty.');
                return $this->redirectToRoute('reset', ['token' => $token]);
            }

            if ($password !== $repeatPassword) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('reset', ['token' => $token]);
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null); // Clear the reset token
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your password has been successfully reset.');
            return $this->redirectToRoute('login');
        }

        return $this->render('auth/reset.html.twig', [
            'token' => $token,
            'email' => $user->getEmail()
        ]);
    }
}