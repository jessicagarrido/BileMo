<?php

namespace App\Entity;

use OpenApi\Annotations as OA;
use App\Repository\ClientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientsRepository::class)]
#[ORM\Table(name: "clients", uniqueConstraints: [
    new ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", columns: ["email"]),
    new ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_USERNAME", columns: ["username"])
])]

/**
 * @OA\Schema(
 *     schema="Clients",
 *     description="Représente un client avec ses informations et utilisateurs associés"
 * )
 */
class Clients implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @OA\Property(
     *     type="integer",
     *     description="Identifiant unique du client"
     * )
     */
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    /**
     * @OA\Property(
     *     type="string",
     *     format="email",
     *     maxLength=180,
     *     description="Adresse email unique du client"
     * )
     */

    #[Assert\Email(
        message: "Veuillez entrer une adresse mail valide."
    )]
    #[Assert\Length(
        max: 180,
        maxMessage: "La longueur de l'email est trop longue. Elle ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string"),
     *     description="Liste des rôles attribués au client"
     * )
     */
    private array $roles = [];

    #[ORM\Column]
    /**
     * @OA\Property(
     *     type="string",
     *     description="Mot de passe hashé du client"
     * )
     */
    #[Assert\NotBlank(
        message: "Le champ est requis."
    )]
    #[Assert\Length(
        min: 8,
        max: 50,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le mot de passe ne doit pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/",
        message: "Le mot de passe doit contenir au moins une majuscule, un caractère spécial et un chiffre."
    )]
    private ?string $password = null;
    #[ORM\Column(length: 200)]
    /**
     * @OA\Property(
     *     type="string",
     *     maxLength=200,
     *     description="Nom d'utilisateur du client"
     * )
     */
    private ?string $username = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    /**
     * @OA\Property(
     *     type="string",
     *     format="date",
     *     description="Date de création du client (YYYY-MM-DD)"
     * )
     */
    private ?\DateTimeInterface $dateCreate = null;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\OneToMany(targetEntity: Users::class, mappedBy: 'client', orphanRemoval: true)]
    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Users"),
     *     description="Liste des utilisateurs associés à ce client"
     * )
     */
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires ici
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTimeInterface $dateCreate): static
    {
        $this->dateCreate = $dateCreate;
        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setClient($this);
        }
        return $this;
    }

    public function removeUser(Users $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getClient() === $this) {
                $user->setClient(null);
            }
        }
        return $this;
    }
}
