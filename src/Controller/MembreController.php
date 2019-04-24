<?php


namespace App\Controller;


use App\Entity\Membre;
use App\Form\ConnexionFormType;
use App\Form\MembreFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MembreController extends AbstractController
{
    /**
     * @Route("/inscription.html", name="membre_inscription")
     */
    public function inscription(Request $request, UserPasswordEncoderInterface $encoder)
    {
        # création d'un Membre
        $membre = new Membre();
        $membre->setRoles(['ROLE_MEMBRE']);

        # création du Formulaire "MembreFormType"
        $form = $this->createForm(MembreFormType::class, $membre);

        $form->handleRequest($request);

        # vérification de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            # Encoder le mot de passe
            $membre->setPassword(
                $encoder->encodePassword($membre, $membre->getPassword())
            );

            # sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($membre);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Féliciattion, vous pouvez vous connecter !');

            # Redirection
            return $this->redirectToRoute('membre_connexion');
        }

        # affichage du Formulaire dans la vue
        return $this->render("membre/inscription.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion.html", name="membre_connexion")
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {
        # Récupération du Formulaire de Connexion
        $form = $this->createForm( ConnexionFormType::class, [
            'email' => $authenticationUtils->getLastUsername()
        ]);

        # Affichage du formulaire dans la vue
        return $this->render('membre/connexion.html.twig', [
            'form' => $form->createView(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/deconnexion.html", name="membre_deconnexion")
     */
    public function deconnexion()
    {

    }
}