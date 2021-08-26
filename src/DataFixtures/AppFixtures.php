<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder ){
        $this->encoder = $encoder;
    }
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName('product '.$i);
            $product->setPrice(random_int(10, 100));
            $product->setDescription('Magnifique télephone');
            $manager->persist($product);
        }

        $tokenGenerator =  new UuidV4Generator();
        $customer = new Customer();
        $customer->setEmail('test@test.com')
            ->setName('admin')
            ->setRoles(array('test'));
        $customer->setPassword($this->encoder->encodePassword($customer, "root"));
        $customer->setToken($tokenGenerator->generate($manager, $customer));

        $manager->persist($customer);

        $manager->flush();
    }
}
