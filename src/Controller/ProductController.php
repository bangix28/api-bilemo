<?php

namespace App\Controller;

    use App\Entity\Product;
    use App\Repository\ProductRepository;
    use FOS\RestBundle\Controller\AbstractFOSRestController;
    use FOS\RestBundle\Controller\Annotations as Rest;
    use Nelmio\ApiDocBundle\Annotation\Model;
    use Nelmio\ApiDocBundle\Annotation\Security;
    use OpenApi\Annotations as OA;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Knp\Component\Pager\PaginatorInterface;


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
    public function listProduct(ProductRepository $Product,PaginatorInterface $paginator ,Request $request)
    {
        $page = $paginator->paginate(
            $Product->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $page->result();
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
