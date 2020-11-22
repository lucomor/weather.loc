<?php

namespace App\Service;

class Weather
{

    // Получение данных о погоде OpenWeatherMap (https://openweathermap.org/)
    // Укажите в переменные ключ API OpenWeatherMap а так же Координаты (lat, lon) города согласно документации
    // return object Данные прогноза погоды
    public function getOpenWeatherMap()
    {
      $lat = '54.7065';
      $lon = '20.511';
      $apiKey = 'f10a04d59afa3b8b7512273ddeec75d1';

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://api.openweathermap.org/data/2.5/onecall?lat=".$lat."&lon=".$lon."&lang=en&units=metric&exclude=minutely&appid=".$apiKey);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $data = json_decode(curl_exec($ch));
      curl_close($ch);

      return $data;
    }

    // Формирует массив данных ПРОГНОЗА погоды (Время, Погодные условия, Температура)
    // Принимает 2 параметра:
    // - Количество часов которые нужно вывести на странице (1-47)
    // - Объект содержащий данные прогноза погоды openweathermap
    // return array
    public function forecast($hours, $forecast)
    {

      $data = [];

      for ($i = 1; $i <= $hours; $i++) {

          $format = [];

          $format = [
              "time" => date('H:i', $forecast->hourly[$i]->dt),
              "weather" => $forecast->hourly[$i]->weather[0]->main,
              "temp" => intval($forecast->hourly[$i]->temp),
          ];

          $data[] = $format;
      }

      return $data;

    }

    // Формирует массив данных о ТЕКУЩЕЙ погоде (Город, Скорость ветра, Процент влажности, Погодные условия, Дата и время, Температура)
    // Принимает объект содержащий данные прогноза погоды openweathermap
    // return array
    public function current($forecast)
    {
      $current = [];

      $timezone = explode('/', $forecast->timezone);

      $current = [
          "city" => $timezone[1],
          "temp" => intval($forecast->current->temp),
          "wind" => $forecast->current->wind_speed,
          "humidity" => $forecast->current->humidity,
          "weather" => $forecast->current->weather[0]->main,
          "date" => date('D, M d Y', $forecast->current->dt),
          "time" => date('H:i', $forecast->current->dt),
      ];

      return $current;
    }

}
