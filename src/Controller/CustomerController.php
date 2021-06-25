<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Uid\Uuid;



class CustomerController extends AbstractFOSRestController
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

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->encoder = $encoder;
    }

    /**
     * @Rest\Get(
     *     path="api/customers",
     *     name="customer_list"
     *     )
     * @Rest\View()
     */
    public function listCustomer(CustomerRepository $customer)
    {
        return $customer->findAll();
    }

    /**
     * @Rest\Get(
     *     path="/customers/{customer}",
     *     name="customer_detail"
     * )
     * @Rest\View()
     */

    public function detailCustomer(Customer $customer){
        return $customer;
    }

    /**
     * @Rest\Post(
     *     path="/customer",
     *     name="customer_create"
     * )
     * @ParamConverter("customer",converter="fos_rest.request_body")
     * @Rest\View(statusCode=201)
     * @throws \Exception
     */
    public function createCustomer(Customer $customer)
    {
        $test = new UuidV4Generator();
        $customer->setToken($test->generate($this->manager,$customer));
        $hash_pass = $this->encoder->encodePassword($customer, $customer->getPassword());
        $customer->setPassword($hash_pass);
        $error = $this->validator->validate($customer);
        if (count($error)) {
            return $this->view($error, Response::HTTP_BAD_REQUEST);
        }
        $this->manager->persist($customer);
        $this->manager->flush();
        return $this->view($customer, Response::HTTP_CREATED, ['Location' => $this->generateUrl('customer_detail', ['customer' => $customer->getId()])]);

    }
    /**
     * @Rest\Delete(
     *     path="/customers/{customer}/delete",
     *     name="customer_delete"
     * )
     * @Rest\View(statusCode=201)
     */
    public function deleteCustomer(Customer $customer)
    {
        $this->manager->remove($customer);
        $this->manager->flush();
    }
}
