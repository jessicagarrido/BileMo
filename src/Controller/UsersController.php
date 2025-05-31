<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UsersController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var SymfonySerializer
     */
    private $deserializer;

    public function __construct(SerializerInterface $serializer, SymfonySerializer $deserializer, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->serializer = $serializer;
        $this->deserializer = $deserializer;
        $this->passwordHasher = $userPasswordHasher;
    }

    /**
     * @OA\Get(
     *     path="/api/listUsers",
     *     summary="Retourne la liste des utilisateurs",
     *     tags={"Users"},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de la page des utilisateurs à afficher",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Retourne la liste des utilisateurs",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref=@Model(type=Users::class))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="JWT Token non trouvé ou expiré",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="JWT Token non trouvé ou expiré")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Page non trouvée",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Ressource non trouvée")
     *         )
     *     )
     * )
     */
    #[Route('/api/users', name: 'api_listUsers', methods: ['GET'])]
    public function listUsers(TagAwareCacheInterface $cache, UsersRepository $userRepository, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $client = $this->getUser();
        $idClient = $client->getId();

        $page = $request->query->getInt('page', 1);

        $usersCache = $cache->get('users'.$page, function (ItemInterface $item) use ($page, $idClient, $paginator, $userRepository) {
            $item->expiresAfter(3600);
            $item->tag('user');

            $datas = $userRepository->findByClient($idClient);

            return $paginator->paginate($datas, $page, 5);
        });
        $json = $this->serializer->serialize($usersCache, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Retourne le détail d'un utilisateur",
     *     tags={"User"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id de l'user",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Retourne le détail d'un user",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref=@Model(type=Users::class))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="JWT Token non trouvé ou expiré",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="JWT Token non trouvé ou expiré")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Page non trouvée",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Ressource non trouvée")
     *         )
     *     )
     * )
     */
    #[Route('/api/user/{id}', name: 'api_user', methods: ['GET'])]
    public function showUser(CacheInterface $cache, $id, UsersRepository $usersRepository): JsonResponse
    {
        $clientConnected = $this->getUser();
        $idClientConnected = $clientConnected->getId();

        $userCache = $cache->get('user_details'.$id, function (ItemInterface $item) use ($id, $idClientConnected, $usersRepository) {
            $item->expiresAfter(3600);

            $user = $usersRepository->find($id);

            $userClient = $user->getClient();
            $idUserClient = $userClient->getId();
            if ($idClientConnected !== $idUserClient) {
                throw new HttpException(403, "You haven't access to this ressource.");
            }

            return $user;
        });

        $json = $this->serializer->serialize($userCache, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/user', name: 'api_addUser', methods: ['POST'])]
    /**
     * Create a new user.
     */
    public function addUser(Request $request, EntityManagerInterface $manager, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $json = $request->getContent();
        $user = $this->deserializer->deserialize($json, Users::class, 'json');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $data = $this->serializer->serialize($errors, 'json');
            $response = new JsonResponse($data, 400, [], true);

            return $response;
        }

        $password = $userPasswordHasher->hashPassword($user, $user->getPassword());
        $dateCreate = new \DateTime();

        $user->setPassword($password)
            ->setRoles(['ROLE_USER'])
            ->setDateCreate($dateCreate)
            ->setFirstname($user->getFirstname())
            ->setLastname($user->getLastname())
            ->setClient($this->getUser());
        $manager->persist($user);
        $manager->flush();

        $json = $this->serializer->serialize($user, 'json');
        $response = new Response($json, 201, []);

        return $response;
    }

    #[Route('/api/user/{id}', name: 'api_deleteUser', methods: ['DELETE'])]
    public function deleteUser($id, UsersRepository $usersRepository, EntityManagerInterface $manager): Response
    {
        $client = $this->getUser();
        $idClient = $client->getId();

        $user = $usersRepository->find($id);

        if (!$user) {
            throw new HttpException(404, 'User not found.');
        }

        $userClient = $user->getClient();
        $idUserClient = $userClient->getId();

        if ($idClient !== $idUserClient) {
            throw new HttpException(403, "You haven't access to this resource.");
        }

        $manager->remove($user);
        $manager->flush();

        return new Response(null, 204);
    }
}
