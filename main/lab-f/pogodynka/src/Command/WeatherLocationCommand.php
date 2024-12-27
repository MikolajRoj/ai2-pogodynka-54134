<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Gets weather forecast for a given location based on its ID',
)]
class WeatherLocationCommand extends Command
{
    private WeatherUtil $weatherUtil;
    private LocationRepository $locationRepository;

    public function __construct(WeatherUtil $weatherUtil, LocationRepository $locationRepository)
    {
        parent::__construct();
        $this->weatherUtil = $weatherUtil;
        $this->locationRepository = $locationRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Location ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationId = $input->getArgument('id');
        $location = $this->locationRepository->find($locationId);

        if (!$location) {
            $io->error("Can't find the location by given ID: $locationId");
            return Command::FAILURE;
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);
        $io->section("Temperature for the given location: " . $location->getCity());
        if (empty($measurements)) {
            $io->writeln("No weather data for given location.");
        } else {
            foreach ($measurements as $measurement) {
                $io->writeln(sprintf(
                    "%s: %s °C",
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius()
                ));
            }
        }

        return Command::SUCCESS;
    }
}