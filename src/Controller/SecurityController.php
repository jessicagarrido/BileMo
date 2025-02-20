<?php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    private $jwtManager;
    private $passwordHasher;

    public function __construct(JWTTokenManagerInterface $jwtManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/api/login', name: 'login_check', methods: ['POST'])]

    public function apiLogin(Request $request): JsonResponse
    {
        // Récupérer les données JSON envoyées (email et mot de passe)
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email et mot de passe sont requis.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Chercher l'utilisateur par email dans la base de données
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            // Si l'utilisateur n'existe pas, on lance une exception d'authentification
            throw new AuthenticationException('Email ou mot de passe invalide.');
        }

        // Vérifier si le mot de passe est correct
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            // Si le mot de passe est incorrect, on lance une exception d'authentification
            throw new AuthenticationException('Email ou mot de passe invalide.');
        }

        // Si l'email et le mot de passe sont valides, générer un JWT
        $jwt = $this->jwtManager->create($user);

        // Retourner le token JWT
        return new JsonResponse(['token' => $jwt]);
    }

}
