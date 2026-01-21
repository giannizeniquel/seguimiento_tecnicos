<?php

declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\AuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtAuthenticator
{
    public function onAuthenticationSuccess(Request $request, UserInterface $user, string $providerKey): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, \Exception $exception): ?Response
    {
        $data = [
            'message' => 'Authentication failed',
            'error' => $exception->getMessage()
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
