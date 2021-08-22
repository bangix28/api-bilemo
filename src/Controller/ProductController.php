<?php

namespace App\Controller;

    use App\Entity\Product;
    use App\Repository\ProductRepository;
    use FOS\RestBundle\Controller\AbstractFOSRestController;
    use FOS\RestBundle\Controller\Annotations as Rest;
    use Nelmio\ApiDocBundle\Annotation\Model;
    use Nelmio\ApiDocBundle\Annotation\Security;
    use OpenApi\Annotations as OA;
    use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("api/v1/products")
     */
class ProductController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(
     *     path="/",
     *     name="Product_list"
     *     )
     * @OA\Response(
     *     response=200,
     *     description="List all products",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     *
     * @OA\Tag(name="Products")
     * @Rest\View()
     * @Security(name="Bearer")
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
     * @OA\Response(
     *     response=200,
     *     description="Show details of specified product",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Product not found",
     *
     * )
     *
     * @OA\Tag(name="Products")
     * @Rest\View()
     * @Security(name="Bearer")
     */

    public function detailProduct(Product $Product){
        return $Product;
    }
}
