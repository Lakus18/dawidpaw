<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        WeatherUtil $weatherUtil,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
    ): Response {
        if (!in_array($format, ['json', 'csv'], true)) {
            throw new BadRequestHttpException('Format must be json or csv.');
        }

        $measurements = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($twig) {
            $contentType = $format === 'csv' ? 'text/csv; charset=UTF-8' : 'application/json';

            return $this->render('weather_api/index.' . $format . '.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ], new Response(headers: ['Content-Type' => $contentType]));
        }

        if ($format === 'csv') {
            $locationFields = array_map(
                fn (string $value) => strpbrk($value, ",\"\r\n") === false
                    ? $value
                    : '"' . str_replace('"', '""', $value) . '"',
                [$city, $country],
            );
            $lines = ['city,country,date,celsius,fahrenheit'];
            foreach ($measurements as $measurement) {
                $lines[] = implode(',', [
                    ...$locationFields,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius(),
                    $measurement->getFahrenheit(),
                ]);
            }

            return new Response(implode("\r\n", $lines) . "\r\n", Response::HTTP_OK, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn (Measurement $m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getCelsius(),
                'fahrenheit' => $m->getFahrenheit(),
            ], $measurements),
        ]);
    }
}
