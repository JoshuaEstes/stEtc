<?php
namespace JoshuaEstes\stEtc\Hosts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class AddCommand extends Command
{
  protected function configure()
  {
    $this
      ->setName('etc:hosts:add')
      ->setDescription('add an entry in your hosts file')
      ->addArgument('ip', InputArgument::OPTIONAL, "IP Address, example 127.0.0.1")
      ->addArgument('hostname', InputArgument::OPTIONAL, "Hostname(s) seperated by spaces");
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $ip = $input->getArgument('ip');

    while(!$ip)
    {
      // ask for ip address
      $ip = $this->getDialog()->ask($output, '<question>Enter IP Address (default: 127.0.0.1)</question> ', '127.0.0.1');
    }

    $hostname = $input->getArgument('hostname');
    while(!$hostname)
    {
      // ask for hostname
      $hostname = $this->getDialog()->ask($output, '<question>Enter hostname (example: example.local)</question> ');
    }

    // @TODO make this edit hosts file on any platform, ie Linux, Mac, Windows
    $process = new Process(sprintf('echo "%s %s" | sudo tee -a /etc/hosts >/dev/null', $ip, $hostname));
    $process->run(function($type, $buffer) use($output)
      {
        $output->writeln($buffer);
      }
    );
  }


  /**
   *
   * @return Symfony\Component\Console\Helper\DialogHelper
   */
  protected function getDialog() {
    return $this->getHelperSet()->get('dialog');
  }
}
