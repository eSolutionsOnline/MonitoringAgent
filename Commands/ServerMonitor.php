<?php


namespace Commands;

use Commands\Traits\OutputTrait;
use Commands\Traits\SendDataTrait;
use Commands\Traits\StatisticalTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ServerMonitor extends Command
{
    use StatisticalTrait, OutputTrait, SendDataTrait;

    public function configure(): void
    {
        $this->setName('monitor')
            ->setDescription("Monitor the server")
            ->addOption("send", "s", null, "Send the monitor data");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $version = env('VERSION', 'v0.0.1');

        //php artisan monitor -qs [for LIVE] q=quiet mode and s for send data.

        $output->writeln("<question>Server Monitor {$version}</question>");

        $stats = $this->Statistics();

        $this->sendData($input, $output, $stats);

        $this->tabulate($input, $output, $stats);

        return 1;
    }
}
