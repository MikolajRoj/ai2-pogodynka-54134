<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country?}', name: 'app_weather')]
    public function city(string $city, ?string $country, LocationRepository $locationRepository, MeasurementRepository $measurementRepository): Response
    {
        $location = $locationRepository->findByCityAndCountry($city, $country);
        $measurements = $measurementRepository->findByLocation($location);

        // Renderuj widok z lokalizacją i prognozami
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}