<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use OAuth2\Client;
use OAuth2\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

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
     * @Route("/mylogin", name="security_my_login")
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @param EventDispatcherInterface $dispatcher
     * @return RedirectResponse
     * @throws Exception
     */
    public function index(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session, EventDispatcherInterface $dispatcher, EntityManagerInterface $manager): RedirectResponse
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('home');
        }


        $id = $this->getParameter('oauth_id');
        $secret = $this->getParameter('oauth_secret');
        $base = $this->getParameter('oauth_base');

        $client = new \OAuth2\Client($id, $secret);

        if(!$request->query->has('code')){
            $url = $client->getAuthenticationUrl($base.'/oauth/v2/auth', $this->generateUrl('security_my_login', [],UrlGeneratorInterface::ABSOLUTE_URL));
            return $this->redirect($url);
        }else{
            $params = ['code' => $request->query->get('code'), 'redirect_uri' => $this->generateUrl('security_my_login', [],UrlGeneratorInterface::ABSOLUTE_URL)];
            $resp = $client->getAccessToken($base.'/oauth/v2/token', 'authorization_code', $params);

            if(isset($resp['result']) && isset($resp['result']['access_token'])){
                $info = $resp['result'];

                $client->setAccessTokenType(\OAuth2\Client::ACCESS_TOKEN_BEARER);
                $client->setAccessToken($info['access_token']);
                $response = $client->fetch($base.'/api/user/me');
                $data = $response['result'];

                $username = $data['username'];

                $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$username]);
                if($user === null){ // Création de l'utilisateur s'il n'existe pas
                    $user = new User;
                    $user->setUsername($username);
                    $user->setPassword(sha1(uniqid()));
                    $user->setEmail($data['email']);
                    $user->setLastName(ucwords(strtolower($data['nom'])));
                    $user->setFirstName($data['prenom']);
                    $user->setRoles(["ROLE_USER"]); // Attribution du rôle par défaut


                    $manager->persist($user);
                    $manager->flush();
                }

                // Connexion effective de l'utilisateur
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $tokenStorage->setToken($token);

                $session->set('_security_main', serialize($token));

                $event = new InteractiveLoginEvent($request, $token);
                $dispatcher->dispatch("security.interactive_login", $event);

            }

            // Redirection vers l'accueil
            return $this->redirectToRoute('home');
        }

    }

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response{
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){
    }
}
