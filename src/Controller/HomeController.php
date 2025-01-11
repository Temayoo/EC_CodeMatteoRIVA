<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\BookRead;
use App\Form\LectureType;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AllowDynamicProperties] class HomeController extends AbstractController
{
    // Inject the repository via the constructor

    public function __construct(BookReadRepository $bookReadRepository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, BookRepository $bookRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(Request $request, EntityManagerInterface $entityManager, ): Response
    {

        if($this->getUser() == null){
            return $this->redirectToRoute('auth.login');
        }

        $userId = $this->getUser()->getId();
        $booksRead  = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => true]);
        $booksReading = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => false]);

//////////////////////////////////////////////////////////////////////////////////////////
        // gestion de récupération des livres + notes
        $booksWithRatings = [];
        $allBooks = $this->bookRepository->findAll();

        foreach ($allBooks as $book) {
            $bookReads = $this->bookReadRepository->findBy(['book_id' => $book->getId()]);

            $ratingSum = 0;
            $ratingCount = 0;

            foreach ($bookReads as $bookRead) {
                if ($bookRead->getRating() !== null) {
                    $ratingSum += $bookRead->getRating();
                    $ratingCount++;
                }
            }

            $averageRating = $ratingCount > 0 ? $ratingSum / $ratingCount : 0;
            $booksWithRatings[$book->getId()] = [
                'book' => $book,
                'averageRating' => $averageRating
            ];
        }
//////////////////////////////////////////////////////////////////////////////////////////
        // gestion récupération Catégories + combien de livre lu dans la catégorie
        $categories = [];
        $allCategories = $this->categoryRepository->findAll();
        foreach ($allCategories as $category) {
            $categories[$category->getId()] = [
                'name' => $category->getName(),
                'count' => 0
            ];
        }
        foreach ($booksRead as $bookRead) {
            $category = $bookRead->getBookId()->getCategoryId();
            if ($bookRead->getIsRead()) {
                $categories[$category->getId()]['count']++;
            }
        }
        $categoryLabels = [];
        $categoryData = [];
        foreach ($categories as $category) {
            $categoryLabels[] = $category['name'];
            $categoryData[] = $category['count'];
        }
//////////////////////////////////////////////////////////////////////////////////////////

        // gestion d'ajout de livre
        $bookRead = new BookRead();
        $form = $this->createForm(LectureType::class, $bookRead);
        $form->handleRequest($request);

        $editBookRead = new BookRead();
        $formEdit = $this->createForm(LectureType::class, $editBookRead);
        $formEdit->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($bookRead->getIsRead() === null) {
                $bookRead->setIsRead(false);
            }
            $bookRead->setRating($form->get('rating')->getData());
            $bookRead->setDescription($form->get('description')->getData());
            $bookRead->setUserId($userId);
            $bookRead->setBookId($form->get('book_id')->getData());
            $bookRead->setCreatedAt(new \DateTime());
            $bookRead->setUpdatedAt(new \DateTime());
            $entityManager->persist($bookRead);
            $entityManager->flush();
            return $this->redirectToRoute('app.home');
        }

        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            if ($editBookRead) {
                $editBookRead->setRating($formEdit->get('rating')->getData());
                $editBookRead->setDescription($formEdit->get('description')->getData());
                $editBookRead->setIsRead($formEdit->get('is_read')->getData() ?? false);
                $editBookRead->setUpdatedAt(new \DateTime());
                $entityManager->flush();
                return $this->redirectToRoute('app.home');
            }
        }


        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'name'      => 'Accueil',
            'userId'    => $userId,
            'bookReadForm' => $form->createView(),
            'formEdit'   => $formEdit->createView(),
            'booksReading' => $booksReading,
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
            'books' => $allBooks,
            'booksWithRatings' => $booksWithRatings,
        ]);
    }
}