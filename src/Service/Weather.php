<?php

namespace App\Service;

class Weather
{
    public function getAPI()
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

    public function getLater($hours, $laterArray)
    {

      $data = [];

      for ($i = 1; $i <= $hours+2; $i++) {

          $timebyunix = gmdate('H:i', $laterArray->hourly[$i]->dt); // Получение и конвектирование дня недели с Unix
          $tempint = intval($laterArray->hourly[$i]->temp); // Получение и конвектирование температуры

          $format = [];

          $format = [
              "time" => $timebyunix,
              "weather" => $laterArray->hourly[$i]->weather[0]->main,
              "temp" => $tempint,
          ];

          $data[] = $format;
      }

      unset($data[0], $data[1]);

      return $data;
    }

    public function getNow($nowArray)
    {
      $current = [];

      $city = $nowArray->name;
      $wind = $nowArray->wind->speed;
      $humidity = $nowArray->main->humidity;
      $weather = $nowArray->weather[0]->main;
      $time = gmdate("D, M d Y", $nowArray->dt);
      $temp = intval($nowArray->main->temp);

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
