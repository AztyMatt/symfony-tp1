<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestEmailCommand extends Command
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:test-email')
            ->setDescription('Send a test email');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('no-reply@votresite.com') // adresse expéditrice
            ->to('testuser@example.com')      // adresse destinataire de test
            ->subject('Test Email')
            ->text('Ceci est un e-mail de test envoyé via Mailpit dans Symfony.');

        // Envoi de l'e-mail
        $this->mailer->send($email);

        $output->writeln('E-mail envoyé avec succès !');

        return Command::SUCCESS;
    }
}
