<?php

namespace App\Command;

use App\Service\InvestmentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:initdatabase')]

class InitDataBase extends Command
{
    public function __construct(
            private InvestmentService $investmentService,
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input,$output);

        $io->success('Init DataBase');
        $this->investmentService->initaliseInvestments($io);

        return Command::SUCCESS;
    }

}