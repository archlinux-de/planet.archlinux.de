<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={@ORM\Index(columns={"last_modified"})})
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item
{
    /**
     * @var string
     * @Assert\Length(min="1", max="255")
     *
     * @ORM\Column()
     */
    private $title;

    /**
     * @var string
     * @Assert\Length(min="1", max="191")
     *
     * @ORM\Id()
     * @ORM\Column(length=191)
     */
    private $publicId;

    /**
     * @var string
     * @Assert\Length(min="3", max="10485760")
     *
     * @ORM\Column(type="text")
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
     * @var Author
     * @Assert\Valid()
     *
     * @ORM\Embedded(class="Author")
     */
    private $author;

    /**
     * @var Feed
     * @Assert\Valid()
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Feed", inversedBy="items", cascade={"all"})
     * @ORM\JoinColumn(name="feed_url", referencedColumnName="url", onDelete="CASCADE")
     */
    private $feed;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Item
     */
    public function setTitle(string $title): Item
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublicId(): string
    {
        return $this->publicId;
    }

    /**
     * @param string $publicId
     * @return Item
     */
    public function setPublicId(string $publicId): Item
    {
        $this->publicId = $publicId;
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
     * @return Item
     */
    public function setDescription(string $description): Item
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
     * @return Item
     */
    public function setLastModified(\DateTime $lastModified): Item
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
     * @return Item
     */
    public function setLink(string $link): Item
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @param Author $author
     * @return Item
     */
    public function setAuthor(Author $author): Item
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Feed
     */
    public function getFeed(): Feed
    {
        return $this->feed;
    }

    /**
     * @param Feed $feed
     * @return Item
     */
    public function setFeed(Feed $feed): Item
    {
        $this->feed = $feed;
        return $this;
    }
}
