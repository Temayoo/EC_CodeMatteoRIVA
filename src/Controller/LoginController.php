<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'auth.login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig', [
            'name' => 'Thibaud',
        ]);
    }

}
