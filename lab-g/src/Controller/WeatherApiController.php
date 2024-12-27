<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WeatherUtil;

class WeatherApiController extends AbstractController
{
    private WeatherUtil $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        $this->weatherUtil = $weatherUtil;
    }

    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $country = $request->query->get('country');
        $city = $request->query->get('city');
        $format = strtolower($request->query->get('format', 'json'));
        $twig = filter_var($request->query->get('twig', false), FILTER_VALIDATE_BOOLEAN);

        try {
            $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);
            $formattedMeasurements = array_map(fn($m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),], $measurements);

            if ($twig) {
                if ($format === 'csv') {
                    return $this->render('weather_api/index.csv.twig', [
                        'city' => $city,
                        'country' => $country,
                        'measurements' => $formattedMeasurements,
                    ], new Response('', 200, [
                        'Content-Type' => 'text/plain',
                    ]));
                } else {
                    return $this->render('weather_api/index.json.twig', [
                        'city' => $city,
                        'country' => $country,
                        'measurements' => $formattedMeasurements,
                    ]);
                }
            }

            if ($format === 'csv') {
                $csvData = "city,country,date,celsius,fahrenheit\n";
                foreach ($formattedMeasurements as $m) {
                    $csvData .= sprintf(
                        '%s,%s,%s,%.2f' . "\n",
                        $city,
                        $country,
                        $m['date'],
                        $m['celsius'],
                        $m['fahrenheit']
                    );
                }

                return new Response($csvData, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }

            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => $formattedMeasurements,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
