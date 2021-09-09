<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class UserController
 * @package App\Controller
 * @Security(name="ApiKeyAuth")
 */
class UserController extends AbstractFOSRestController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    private $encoder;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->encoder = $encoder;
    }

    /**
     * @Rest\Post(
     *     path="v1/customers/{customer}/users",
     *     name="user_create"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Create a new User",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"details"}))
     *     )
     * )
     * @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserCreate")
     *      ),
     * @ParamConverter("user",converter="fos_rest.request_body")
     * @Rest\View(statusCode=201, serializerGroups={"details"})
     * @OA\Tag(name="Customers")
     */
    public function createUser(Customer $customer, Request $request, User $user, ValidatorInterface $validator)
    {
        $violations = $validator->validate($customer);
        if (count($violations) > 0) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }
        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
            $user->setCustomer($customer);
            $hash_pass = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash_pass);
            $user->setRoles(array("USER"));
            $error = $this->validator->validate($user);
            if (count($error)) {
                return $this->view($error, Response::HTTP_BAD_REQUEST);
            }
            $this->manager->persist($user);
            $this->manager->flush();

            return $this->view($user, Response::HTTP_CREATED,
                ['Location' => $this->generateUrl('user_detail', [
                        'customer' => $customer->getId(),
                        'user' => $user->getId()]
                )]);
        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete (
     *     path="v1/customers/{customer}/users/{user}",
     *     name="user_delete"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Delete User",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"details"}))
     *     )
     * )
     *
     * @OA\Tag(name="Customers")
     * @Rest\View(statusCode=204)
     */
    public function deleteUser(Customer $customer, Request $request, User $user)
    {
        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
            $this->manager->remove($user);
            $this->manager->flush();
            return $this->view('User deleted', Response::HTTP_OK);

        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get(
     *     path="v1/customers/{customer}/user/{user}",
     *     name="user_detail"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Fetch details of user link to customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"details"}))
     *     )
     * )
     *
     * @OA\Tag(name="Customers")
     * @Rest\View(statusCode=201, serializerGroups={"details"})
     */
    public function detailUser( Customer $customer, User $user, Request $request)
    {
        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
            return $this->view($user, Response::HTTP_ACCEPTED);
        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }


    /**
     * @Rest\Get(
     *     path="v1/customers/{customer}/users",
     *     name="list_user"
     * )
     * @OA\Response(
     *     response=200,
     *     description="List User of customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="le numéro de la page",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Customers")
     * @Rest\View(serializerGroups={"details"})
     */
    public function ListUser(Customer $customer, PaginatorInterface $paginator, SerializerInterface $serializer, Request $request)
    {
        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
            $pagination = $paginator->paginate(
                $customer->getUsers(),
                $request->query->getInt('page', 1),
                10
            );
            $pagerResult = [
                'count' => $pagination->getTotalItemCount(),
                'items' => $pagination->getItems(),
                'limit' => $pagination->getItemNumberPerPage(),
                'current' => $pagination->getCurrentPageNumber()
            ];
            return $this->view($pagerResult, RESPONSE::HTTP_OK);

        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }
}
