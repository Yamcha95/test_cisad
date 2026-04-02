<?php

namespace App\Form;

use App\Entity\Infos;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Joueur',
                'attr' => ['class' => 'form-control'],
                'disabled' => true, // Optionnel : empêche de changer le joueur lié à cette fiche
            ])
            ->add('rang', ChoiceType::class, [
                'label' => 'Rang de Ligue',
                'choices'  => [
                    'Fer' => 'Iron', // Affiche Fer, stocke Iron
                    'Bronze' => 'Bronze',
                    'Argent' => 'Silver',
                    'Or' => 'Gold',
                    'Platine' => 'Platinum',
                    'Diamant' => 'Diamond',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('victoire', IntegerType::class, [
                'label' => 'Victoires',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('defaite', IntegerType::class, [
                'label' => 'Défaites',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Infos::class,
        ]);
    }
}
