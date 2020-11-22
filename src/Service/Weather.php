<?php

namespace App\Service;

class Weather
{

    // Получение данных о погоде API OpenWeatherMap
    // Укажите в переменные ключ API OpenWeatherMap а так же Город, Координаты города согласно документации OpenWeatherMap
    // Возвращает два объекта:
    // -- Прогноз погоды
    // -- Текущая погода
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

      $later = json_decode(curl_multi_getcontent($ch1));
      $now = json_decode(curl_multi_getcontent($ch2));

      curl_multi_remove_handle($mh, $ch1);
      curl_multi_remove_handle($mh, $ch2);
      curl_multi_close($mh);

      return [$later, $now];
    }

    // Формирует массив данных ПРОГНОЗА погоды (Время, Погодные условия, Температура)
    // Принимает 2 параметра:
    // -- Количество часов которые нужно вывести на странице (1-47)
    // -- Объект содержащий данные прогноза погоды openweathermap
    // return array
    public function getLater($hours, $laterArray)
    {

      $data = [];

      for ($i = 1; $i <= $hours; $i++) {

          $timebyunix = date('H:i', $laterArray->hourly[$i]->dt); // Получение и конвектирование дня недели с Unix
          $tempint = intval($laterArray->hourly[$i]->temp); // Получение и конвектирование температуры

          $format = [];

          $format = [
              "time" => $timebyunix,
              "weather" => $laterArray->hourly[$i]->weather[0]->main,
              "temp" => $tempint,
          ];

          $data[] = $format;
      }

      return $data;
    }

    // Формирует массив данных о ТЕКУЩЕЙ погоде (Город, Скорость ветра, Процент влажности, Погодные условия, Дата и время, Температура)
    // Принимает 1 параметр:
    // -- Объект содержащий данные о текущей погоде openweathermap
    // return array
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
