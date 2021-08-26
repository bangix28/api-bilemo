<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;


class SecurityController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(
     *     path="/api/login_check",
     *     name="api_login"
     *     )
     * @OA\Response(
     *     response=200,
     *     description="Donne un token Bearer"
     * )
     * @OA\Tag(name="Login")
     */
    public function api_login() {
        $user = $this->GetUser();
        return new JsonResponse([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }


}
