<?php

namespace App\Entity;

use OpenApi\Annotations as OA;
use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;


#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\Table(name: "users", uniqueConstraints: [
    new ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", columns: ["email"]),
    new ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_USERNAME", columns: ["username"])
])]
/**
 * @OA\Schema(
 *     schema="Users",
 *     description="Représente un utilisateur avec ses informations personnelles."
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href=@Hateoas\Route(
 *          "api_user",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *      )
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href=@Hateoas\Route(
 *          "api_listUsers",
 *          absolute=true
 *      )
 * )
 * @Hateoas\Relation(
 *      "addUser",
 *      href=@Hateoas\Route(
 *          "api_addUser",
 *          absolute=true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href=@Hateoas\Route(
 *          "api_deleteUser",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute=true
 *      )
 * )
 * @Serializer\ExclusionPolicy("ALL")
 */
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Expose]

    /**
     * @OA\Property(type="integer", description="Identifiant unique de l'utilisateur")
     */
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    /**
     * @OA\Property(type="string", format="email", maxLength=180, description="Adresse email unique de l'utilisateur")
     */
    #[Assert\NotBlank(message: "Le champ email est requis.")]
    #[Assert\Email(message: "Veuillez entrer une adresse email valide.")]
    #[Assert\Length(max: 180, maxMessage: "L'email ne doit pas dépasser {{ limit }} caractères.")]
    #[Serializer\Expose]

    private ?string $email = null;

    #[ORM\Column]
    /**
     * @OA\Property(type="array", @OA\Items(type="string"), description="Rôles attribués à l'utilisateur")
     */
    private array $roles = [];

    #[ORM\Column]
    /**
     * @OA\Property(type="string", description="Mot de passe hashé de l'utilisateur")
     */
    #[Assert\NotBlank(message: "Le champ mot de passe est requis.")]
    #[Assert\Length(min: 8, max: 50, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.", maxMessage: "Le mot de passe ne doit pas dépasser {{ limit }} caractères.")]
    #[Assert\Regex(pattern: "/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/", message: "Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial.")]

    private ?string $password = null;

    #[ORM\Column(length: 200, unique: true)]
    /**
     * @OA\Property(type="string", maxLength=200, description="Nom d'utilisateur unique")
     */
    #[Assert\NotBlank(message: "Le champ username est requis.")]
    #[Assert\Length(max: 200, maxMessage: "Le nom d'utilisateur ne doit pas dépasser {{ limit }} caractères.")]
    #[Serializer\Expose]

    private ?string $username = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    /**
     * @OA\Property(type="string", format="date", description="Date de création de l'utilisateur")
     */
    #[Serializer\Expose]

    private ?\DateTimeInterface $dateCreate = null;

    #[ORM\Column(length: 200)]
    /**
     * @OA\Property(type="string", maxLength=200, description="Prénom de l'utilisateur")
     */
    #[Assert\NotBlank(message: "Le prénom est requis.")]
    #[Assert\Length(max: 200, maxMessage: "Le prénom ne doit pas dépasser {{ limit }} caractères.")]
    #[Serializer\Expose]

    private ?string $firstname = null;

    #[ORM\Column(length: 200)]
    /**
     * @OA\Property(type="string", maxLength=200, description="Nom de famille de l'utilisateur")
     */
    #[Assert\NotBlank(message: "Le nom de famille est requis.")]
    #[Assert\Length(max: 200, maxMessage: "Le nom de famille ne doit pas dépasser {{ limit }} caractères.")]
    #[Serializer\Expose]

    private ?string $lastname = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @OA\Property(ref="#/components/schemas/Clients", description="Client auquel l'utilisateur est rattaché")
     */
    private ?Clients $client = null;

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

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

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
        // Clear temporary sensitive data if necessary
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): static
    {
        $this->client = $client;
        return $this;
    }
}
