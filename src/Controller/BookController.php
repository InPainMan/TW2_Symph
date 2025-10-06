<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/add', name: 'book_add')]
    public function add(Request $r, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($r);
        if ($form->isSubmitted() && $form->isValid()) {
            $book->setPublished(true);               
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_list');
        }
        return $this->render('book/form.html.twig', ['f' => $form->createView()]);
    }


    #[Route('/', name: 'book_list')]
    public function list(BookRepository $repo): Response
    {
        $published = $repo->findBy(['published' => true]);
        $nbPub     = count($published);
        $nbUnpub   = count($repo->findBy(['published' => false]));
        return $this->render('book/list.html.twig', [
            'books'   => $published,
            'nbPub'   => $nbPub,
            'nbUnpub' => $nbUnpub,
        ]);
    }


    #[Route('/edit/{id}', name: 'book_edit')]
    public function edit(Book $book, Request $r, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($r);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('book_list');
        }
        return $this->render('book/form.html.twig', ['f' => $form->createView()]);
    }

 
    #[Route('/delete/{id}', name: 'book_delete')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();
        $author->setNbBooks($author->getNbBooks() - 1);
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('book_list');
    }


    #[Route('/show/{id}', name: 'book_show')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', ['book' => $book]);
    }


    #[Route('/prune-authors', name: 'book_prune_authors')]
    public function pruneAuthors(EntityManagerInterface $em): Response
    {
        $em->createQuery('DELETE App\Entity\Author a WHERE a.nbBooks = 0')->execute();
        return $this->redirectToRoute('author_list');
    }
}