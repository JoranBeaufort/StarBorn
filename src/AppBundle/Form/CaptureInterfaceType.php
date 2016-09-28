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
                    'Urban, Semiurban, Siedlung' => 'urban',
                    'Industrie-, Gewerbe- und Verkehrs- Flächen' => 'industry',
                    'Abbauflächen, Deponien und Baustellen' => 'mine',
                    'Künstliche Grünfläche' => 'greenarea',
                    'Ackerfläche' => 'arable',
                    'Dauerkultur' => 'permacrop',
                    'Weideland' => 'pasture',
                    'Weitere Landwirtschaft' => 'agriculture',
                    'Wald' => 'forest',
                    'Strauch- und Kraut- Vegetation' => 'shrub',
                    'Offene Flächen ohne/mit geringer Vegetation' => 'noveg',
                    'Feuchtfläche' => 'wetland',
                    'Wasserfläche' => 'water'
                 ),
                 'choice_attr' => array(
                     'Urban, Semiurban, Siedlung'                   =>array('img1' => 'img/capture/urban2.png', 'img2' =>'img/capture/urban1.png'),
                     'Industrie-, Gewerbe- und Verkehrs- Flächen'   =>array('img1' => 'img/capture/industry1.png', 'img2' =>'img/capture/industry2.png'),
                     'Abbauflächen, Deponien und Baustellen'        =>array('img1' => 'img/capture/mine1.png', 'img2' =>'img/capture/mine2.png'),
                     'Künstliche Grünfläche'                        =>array('img1' => 'img/capture/greenarea1.png', 'img2' =>'img/capture/greenarea2.png'),
                     'Ackerfläche'                                  =>array('img1' => 'img/capture/arable1.png', 'img2' =>'img/capture/arable2.png'),
                     'Dauerkultur'                                  =>array('img1' => 'img/capture/permacrop1.png', 'img2' =>'img/capture/permacrop2.png'),
                     'Weideland'                                    =>array('img1' => 'img/capture/pasture1.png', 'img2' =>'img/capture/pasture2.png'),
                     'Weitere Landwirtschaft'                       =>array('img1' => 'img/capture/agriculture1.png', 'img2' =>'img/capture/agriculture2.png'),
                     'Wald'                                         =>array('img1' => 'img/capture/forest1.png', 'img2' =>'img/capture/forest2.png'),
                     'Strauch- und Kraut- Vegetation'               =>array('img1' => 'img/capture/shrub1.png', 'img2' =>'img/capture/shrub2.png'),
                     'Offene Flächen ohne/mit geringer Vegetation'  =>array('img1' => 'img/capture/noveg1.png', 'img2' =>'img/capture/noveg2.png'),
                     'Feuchtfläche'                                 =>array('img1' => 'img/capture/wetland1.png', 'img2' =>'img/capture/wetland2.png'),
                     'Wasserfläche'                                 =>array('img1' => 'img/capture/water1.png', 'img2' =>'img/capture/water2.png'),
                 ),
                 'expanded' => true,
                 'multiple' => true,
             ) 
        );
            
    }
}