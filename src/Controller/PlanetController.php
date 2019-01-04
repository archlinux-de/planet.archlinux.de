<?php

namespace App\Controller;

use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
use App\Service\ItemExporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanetController extends AbstractController
{
    /** @var ItemRepository */
    private $itemRepository;

    /** @var FeedRepository */
    private $feedRepository;

    /** @var ItemExporter */
    private $itemExporter;

    /**
     * @param ItemRepository $itemRepository
     * @param FeedRepository $feedRepository
     * @param ItemExporter $itemExporter
     */
    public function __construct(
        ItemRepository $itemRepository,
        FeedRepository $feedRepository,
        ItemExporter $itemExporter
    ) {
        $this->itemRepository = $itemRepository;
        $this->feedRepository = $feedRepository;
        $this->itemExporter = $itemExporter;
    }

    /**
     * @Route("/", methods={"GET"})
     * @Cache(smaxage="600")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'items' => $this->itemRepository->findLatest(30),
                'feeds' => $this->feedRepository->findLatest()
            ]
        );
    }

    /**
     * @Route("/{_format}.xml", methods={"GET"}, requirements={"_format": "atom|rss"})
     * @Route("/rss20.xml", methods={"GET"}, defaults={"_format"="rss"})
     * @Cache(smaxage="600")
     *
     * @param string $_format
     * @return Response
     */
    public function feedAction(string $_format): Response
    {
        return new Response(
            $this->itemExporter->export($this->itemRepository->findLatest(30), $_format)
        );
    }
}
