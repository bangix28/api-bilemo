<?php

namespace App\Controller;

    use App\Entity\Product;
    use App\Repository\ProductRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use FOS\RestBundle\Controller\AbstractFOSRestController;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    use FOS\RestBundle\Controller\Annotations as Rest;
    use Symfony\Contracts\Cache\CacheInterface;


    /**
     * @Route("api/v1/products")
     */
class ProductController extends AbstractFOSRestController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    /**
     * @Rest\Get(
     *     path="/",
     *     name="Product_list"
     *     )
     * @Rest\View()
     */
    public function listProduct(ProductRepository $Product)
    {
        return $Product->findAll();
    }

    /**
     * @Rest\Get(
     *     path="/{id}",
     *     name="Product_detail"
     * )
     * @Rest\View()
     */

    public function detailProduct(Product $Product){
        return $Product;
    }
}
