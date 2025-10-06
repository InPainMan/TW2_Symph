<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /* -------------------------------------------------
       EXACT-MATCH ROUTES  (MUST be first)
    ------------------------------------------------- */
    #[Route('/author/fixture', name: 'author_fixture')]
    public function fixture(EntityManagerInterface $em): Response
    {
        try {
            foreach (['Albert Camus', 'Victor Hugo', 'Nathaniel Hawthorne'] as $name) {
                $a = new Author();
                $a->setUsername($name)
                  ->setEmail(strtolower(str_replace(' ', '', $name)) . '@esprit.tn')
                  ->setNbBooks(0);
                $em->persist($a);
            }
            $em->flush();
        } catch (\Exception $e) {
            return new Response('Flush error: ' . $e->getMessage());
        }
        return new Response('3 authors inserted – <a href="/author">go to list</a>');
    }

    #[Route('/author', name: 'author_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $authors = $em->getRepository(Author::class)->findAll();
        return $this->render('author/list.html.twig', ['authors' => $authors]);
    }

    #[Route('/author/add', name: 'author_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();
        $form   = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author->setNbBooks(0);
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_list');
        }
        return $this->render('author/form.html.twig', ['f' => $form->createView()]);
    }

    #[Route('/author/edit/{id}', name: 'author_edit')]
    public function edit(Author $author, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('author_list');
        }
        return $this->render('author/form.html.twig', ['f' => $form->createView()]);
    }

    #[Route('/author/delete/{id}', name: 'author_delete')]
    public function delete(Author $author, EntityManagerInterface $em): Response
    {
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute('author_list');
    }

    /* -------------------------------------------------
       WILDCARD ROUTES  (must be LAST)
    ------------------------------------------------- */
    #[Route('/author/{name}', name: 'show_author')]
    public function showAuthor(string $name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name
        ]);
    }

    #[Route('/authors', name: 'list_authors')]
    public function listAuthors(): Response
    {
        $authors = [
            ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        return $this->render('author/list.html.twig', [
            'authors' => $authors
        ]);
    }
}