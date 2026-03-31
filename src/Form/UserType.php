<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // Important : ce champ n'existe pas dans l'entité User
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'placeholder' => 'Min. 8 car. (ex: qwerty123)'
                ],
                'constraints' => [
                    // On retire les [] pour utiliser l'argument nommé 'message'
                    new NotBlank(
                        message: 'Veuillez entrer un mot de passe'
                    ),
                    new Length(
                        min: 8,
                        minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères',
                        max: 4096,
                    ),
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*\d).+$/',
                        message: 'Le mot de passe doit contenir au moins une lettre et un chiffre'
                    ),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Droits d\'accès',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'label_attr' => ['class' => 'fw-bold text-muted small text-uppercase'],
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
