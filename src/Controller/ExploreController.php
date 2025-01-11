<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExploreController extends AbstractController
{
    #[Route('/explore', name: 'app_explore')]
    public function index(BookReadRepository $bookReadRepository): Response
    {
        // get all Books read
        $booksRead = $bookReadRepository->findAll();

        return $this->render('pages/explore.html.twig', [
            'booksRead' => $booksRead,
        ]);
    }
}
