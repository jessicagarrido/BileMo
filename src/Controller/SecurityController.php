<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    /**
    * #[Route('/api/login_check', name: 'api_login')]
    * @return JsonResponse
    */

    public function apiLogin(): JsonResponse
    {

    }

}
