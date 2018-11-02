<?php

namespace AppBundle\Form;

use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\User;
use AppBundle\Entity\Wallet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ApiEndPointType
 */
class ApiEndPointType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'attr' => [
                'placeholder' => 'Name'
            ]
        ]);
        $builder->add('description', TextType::class, [
            'attr' => [
                'placeholder' => 'Description'
            ]
        ]);

        //TODO agregar opcion para que usuario normal elija su wallet
        $isAdmin = $options['is_admin'];
        if ($isAdmin) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'placeholder' => '-- Choose an option --',
                'choice_label' => 'fullName'
            ]);
            $builder->add('wallet', EntityType::class, [
                'class' => Wallet::class,
                'placeholder' => '-- Choose an option --',
                'choice_label' => 'formLabel'
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => ApiEndPoint::class]);
        $resolver->setRequired('is_admin');
    }
}