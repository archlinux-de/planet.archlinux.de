<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\FeedRepository")
 */
class Feed
{
    /**
     * @var string
     * @Assert\Url()
     * @Assert\Length(max="191")
     *
     * @ORM\Id
     * @ORM\Column(length=191)
     */
    private $url;

    /**
     * @var Collection
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="feed", cascade={"all"})
     */
    private $items;

    /**
     * @var string
     * @Assert\Length(min="1", max="255")
     *
     * @ORM\Column()
     */
    private $title;

    /**
     * @var string|null
     * @Assert\Length(max="255")
     *
     * @ORM\Column(nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $lastModified;

    /**
     * @var string
     * @Assert\Url()
     * @Assert\Length(max="255")
     *
     * @ORM\Column()
     */
    private $link;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->items = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Feed
     */
    public function setDescription(?string $description): Feed
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param Item $item
     * @return Feed
     */
    public function addItem(Item $item): Feed
    {
        $this->items->add($item);
        $item->setFeed($this);
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Feed
     */
    public function setTitle(string $title): Feed
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    /**
     * @param \DateTime $lastModified
     * @return Feed
     */
    public function setLastModified(\DateTime $lastModified): Feed
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Feed
     */
    public function setLink(string $link): Feed
    {
        $this->link = $link;
        return $this;
    }
}
