<?php

namespace AcMarche\Edr\Command;

use AcMarche\Edr\Mailer\InitMailerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

class MailTestCommand extends Command
{
    use InitMailerTrait;

    protected static $defaultName = 'edr:test-mail';

    protected function configure(): void
    {
        $this
            ->setDescription('Test envoie mail')
            ->addArgument('from', InputArgument::REQUIRED, 'Expéditeur')
            ->addArgument('to', InputArgument::REQUIRED, 'Destinataire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $message = new Email();
        $message->subject('Test applicaiton edr');
        $message->from($from);
        $message->to($to);
        $message->text('Coucou, mail de test');

        try {
            $this->sendMail($message);
            $io->success('Le mail a bien été envoyé.');
        } catch (TransportExceptionInterface $transportException) {
            $io->error('Erreur lors de l envoie: '.$transportException->getMessage());
        }

        return Command::SUCCESS;
    }
}
