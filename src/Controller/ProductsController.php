<?php

namespace App\Controller;

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

    #[Route('/listMobiles', name: 'api_listMobiles', methods: ['GET'])]

    /**
     * Return a list of phones ressource
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return response
     */

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