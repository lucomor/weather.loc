<?php

// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function index()
    {
      [$jsonForecast, $jsonTodayWeather] = $this->getApiWeatherData();

      return $this->render('index.html.twig', [
        'forecast' => $this->getForecast(5, $jsonForecast),
        'today' => $this->getTodayWeather($jsonTodayWeather),
      ]);
    }

    public function getApiWeatherData()
    {
      $city = 'Kaliningrad';
      $lat = '54.7065';
      $lon = '20.511';
      $apiKey = 'f10a04d59afa3b8b7512273ddeec75d1';

      $ch1 = curl_init();
      $ch2 = curl_init();

      curl_setopt($ch1, CURLOPT_URL, "https://api.openweathermap.org/data/2.5/onecall?lat=".$lat."&lon=".$lon."&lang=en&units=metric&exclude=minutely&appid=".$apiKey);
      curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch2, CURLOPT_URL, "http://api.openweathermap.org/data/2.5/weather?q=".$city."&lang=en&units=metric&appid=".$apiKey);
      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

      $mh = curl_multi_init();

      curl_multi_add_handle($mh, $ch1);
      curl_multi_add_handle($mh, $ch2);

      $running = null;

      do {
          curl_multi_exec($mh, $running);
      } while ($running > 0);

      $forecast = json_decode(curl_multi_getcontent($ch1));
      $today = json_decode(curl_multi_getcontent($ch2));

      curl_multi_remove_handle($mh, $ch1);
      curl_multi_remove_handle($mh, $ch2);
      curl_multi_close($mh);

      return [$forecast, $today];
    }

    public function getForecast($hours, $jsonForecast)
    {

      $data = [];

      for ($i = 1; $i <= $hours+2; $i++) {

          $timebyunix = gmdate('H:i', $jsonForecast->hourly[$i]->dt); // Получение и конвектирование дня недели с Unix
          $tempint = intval($jsonForecast->hourly[$i]->temp); // Получение и конвектирование температуры

          $format = [];

          $format = [
              "time" => $timebyunix,
              "weather" => $jsonForecast->hourly[$i]->weather[0]->main,
              "temp" => $tempint,
          ];

          $data[] = $format;
      }

      unset($data[0], $data[1]);

      return $data;
    }

    public function getTodayWeather($jsonTodayWeather)
    {
      $current = [];

      $city = $jsonTodayWeather->name;
      $wind = $jsonTodayWeather->wind->speed;
      $humidity = $jsonTodayWeather->main->humidity;
      $weather = $jsonTodayWeather->weather[0]->main;
      $time = gmdate("D, M d Y", $jsonTodayWeather->dt);
      $temp = intval($jsonTodayWeather->main->temp);

      $current = [
          "city" => $city,
          "wind" => $wind,
          "humidity" => $humidity,
          "weather" => $weather,
          "time" => $time,
          "temp" => $temp
      ];

      return $current;

    }

}
