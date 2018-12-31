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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\Length(min="1", max="255")
     *
     * @ORM\Column()
     */
    private $title;

    /**
     * @var string
     * @Assert\Length(min="1", max="255")
     *
     * @ORM\Column()
     */
    private $publicId;

    /**
     * @var string
     * @Assert\Length(min="3", max="16384")
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
     * @ORM\ManyToOne(targetEntity="Feed", inversedBy="items")
     */
    private $feed;

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
}
