<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProductsController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @OA\Get(
     *     path="/listMobiles",
     *     summary="Retourne la liste des mobiles",
     *     tags={"Mobiles"},
     * 
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de la page des produits à afficher",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Retourne la liste des mobiles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Product::class))
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="JWT Token non trouvé ou expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="JWT Token non trouvé ou expiré")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Page non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ressource non trouvée")
     *         )
     *     )
     * )
     */

    #[Route('/listMobiles', name: 'api_listMobiles', methods: ['GET'])]

    public function listMobiles(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //recover the page
        $page = $request->query->getInt("page", 1);
        //recover all mobiles
        $datas = $productRepository->findAll();
        //recover a page with 6 mobiles
        $products = $paginator->paginate($datas, $page, 6);

        $json = $this->serializer->serialize($products, 'json');
        $response = new Response($json, 200, []);

        return $response;
    }

        /**
     * @OA\Get(
     *     path="/mobile/{id}",
     *     summary="Retourne le détail d'un mobile",
     *     tags={"Mobiles"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id du mobile",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Retourne le détail d'un mobile",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Product::class))
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="JWT Token non trouvé ou expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="JWT Token non trouvé ou expiré")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Page non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ressource non trouvée")
     *         )
     *     )
     * )
     */

    #[Route('/mobile/{id}', name: 'api_mobile', methods: ['GET'])]

    /**
     * Return phone details
     * @param $id
     * @param ProductRepository $productRepository
     * @return response
     */
    public function showMobile($id, ProductRepository $productRepository, ): Response
    {
        //recover one mobile
        $product = $productRepository->findById($id);
        $json = $this->serializer->serialize($product, 'json');

        $response = new Response($json, 200, []);

        return $response;
    }
}