<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TeamApplyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('teamapply', ChoiceType::class, array(
                'choices' => array(
                    'Red Giants' => 'red_giants',
                    'Blue Dwarfs' => 'blue_dwarfs',
                 ),
                 'choice_attr' => array(
                    'Red Giants' => array(
                        'title' => 'Galaktisches Imperium der Roten Riesen',
                        'text' => 'Das galaktische Imperium der Roten Riesen (auch bekannt als die "Red Giants") sind ein zielstrebiges und naturverbundenes Volk. Die Roten Riesen haben durch jahrtausend lange Forschungen die Energiegewinnung aus der Luft optimiert und überzeugen durch ihre technische Affinität. Die roten Riesen setzen auch vereinzelt ihre Technologien in Konflikte ein, um sich einen Vorteil zu verschaffen.',
                        'icon' => 'fa-rocket',
                        'colour' => '#FF8800',
                        'class' => 'red_giants'
                    ),
                    'Blue Dwarfs' => array(
                        'title' => 'Interplanetare Gesellschaft der Blauen Zwerge',
                        'text' => 'Die interplanetare Gesellschaft der Blauen Zwerge (auch bekannt als die "Blue Dwarfs") gehören zu den Untertagbewohner. Die blauen Zwerge zapfen die Energieadern der Erde an um einen unbestrittenen Wirkungsgrad der Energieerzeugen zu erreichen. Auch im Kampf sind die blauen Zwerge nicht zu unterschätzen, wobei sie mehr auf ihre Ausdauer und ihr Können Zählen, als auf Technologien!',
                        'icon' => 'fa-space-shuttle',
                        'colour' => '#0099CC',
                        'class' => 'blue_dwarfs'
                    ),
                 ),
                 'expanded' => true,
                 'multiple' => false,
             ) 
        );
            
    }
}