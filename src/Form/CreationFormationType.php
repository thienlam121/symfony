<?php

namespace App\Form;

use App\Entity\Formation;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as SFType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;




class CreationFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentDate = new \DateTime();
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date',
                    'style' => 'padding: 8px; border: 1px solid #ced4da; border-radius: 4px; width: 100%; box-sizing: border-box; font-size: 16px;'
                ]
            ])
            ->add('nbreHeures')
            ->add('departement')
            ->add('nom')
            ->add('produit', EntityType::class ,array('class'=>'App\Entity\Produit', 'choice_label'=>'libelle'))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
