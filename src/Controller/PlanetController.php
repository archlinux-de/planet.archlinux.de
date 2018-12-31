<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanetController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Cache(smaxage="600")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('index.html.twig');
    }
}
