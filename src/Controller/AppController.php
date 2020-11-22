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

      [$later, $now] = $this->weather->getAPI();

      return $this->render('index.html.twig', [
        'later' => $this->weather->getLater(5, $later),
        'now' => $this->weather->getNow($now),
      ]);
    }

    /**
    * @Route("/history", name="history")
    */
    public function history()
    {
      $history = $this->getDoctrine()
          ->getRepository(History::class)
          ->findBy([], ['id' => 'ASC'],5);

      return $this->render('history.html.twig', [
            'history' => $history,
      ]);

    }

    // Метод вызывается консольной командой cron:weather
    // Добавляет данные о погоде (Время, Погодные условия, Температура) в базу данных
    public function addWeather()
    {

      [$later, $now] = $this->weather->getAPI();

      $weather = $now->weather[0]->main;
      $time = date('d.m - H:i');
      $temp = intval($now->main->temp);

      echo 'Time: ' . $time . ' ; Weather: ' . $weather . ' ; Temp (c): ' . $temp;

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
