<?php


namespace yuchanns\toybox\app;


use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use yuchanns\toybox\app\cgroup\Manager;
use yuchanns\toybox\app\cgroup\subsystems\ResourceCfg;

class RunCmd extends Command
{
    private $stdin = ['file', '/dev/null', 'r'];
    private $stdout = ['file', '/dev/null', 'r'];

    protected static string $defaultName = 'run';

    protected function configure()
    {
        $this->addOption('interactive', 'i', InputOption::VALUE_NONE, 'Keep STDIN open even if not attached');
        $this->addOption('tty', 't', InputOption::VALUE_NONE, 'Allocate a pseudo-TTY');
        $this->addOption('memory', 'm', InputOption::VALUE_REQUIRED, 'Memory limit');
        $this->addArgument("cmd", InputArgument::REQUIRED, "Run a command in a running container");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('interactive')) {
            $this->stdin = STDIN;
        }
        if ($input->getOption('tty')) {
            $this->stdout = STDOUT;
        }
        $resCfg = new ResourceCfg(
            $input->getOption('memory')
        );

        $stderr = STDERR;

        $command = $input->getArgument("cmd");

        $cgroupManager = new Manager("toybox-cgroup");
        if (!$cgroupManager->set($resCfg)) {
            die("cgroup sets failed");
        }
        if (!$cgroupManager->apply(posix_getpid())) {
            die("cgroup applys failed");
        }
        pcntl_unshare(CLONE_NEWPID | CLONE_NEWUTS | CLONE_NEWIPC | CLONE_NEWNS | CLONE_NEWNET);
        $process = proc_open(['php', APP_ROOT, 'init', $command], [
            $this->stdin,
            $this->stdout,
            $stderr,
        ], $pipe, null, getenv());
        if (!is_resource($process)) exit(-1);
        pcntl_wait($status);

        return 0;
    }
}