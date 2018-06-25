<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */    
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptive;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $languages;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $links;

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescriptive(): ?string
    {
        return $this->descriptive;
    }

    public function setDescriptive(?string $descriptive): self
    {
        $this->descriptive = $descriptive;

        return $this;
    }

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(?array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getLinks(): ?array
    {
        return $this->links;
    }

    public function setLinks(?array $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "userId" => $this->user->getId(),
            "name" => $this->name,
            "descriptive" => $this->descriptive,
            "languages" =>  $this->languages,
            "links" => $this->links
        ];
    }
}
