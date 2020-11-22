<?php

namespace App\Controller;

use App\Service\Weather;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\History;

class AppController extends AbstractController
{

    private $weather;

    public function __construct(Weather $weather)
    {
        $this->weather = $weather;
    }

    public function index()
    {
      $owm = $this->weather->getOpenWeatherMap();

      return $this->render('index.html.twig', [
        'forecast' => $this->weather->forecast(5, $owm),
        'current' => $this->weather->current($owm),
      ]);
    }

    /**
    * @Route("/history", name="history")
    */
    public function history()
    {
      $history = $this->getDoctrine()
          ->getRepository(History::class)
          ->findBy([], ['id' => 'DESC'], 10);

      return $this->render('history.html.twig', [
            'history' => $history,
      ]);
    }

    // console cron:weather method
    // Добавляет данные о погоде (Время, Погодные условия, Температура) в базу данных
    public function save()
    {

      $forecast = $this->weather->getOpenWeatherMap();

      $time = date('H:i', $forecast->current->dt);
      $weather = $forecast->current->weather[0]->main;
      $temp = intval($forecast->current->temp);

      echo 'Time ' . $time . ', weather ' . $weather . ', temp (c) ' . $temp;

      $entityManager = $this->getDoctrine()->getManager();

      $product = new History();
      $product->setTime($time);
      $product->setWeather($weather);
      $product->setTemp($temp);

      $entityManager->persist($product);
      $entityManager->flush();

      return true;
    }

}
