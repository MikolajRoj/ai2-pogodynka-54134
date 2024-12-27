<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:countryandcity',
    description: 'Display weather forecast for a specific city and country code',
)]
class WeatherCountryandcityCommand extends Command
{
    private WeatherUtil $weatherUtil;
    public function __construct(WeatherUtil $weatherUtil)
    {
        parent::__construct();
        $this->weatherUtil = $weatherUtil;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Country code (e.g., "PL" for Poland)')
            ->addArgument('city', InputArgument::REQUIRED, 'City name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument('countryCode');
        $city = $input->getArgument('city');

        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $city);

        if (empty($measurements)) {
            $io->error(sprintf('No weather data found for city "%s" in country "%s".', $city, $countryCode));
            return Command::FAILURE;
        }

        $io->section(sprintf('Temperature for the given location: %s (%s)', $city, $countryCode));
        foreach ($measurements as $measurement) {
            $io->writeln(sprintf(
                "Date: %s, Temperature: %s°C",
                $measurement->getDate()->format('Y-m-d'),
                $measurement->getCelsius()
            ));
        }
        return Command::SUCCESS;
    }
}
