<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Author
{
    /**
     * @var string
     * @Assert\Length(min="1", max="255")
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @var string|null
     * @Assert\Url()
     * @Assert\Length(max="255")
     *
     * @ORM\Column(nullable=true)
     */
    private $uri;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param string|null $uri
     * @return Author
     */
    public function setUri(?string $uri): Author
    {
        $this->uri = $uri;
        return $this;
    }
}
