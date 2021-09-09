<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Annotations as OA;


class SecurityController extends AbstractFOSRestController
{

    /**
     * @Rest\Post(
     *     path="/api/login_check",
     *     name="api_login"
     *     )
     * @OA\Response(
     *     response=200,
     *     description="Donne un token Bearer"
     * )
     * @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
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
