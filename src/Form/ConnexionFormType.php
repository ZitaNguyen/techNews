<?php


namespace App\Form;


use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnexionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre email'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre mot de passe'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Connexion'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        /*
         * Ici, mon formulaire MembreFormType travaillfera avec des instances de App/Entity/Membre
         */
        $resolver->setDefault('data_class', null);
    }

    public function getBlockPrefix()
    {
        return 'app_login';
    }
}