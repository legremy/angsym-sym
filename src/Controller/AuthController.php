<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_login")
     */
    public function login()
    {
        return $this->render('auth/login.html.twig', []);
    }

    public function logout()
    { }
}
