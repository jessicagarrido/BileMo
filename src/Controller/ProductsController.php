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
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/mobiles', name: 'api_listMobiles', methods: ['GET'])]

    public function listMobiles(TagAwareCacheInterface $cache, ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        //recover the page
        $page = $request->query->getInt("page", 1);

        $mobilesCache = $cache->get("products" . $page, function (ItemInterface $item) use ($page, $paginator, $productRepository) {
            $item->expiresAfter(3600);
            $item->tag('mobile');

            $datas = $productRepository->findAll();
            return $paginator->paginate($datas, $page, 6);

        });
        $json = $this->serializer->serialize($mobilesCache, 'json');
        return new JsonResponse($json, 200, [], true);

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

    #[Route('/api/mobile/{id}', name: 'api_mobile', methods: ['GET'])]

    /**
     * Return phone details
     * @param $id
     * @param ProductRepository $productRepository
     * @return response
     */
    public function showMobile(CacheInterface $cache, $id, ProductRepository $productRepository, ): JsonResponse
    {
        $mobileCache = $cache->get("product_details" . $id, function (ItemInterface $item) use ($id, $productRepository) {
            $item->expiresAfter(3600);

            //recover one mobile
            return $productRepository->findById($id);
        });
        $json = $this->serializer->serialize($mobileCache, 'json');

        return new JsonResponse($json, 200, [], true);
    }
}