<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;
use App\Entity\Article;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=>$articles
        ]);
    }
    /**
     * @Route("/",name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig', [
            'title'=> "Bievenue ici les amis!",
            'age'=>31
        ]);
    }

    /**
     * @Route ("/blog/new", name="blog_create")   
     * @Route ("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager){
        if(!$article){
            $article = new Article();
        }

        /*$form = $this->createFormBuilder($article)
                     ->add('title')
                     ->add('content')
                     ->add('image')
                     ->getForm();*/

        $form= $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreateAt(new \Datetime());
            }
            

            $manager->persist($article);
            $manager->flush();

            return $this ->redirectToRoute('blog_show',['id'=>$article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle'=> $form->createView(),
            'editMode'=>$article->getID() !== null
        ]);
    }

    /**
     * @Route ("/blog/{id}", name="blog_show")
     */
    public function show($id){
        $repo = $this->getDoctrine()->getRepository(Article::class);

        $article =$repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article'=>$article
        ]);
    }

}
