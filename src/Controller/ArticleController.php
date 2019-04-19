<?php


namespace App\Controller;


use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
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
    public function  addArticle()
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
        $form = $this->createFormBuilder($article)
                    ->add('titre', TextType::class, [
                        'required' => true,
                        'label'    => false,
                        'attr'     => [
                            'placeholder' => "Titre de l'Article"
                        ]
                    ])
                    ->add('categorie', EntityType::class, [
                        'class' => Categorie::class,
                        'choice_label' => 'nom',
                        'label' => false,
                        'placeholder' => 'Choisissez le catégorie'
                    ])
                    ->add('contenu', TextareaType::class, [
                        'required' => true,
                        'label'    => false,
                        'attr'     => [
                            'placeholder' => "Contenu de l'Article"
                        ]
                    ])
                    ->add('featuredImage', FileType::class, [
                        'required' => true,
                        'label'    => false,
                        'attr' => [
                            'class' => 'dropify'
                        ]
                    ])
                    ->add('special', CheckboxType::class, [
                        'required' => false,
                        'attr' => [
                            'data-toggle' => 'toggle',
                            'data-on' => 'Oui',
                            'data-off' => 'Non'
                        ]
                    ])
                    ->add('spotlight', CheckboxType::class, [
                        'required' => false,
                        'attr' => [
                            'data-toggle' => 'toggle',
                            'data-on' => 'Oui',
                            'data-off' => 'Non'
                        ]
                    ])
                    ->add('submit', SubmitType::class, [
                        'label' => 'Publier mon Article'
                    ])
                    ->getForm();

        # Affichage du formulaire dans la vue
        return $this->render("article/addform.html.twig", [
            'form' => $form->createView()
        ]);

    }

}