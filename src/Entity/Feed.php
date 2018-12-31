<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Feed
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\Url()
     * @Assert\Length(max="255")
     *
     * @ORM\Column(unique=true)
     */
    private $url;

    /**
     * @var Item[]
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="feed", cascade={"remove"})
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
     * @var string
     * @Assert\Length(max="255")
     *
     * @ORM\Column()
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
     * @param Item $item
     * @return Feed
     */
    public function addItem(Item $item): Feed
    {
        $this->items->add($item);
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return Item[]
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Feed
     */
    public function setDescription(string $description): Feed
    {
        $this->description = $description;
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
