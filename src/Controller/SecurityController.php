<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="security_registration")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $psw
     * @return Response
     */
    public function registration(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $psw): Response{
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $hash = $psw->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("security_login");
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){
    }
}
