<?php


namespace App\Controller;


use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Page d'Accueil
     */
    public function index()
    {
        $repository = $this->getDoctrine()
                    ->getRepository(Article::class);

        # Je récupère tous les articles de ma base
        $articles = $repository->findAll();
        $spotlights = $repository->findBySpotlight();

        # Transmission à la vue pour affichage
        return $this->render("default/index.html.twig.", [
            'articles' => $articles,
            'spotlights' => $spotlights
        ]);

    }

    /**
     * Page de Contact
     */
    public function contact()
    {
        return $this->render("default/contact.html.twig");
        #return new Response("<html><body><h1>Page Contact</h1></body></html>");
    }

    /**
     * Page permettant d'afficher les articles d'une catégorie
     * @Route("/categorie/{slug<[a-zA-Z0-9\-_\/]+>}",
     *     defaults={"slug"="default"},
     *     methods={"GET|POST"},
     *     name="default_categorie")
     */
    public function categorie($slug)
    {
        /*
         * Récupérer la catégorie correspondant au "slug" passer en parametre de la route
         * ---------------------------------------------------------------
         * on récupère la paramètre "slug" de la route (url) dans notre variable $slug
         */
        $categorie = $this->getDoctrine()
                ->getRepository(Categorie::class)
                ->findOneBy(['slug' => $slug]);

        #dump( $categorie );
        #dump ( Categorie::class);

        /*
         * Grace à la relation entre Article et Catégorie (OneToMany),
         * je suis en mesure de récupérer les articles d'une catégorie
         */
        $articles = $categorie->getArticles();

        return $this->render('default/categorie.html.twig', [
            'articles' => $articles,
            'categorie' => $categorie
        ]);
        #return new Response("<html><body><h1>Page Catégorie: $slug</h1></body></html>");
    }

    /**
     * Page permettant d'afficher un article
     * @Route("/{categorie}/{slug}_{id<\d+>}.html",
     *          name="default_article")
     */
    public function article($categorie, $slug, $id)
    {
        /*
         * Récupération de l'article correspondant
         * à l'ID en paramètre de notre route
         */
        $article = $this->getDoctrine()
                    ->getRepository(Article::class)
                    ->find($id);

        # on passe à la vue les données à afficher
        return $this->render('default/article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * Génération de la sidebar
     */
    public function sidebar()
    {
        $repository = $this->getDoctrine()
                        ->getRepository(Article::class);

        # Récupération des 5 derniers articles
        $articles = $repository->findByLatest();

        # Récupération des articles à la position "special"
        $special = $repository->findBySpecial();

        # Transmission des informations à la vue
        return $this->render('components/_sidebar.html.twig', [
           'articles' => $articles,
           'special' => $special
        ]);
    }

}