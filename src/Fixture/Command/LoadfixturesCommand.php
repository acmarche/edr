<?php

namespace AcMarche\Edr\Fixture\Command;

use AcMarche\Edr\Fixture\FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class LoadfixturesCommand extends Command
{
    protected static $defaultName = 'edr:load-fixtures';

    public function __construct(
        private readonly FixtureLoader $fixtureLoader,
        private readonly EntityManagerInterface $entityManager,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Chargment des fixtures')
            ->addOption('purge', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $purge = $input->getOption('purge');

        if (null === $purge) {
            $confirmationQuestion = new ConfirmationQuestion("Voulez vous vider la base de données ? [y,N] \n", false);
            $purge = $helper->ask($input, $output, $confirmationQuestion);
        }

        if ($purge) {
            $ormPurger = new ORMPurger($this->entityManager);
            $ormPurger->setPurgeMode(1);
            $ormPurger->purge();
            $io = new SymfonyStyle($input, $output);
            $io->info('Bd purgée');
        }

        $this->fixtureLoader->load();

        return Command::SUCCESS;
    }
}
