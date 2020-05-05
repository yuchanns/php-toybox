<?php


namespace yuchanns\toybox\app;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCmd extends Command
{
    protected static string $defaultName = 'init';

    protected function configure()
    {
        $this->addArgument("cmd", InputArgument::REQUIRED, "command");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getArgument("cmd");
        system("mount --make-rprivate /");

        system("mount -t proc proc /proc -o noexec,nosuid,nodev");

        pcntl_exec($command, [], getenv());
    }
}