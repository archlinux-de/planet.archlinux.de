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
     * @var string|null
     * @Assert\Length(max="255")
     *
     * @ORM\Column(nullable=true)
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return Author
     */
    public function setName(?string $name): Author
    {
        $this->name = $name;

        return $this;
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
     *
     * @return Author
     */
    public function setUri(?string $uri): Author
    {
        $this->uri = $uri;

        return $this;
    }
}
