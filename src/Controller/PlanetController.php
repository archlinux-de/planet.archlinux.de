<?php

namespace App\Controller;

use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
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

    /**
     * @param ItemRepository $itemRepository
     * @param FeedRepository $feedRepository
     */
    public function __construct(ItemRepository $itemRepository, FeedRepository $feedRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->feedRepository = $feedRepository;
    }

    /**
     * @Route("/", methods={"GET"})
     * @Cache(smaxage="600")
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
}
