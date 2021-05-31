<?php

namespace App\Controller;

    use App\Entity\Product;
    use App\Repository\ProductRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use FOS\RestBundle\Controller\AbstractFOSRestController;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    use FOS\RestBundle\Controller\Annotations as Rest;


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
     *     path="/products",
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
     *     path="/products/{product}",
     *     name="Product_detail"
     * )
     * @Rest\View()
     */

    public function detailProduct(Product $Product){
        return $Product;
    }
    /**
     * @Rest\Post(
     *     path="/products",
     *     name="Product_create"
     * )
     * @ParamConverter("Product",converter="fos_rest.request_body")
     * @Rest\View(statusCode=201)
     */
    public function createProduct(Product $Product)
    {
        $error = $this->validator->validate($Product);
        if (count($error))
        {
            return $this->view($error, Response::HTTP_BAD_REQUEST);
        }
        $this->manager->persist($Product);
        $this->manager->flush();
        return $this->view($Product, Response::HTTP_CREATED, ['Location' => $this->generateUrl('Product_detail', ['product' => $Product->getId()])]);
    }

    /**
     * @Rest\Delete(
     *     path="/products/{Product}/delete",
     *     name="Product_delete"
     * )
     * @Rest\View(statusCode=201)
     */
    public function deleteProduct(Product $Product)
    {
        $this->manager->remove($Product);
        $this->manager->flush();
    }
}
