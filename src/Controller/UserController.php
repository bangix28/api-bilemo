<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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

    private $serializer;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->encoder = $encoder;
    }

    /**
     * @Rest\Post(
     *     path="/v1/customer/{id}/user_register",
     *     name="User_create"
     * )
     * @ParamConverter("user",converter="fos_rest.request_body")
     * @Rest\View(statusCode=201, serializerGroups={"details"})
     */
    public function api_register(Customer $customer, Request $request, User $user, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
            $user->setCustomer($customer);
            $hash_pass = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash_pass);
            $user->setCustomer($customer);
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
     * @Rest\Get(
     *     path="v1/customer/{customer}/user/{user}",
     *     name="user_detail"
     * )
     * @Rest\View(statusCode=201, serializerGroups={"details"})
     */
    public function detailUser(Customer $customer, User $user, Request $request)
    {
        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
            return $this->view($user, Response::HTTP_ACCEPTED);
        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }


    /**
     * @Rest\Get(
     *     path="v1/customer/{customer}/users",
     *     name="list_user"
     * )
     * @Rest\View(serializerGroups={"details"})
     */
    public function ListUser(Customer $customer,UserRepository $userRepository,Request $request)
    {
        $token = $request->headers->get('Authorization');
        if ($token === $customer->getToken()) {
        return $this->view(array(
            "users" => $customer->getUsers(),
            "societe" => $customer
        ));
        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }
}
