<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/all/users", name="api_all_users", methods={"GET"})
     * @param UtilisateurRepository $repository
     * @param SerializerInterface $serialezer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(UtilisateurRepository $repository, SerializerInterface $serialezer)
    {
        $users = $repository->findAll();
        $result = $serialezer->serialize(
            $users,
            'json'

        );

        return new JsonResponse($result,200,[],true);

    }

    /**
     * @Route("/api/user/{id}", name="api_users_show", methods={"GET"})
     * @param Utilisateur $user
     * @param SerializerInterface $serialezer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Utilisateur $user,SerializerInterface $serialezer)
    {

        $result = $serialezer->serialize(
            $user,
            'json'

        );

        return new JsonResponse($result, 200,[],true);
    }


    /**
     * @Route("/api/users/create", name="api_users_create", methods={"POST"})
     * @param Request $request
     * @param ObjectManager $em
     * @param SerializerInterface $serialezer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request,EntityManagerInterface $em,SerializerInterface $serialezer)
    {

        $data = $request->getContent();
        $user=  $serialezer->deserialize($data,Utilisateur::class,'json');
        $date = date('Y-m-d');
        $startDate = new \DateTime($date);
        $user->setCreationDate($startDate);
        $user->setUpdateDate($startDate);
        $em->persist($user);
        $em->flush();

        return new JsonResponse("utilisateur a étais céer",
            201,
            //["location"=> "api/users/creaate".$user->getId()],
            ["location" => $this->generateUrl('api_users_show', ["id"=>$user->getId(),UrlGeneratorInterface::ABSOLUTE_URL])],
            true);
    }


    /**
     * @Route("/api/user/{id}", name="api_users_update", methods={"PUT"})
     * @param Utilisateur $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serialezer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Utilisateur $user,Request $request,EntityManagerInterface $em,SerializerInterface $serialezer)
    {

        $data = $request->getContent();
        $serialezer->deserialize($data,Utilisateur::class,'json',['object_to_populate'=> $user]);

        $user->setCreationDate($user->getCreationDate());
        $date = date('Y-m-d');
        $startDate = new \DateTime($date);
        $user->setUpdateDate($startDate);
        $em->persist($user);
        $em->flush();
        return new JsonResponse('utilisateur a étais bien modifier', 200,[],true);
    }


    /**
     * @Route("/api/user/{id}", name="api_users_delete", methods={"DELETE"})
     * @param Utilisateur $user
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Utilisateur $user,EntityManagerInterface $em)
    {

        $em->remove($user);
        $em->flush();
        return new JsonResponse('utilisateur a étais bien supprimer', 200,[],false);
    }






}
