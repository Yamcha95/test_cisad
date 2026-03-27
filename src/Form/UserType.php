<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
// Ne pas oublier l'import du ChoiceType !
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => ['placeholder' => 'Ex: Mario'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'attr' => ['placeholder' => 'mario@nintendo.com'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'always_empty' => false, // Utile pour l'édition
                'help' => 'Laissez vide pour ne pas modifier (si édition)',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Droits d\'accès',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,  // Cases à cocher au lieu d'un select
                'multiple' => true,  // Permet d'avoir plusieurs rôles
                'label_attr' => ['class' => 'fw-bold'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
