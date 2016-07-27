<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CaptureInterfaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('landcover', ChoiceType::class, array(
                'choices' => array(
                    'Wald' => 'wood',
                    'Wasser' => 'water',
                    'Landwirtschaft' => 'food',
                    'Urban' => 'work',
                    'Schnee' => 'water',
                    'Berg' => 'stone',
                    'Wiese' => 'wood',
                    'Infrastruktur' => 'work',
                 ),
                 'choice_attr' => array(
                    'Wald' => array('text' => 'Das ist Wald'),
                    'Wasser' => array('text' => 'Das ist Wasser'),
                    'Landwirtschaft' => array('text' => 'Das ist LWS'),
                    'Urban' => array('text' => 'Das ist Urban'),
                    'Schnee' => array('text' => 'Das ist Urban'),
                    'Berg' => array('text' => 'Das ist Urban'),
                    'Wiese' => array('text' => 'Das ist Urban'),
                    'Infrastruktur' => array('text' => 'Das ist Urban'),
                 ),
                 'expanded' => true,
                 'multiple' => true,
             ) 
        );
            
    }
}