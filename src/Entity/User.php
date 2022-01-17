<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_MANAGER')",
 *              "security_message"="Only admin or manager can view users.",
 *              "normalization_context"={"groups"={"user.list"}},
 *              "enable_max_depth"=true
 *          },
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"user.detail"}},
 *              "enable_max_depth"=true
 *          },
 *          "current"={
 *              "method"="GET",
 *              "path"="api/users/current",
 *              "defaults"={"id"="current"},
 *              "enable_max_depth"=true
 *           },
 *     },
 *     attributes={"pagination_client_enabled"=true}
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={"user.firstName": "exact", "user.lastName": "exact"}
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableTrait;
    use TimestampableTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user.list", "schema", "plan.create", "user.create", "user.detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user.create", "user.admin"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user.create", "user.admin"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user.create"})
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Plan::class, mappedBy="author", orphanRemoval=true)
     * @Groups({"user.create", "user.detail"})
     * @MaxDepth(1)
     */
    private $authors;

    /**
     * @ORM\OneToMany(targetEntity=Plan::class, mappedBy="resolvedBy")
     * @Groups({"user.create", "user.admin"})
     * @MaxDepth(1)
     */
    private $resolvers;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.list", "user.create", "user.detail"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user.list", "user.create", "user.detail"})
     */
    private $lastName;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->resolvers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Plan[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Plan $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthor(Plan $author): self
    {
        if ($this->authors->removeElement($author)) {
            // set the owning side to null (unless already changed)
            if ($author->getAuthor() === $this) {
                $author->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Plan[]
     */
    public function getResolvers(): Collection
    {
        return $this->resolvers;
    }

    public function addResolver(Plan $resolver): self
    {
        if (!$this->resolvers->contains($resolver)) {
            $this->resolvers[] = $resolver;
            $resolver->setResolvedBy($this);
        }

        return $this;
    }

    public function removeResolver(Plan $resolver): self
    {
        if ($this->resolvers->removeElement($resolver)) {
            // set the owning side to null (unless already changed)
            if ($resolver->getResolvedBy() === $this) {
                $resolver->setResolvedBy(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
