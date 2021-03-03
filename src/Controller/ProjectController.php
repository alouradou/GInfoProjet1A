<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Item;
use App\Entity\User;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="projet_claque")
     */
    public function index(): Response{
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function home(): Response{
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('project/home.html.twig',[
            'controller_name' => 'ProjectController',
            'users' => $users
        ]);
    }

    /**
     * @Route("/utilisateurs", name="users")
     */
    public function users(): Response {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('project/users.html.twig',[
            'controller_name' => 'ProjectController',
            'users' => $users
        ]);
    }

    /**
     * @Route("/cours", name="courses")
     */
    public function courses(): Response {
        $repo = $this->getDoctrine()->getRepository(Course::class);
        $courses = $repo->findAll();
        return $this->render('project/courses.html.twig', [
            'controller_name' => 'ProjectController',
            'courses' => $courses
        ]);
    }

    /**
     * @Route("/elements", name="items")
     */
    public function items(): Response {
        $repo = $this->getDoctrine()->getRepository(Item::class);
        $items = $repo->findAll();
        return $this->render('project/items.html.twig', [
            'controller_name' => 'ProjectController',
            'items' => $items
        ]);
    }
}
