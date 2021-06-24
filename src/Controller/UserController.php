<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;


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
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Post(
     *     path="/v1/user_register",
     *     name="User_create"
     * )
     * @ParamConverter("user",converter="fos_rest.request_body")
     * @Rest\View(statusCode=201)
     */
    public function api_register(Request $request,User $user)
    {
        $data = $this->serializer->deserialize($request->getContent(),'array','json');
        $customer = $this->manager->getRepository(Customer::class)->findOneBy(array('id'=> $data['id_customer']));
        $token = $request->headers->get('Authorization');
        if($token === $customer->getToken() ) {
            $user->setCustomer($customer);
            $hash_pass = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash_pass);
            $error = $this->validator->validate($user);
            if (count($error)) {
                return $this->view($error, Response::HTTP_BAD_REQUEST);
            }
            $this->manager->persist($user);
            $this->manager->flush();
            return $this->view($user, Response::HTTP_CREATED, ['Location' => $this->generateUrl('User_detail', ['User' => $user->getId()])]);
        }
        return $this->view('wrong token', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get(
     *     path="/users/{User}",
     *     name="User_detail"
     * )
     * @Rest\View()
     */
    public function detailUser(User $user){
        return $user;
    }
}
