<?php

namespace App\Command;

use App\Controller\AppController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherCommand extends Command
{

    private $weather;

    public function __construct(AppController $weather)
    {
        $this->weather = $weather;
        parent::__construct();
    }

    protected function configure()
    {
      $this
      ->setName('cron:weather')
      ->setDescription('Adding the current weather to the database')
      ->setHelp('This command will add the current weather to the database table');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

      $output->writeln([
        'cron init',
        '',
      ]);

      $this->weather->addWeather();

      $output->writeln([
        '',
        '<info>:)</info>',
      ]);

      return Command::SUCCESS;
    }
}
