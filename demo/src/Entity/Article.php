<?php
namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min : 10,max : 255,minMessage : "Your title namemust be at least 10 characters long",
     maxMessage : "Your first namecannot be longer than 255 characters")]
    private $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min : 10)]
    private $content;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Url()]
    private $image;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeImmutable
    {
        return $this->createdat;
    }

    public function setCreatedat(?\DateTimeImmutable $createdat): static
    {
        $this->createdat = $createdat;

        return $this;
    }
}
