<?php
namespace JoranBeaufort\Neo4jUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('screenname', TextType::class)
            ->add('plainPassword', RepeatedType::class, array(
                  'type' => PasswordType::class)
            )
            ->add('gender', ChoiceType::class, array(
                'choices' => array(
                    'Keine Angabe' => 'k',
                    'Männlich' => 'm',
                    'Weiblich' => 'f'
                ),
                'required'    => True,
                'empty_data'  => null
            ))
            ->add('birthdate', BirthdayType::class, array(
                'input' => 'string',
                'html5' => true,
            ));
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JoranBeaufort\Neo4jUserBundle\Entity\User',
        ));
    }
}