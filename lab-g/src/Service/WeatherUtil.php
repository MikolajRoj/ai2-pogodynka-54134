<?php

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;

class WeatherUtil
{
    private MeasurementRepository $measurementRepository;
    private LocationRepository $locationRepository;

    public function __construct(MeasurementRepository $measurementRepository, LocationRepository $locationRepository)
    {
        $this->measurementRepository = $measurementRepository;
        $this->locationRepository = $locationRepository;
    }

    /**
     *
     * @param Location $location
     * @return Measurement[]
     */
    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurementRepository->findBy(
            ['location' => $location],
            ['date' => 'ASC']
        );
    }

    /**
     *
     * @param string $countryCode
     * @param string $cityName
     * @return Measurement[]
     */
    public function getWeatherForCountryAndCity(string $countryCode, string $cityName): array
    {
        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'city' => $cityName
        ]);

        if ($location === null) {
            throw new \InvalidArgumentException("Location not found for country: $countryCode and city: $cityName");
        }
        return $this->getWeatherForLocation($location);
    }
}