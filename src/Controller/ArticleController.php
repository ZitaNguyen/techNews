<?php


namespace App\Controller;


use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use App\Form\ArticleFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    use HelperTrait;
    /**
     * Démonstration de l'ajout d'un
     * Article avec Doctrine
     * @Route("/demo/article", name="article_demo")
     */
    public function demo()
    {
        # Création d'une Catégorie
        $categorie = new Categorie();
        $categorie->setNom("Politique");
        $categorie->setSlug("politique");

        # Création d'un Auteur (Membre)
        $membre = new Membre();
        $membre->setPrenom("Hugo")
                ->setNom("LIEGEARD")
                ->setEmail("hugo@technews.com")
                ->setPassword("test")
                ->setRoles(['ROLE_AUTEUR'])
                ->setDateInscription(new \DateTime());

        # Création de l'Article
        $article = new Article();
        $article->setTitre("Notre-dame de Paris reconstruire en 5 ans?")
                ->setSlug("notre-dame-de-paris-reconstuire-en-5-ans")
                ->setContenu("<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>")
                ->setFeaturedImage("notre-dame.jpg")
                ->setSpotlight(1)
                ->setSpecial(0)
                ->setMembre($membre)
                ->setCategorie($categorie)
                ->setDateCreation(new \DateTime());

        /*
         * Récupération du Manager de Doctrine
         * -------------------------------------
         * Le EntityManager est une classe qui sais comment persister d'autres classes.
         * Effectuer des opérations CRUD sur nos Entités
         */

        $em = $this->getDoctrine()->getManager(); // permet récuperer le EntityManager de Doctrine
        $em->persist($categorie); // J'enregistre dans ma base la catégorie
        $em->persist($membre); // Le Membre
        $em->persist($article); // et l'article

        $em->flush(); // J'execute le tout

        # Retourner une réponse à la vue
        return new Response('Nouvel Article ajouté avec ID: '
            . $article->getId()
            . ' et la nouvelle categorie '
            . $categorie->getNom()
            . 'de Auteur : '
            . $membre->getPrenom()
        );

    }

    /**
     * Formulaire pour créer un article
     * @Route("/creer-un-article", name="article_add")
     */
    public function  addArticle(Request $request)
    {
        # Création d'un nouvel article
        $article = new Article();


        # Récupération d'un Auteur (Membre)
        $membre = $this->getDoctrine()
                    ->getRepository(Membre::class)
                    ->find(1);

        # Affecter un Auteur à l'Article
        $article->setMembre($membre);

        # Création d'un Formulaire permettant l'ajout d'un Article
        $form = $this->createForm(ArticleFormType::class, $article);


        # Traitement des données POST
        $form->handleRequest($request);

        # Si le form est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            # dump($article);

            # 1. génération du slug
            $article->setSlug( $this->slugify( $article->getTitre() ) );

            # 2. traitement de l'upload de l'image
            /** @var UploadedFile $file */
            $file = $article->getFeaturedImage();
            $fileName = $article->getSlug().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            # Mise à jour de l'image
            $article->setFeaturedImage($fileName);

            # 3. sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            # 4. notification
            $this->addFlash('notice',
                'Félicitations, votre article est en ligne!');

            # 5. redirection
            return $this->redirectToRoute('default_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug' => $article->getSlug(),
                'id' => $article->getId()
            ]);

        }

        # Affichage du formulaire dans la vue
        return $this->render("article/addform.html.twig", [
            'form' => $form->createView()
        ]);

    }

}