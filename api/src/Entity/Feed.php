<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
class Feed
{
    #[ORM\Id]
    #[ORM\Column(length: 191)]
    #[Assert\Url]
    #[Assert\Length(max: 191)]
    private string $url;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(mappedBy: 'feed', targetEntity: Item::class, cascade: ['all'])]
    #[Assert\Valid]
    private Collection $items;

    #[ORM\Column]
    #[Assert\Length(min: 1, max: 255)]
    private string $title;

    #[ORM\Column(nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTime $lastModified;

    #[ORM\Column]
    #[Assert\Url]
    #[Assert\Length(max: 255)]
    private string $link;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->items = new ArrayCollection();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Feed
    {
        $this->description = $description;
        return $this;
    }

    public function addItem(Item $item): Feed
    {
        $this->items->add($item);
        $item->setFeed($this);
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Feed
    {
        $this->title = $title;
        return $this;
    }

    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    public function setLastModified(\DateTime $lastModified): Feed
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): Feed
    {
        $this->link = $link;
        return $this;
    }
}
