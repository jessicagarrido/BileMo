<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{

   /**
     * @OA\Post(
     *     path="/api/login_check",
     *     summary="Authentification et récupération d'un token JWT",
     *     tags={"Authenticate"},
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     description="L'email du client",
     *                     type="string",
     *                     example="youremail@gmail.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Mot de passe du client",
     *                     type="string",
     *                     format="password",
     *                     example="yourPassword"
     *                 )
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Retourne un token JWT",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzM4NCJ9...")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Identifiants invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Votre email ou mot de passe sont incorrects")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Ressource non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Page non trouvée")
     *         )
     *     )
     * )
     *
     */
    
    #[Route('/api/login_check', name: 'api_login', methods: ['POST'])]
    public function apiLogin(): JsonResponse
    {

    }

}
