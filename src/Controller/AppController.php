<?php

namespace App\Controller;

use App\Service\Weather;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function index(Weather $weather)
    {
      [$later, $now] = $weather->getAPI();

      return $this->render('index.html.twig', [
        'later' => $weather->getLater(5, $later),
        'now' => $weather->getNow($now),
      ]);
    }

}
