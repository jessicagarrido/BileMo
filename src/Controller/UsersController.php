<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Clients;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializer;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Knp\Component\Pager\PaginatorInterface;

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

    public function  __construct(SerializerInterface $serializer, SymfonySerializer $deserializer, UserPasswordHasherInterface $userPasswordHasher) {
        $this->serializer = $serializer;
        $this->deserializer = $deserializer;
        $this->passwordHasher = $userPasswordHasher;
    }

    #[Route('/listUsers', name: 'api_listUsers', methods: ['GET'])]

    /**
     * Return a list of users for the client
     * @param UsersRepository $userRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return response
     */
    public function listUsers(UsersRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //recover the client id connected
        // $client = $this->getUser();
        // $idClient = $client->getId();

        //recover the page
        $page = $request->query->getInt("page", 1);

            //recover the users of the client connected
            $datas = $userRepository->findByClient('40');
            //recover a page with 5 users
            $users = $paginator->paginate($datas, $page, 5);

            $json = $this->serializer->serialize($users, 'json');
            $response = new Response($json, 200, []);

            return $response;
    }

    #[Route('/user/{id}', name: 'api_user', methods: ['GET'])]

    /**
     * Return user client details
     * @param $id
     * @param UsersRepository $usersRepository
     * @return response
     */
    public function showUser($id, UsersRepository $usersRepository): Response
    {
        //recover the id of the client connected
        // $client = $this->getUser();
        // $idClient = $client->getId();

            //recover one mobile
            //recover the datas user
            $user = $usersRepository->findOneById('16');
            //recover the client id of the user
            // $userClient = $user->getClient();
            // $idUserClient = $userClient->getId();
            //verify if the client has access to this user
            // if($idClient !== $idUserClient) {
            //     throw New HttpException(403, "You haven't access to this ressource.");
            // }
            
            $json = $this->serializer->serialize($user, 'json');
            $response = new Response($json, 200, []);

            return $response;

        }
        
        #[Route('/addUser', name: 'api_addUser', methods: ['POST'])]

    /**
     * Create a new user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return response
     */
    public function addUser(Request $request, EntityManagerInterface $manager, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $json = $request->getContent();
        //transform the datas in object
        $user = $this->deserializer->deserialize($json, Users::class, 'json');
        $errors = $validator->validate($user);

        if(count($errors) > 0) {
            $data = $this->serializer->serialize($errors, 'json');
            $response =  new JsonResponse($data, 400, [], true);
            return $response;
        }

        $password = $userPasswordHasher->hashPassword($user,$user->getPassword());
        $dateCreate = new \DateTime();

        $user->setPassword($password)
            ->setRoles(["ROLE_USER"])
            ->setDateCreate($dateCreate)
            ->setClient($this->getUser());
        $manager->persist($user);
        $manager->flush();
        
        $json = $this->serializer->serialize($user, 'json');
        $response = new Response($json, 201, []);
        return $response;
    }

    #[Route('/deleteUser/{id}', name: 'api_deleteUser', methods: ['DELETE'])]

    /**
     * Delete an user
     * @param $id
     * @param UsersRepository $usersRepository
     * @param EntityManagerInterface $manager
     * @return response
     */
    public function deleteUser($id, UsersRepository $usersRepository): Response
    {
        //recover the id of the client connected
        $client = $this->getUser();
        $idClient = $client->getId();
        //recover the datas user
        $user = $usersRepository->find($id);
        //recover the client id of the user
        $userClient = $user->getClient();
        $idUserClient = $userClient->getId();
        //verify if the client has access to this user
        if($idClient !== $idUserClient) {
            throw New HttpException(403, "You haven't access to this ressource.");
        }
        
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        return new Response("The user has been deleted");
        
    }
}