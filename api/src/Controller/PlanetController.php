<?php

namespace App\Controller;

use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
use App\Request\PaginationRequest;
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
     * @Route("/rss.xml", methods={"GET"})
     * @Cache(smaxage="600")
     *
     * @return Response
     */
    public function rssFeedAction(): Response
    {
        $response = $this->render(
            'feed.rss.xml.twig',
            ['items' => $this->itemRepository->findLatest(0, 30)]
        );
        $response->headers->set('Content-Type', 'application/rss+xml; charset=UTF-8');
        return $response;
    }

    /**
     * @Route("/atom.xml", methods={"GET"})
     * @Cache(smaxage="600")
     *
     * @return Response
     */
    public function atomFeedAction(): Response
    {
        $response = $this->render(
            'feed.atom.xml.twig',
            ['items' => $this->itemRepository->findLatest(0, 30)]
        );
        $response->headers->set('Content-Type', 'application/atom+xml; charset=UTF-8');
        return $response;
    }

    /**
     * @Route("/api/feeds", methods={"GET"})
     * @Cache(smaxage="600")
     *
     * @param PaginationRequest $paginationRequest
     * @return Response
     */
    public function feedsAction(PaginationRequest $paginationRequest): Response
    {
        return $this->json($this->feedRepository->findLatest(
            $paginationRequest->getOffset(),
            $paginationRequest->getLimit()
        ));
    }

    /**
     * @Route("/api/items", methods={"GET"})
     * @Cache(smaxage="600")
     *
     * @param PaginationRequest $paginationRequest
     * @return Response
     */
    public function itemsAction(PaginationRequest $paginationRequest): Response
    {
        return $this->json($this->itemRepository->findLatest(
            $paginationRequest->getOffset(),
            $paginationRequest->getLimit()
        ));
    }
}
