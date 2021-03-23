<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Require ROLE_USER for *every* controller method in this class.
 *
 * @IsGranted("ROLE_USER")
 */
class FormationsController extends AbstractController
{
    /**
     * @Route("/formations", name="formations")
     */
    public function index(): Response{
        $repo = $this->getDoctrine()->getRepository(Course::class);
        $courses = $repo->findAll();
        $user = $this->getUser();
        return $this->render('formations/formations.html.twig', [
            'controller_name' => 'FormationsController',
            'courses' => $courses,
            'current_user' => $user
        ]);
    }

    /**
     * @Route("/course/{id}/subscribe", name="course_subscribe")
     *
     * @param Course $course
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function inscription(Course $course, EntityManagerInterface $manager): Response{
        $user = $this->getUser();

        if (!$user)
            return $this->json(['code' => 403,'message' => 'Unauthorized'],403);

        $user->addCoursesFollowed($course);
        $manager->persist($user);
        $manager->flush();

        return $this->json(['code' => 200,'message' => 'Insription au cours prise en compte'],200);
    }

    /**
     * @Route("/course/{id}/unsubscribe", name="course_unsubscribe")
     *
     * @param Course $course
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function deinscription(Course $course, EntityManagerInterface $manager): Response{
        $user = $this->getUser();

        if (!$user)
            return $this->json(['code' => 403,'message' => 'Unauthorized'],403);


        $user->removeCoursesFollowed($course);
        $manager->persist($user);
        $manager->flush();

        return $this->json(['code' => 200,'message' => 'Désinsription au cours prise en compte'],200);
    }
}
