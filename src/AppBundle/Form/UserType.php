<?php

namespace AppBundle\Form;

use AppBundle\Data\UserTypes;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, [
            'attr' => [
                'placeholder' => 'First Name'
            ]
        ]);
        $builder->add('lastName', TextType::class, [
            'attr' => [
                'placeholder' => 'Last Name'
            ]
        ]);
        $builder->add('email', EmailType::class, [
            'attr' => [
                'placeholder' => 'Email'
            ]
        ]);
        $builder->add('username', TextType::class, [
            'attr' => [
                'placeholder' => 'Username'
            ]
        ]);

        $isNew = $options['is_new'];
        if ($isNew) {
            $builder->add('plainPassword', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Password'
                ]
            ]);
        }

        $builder->add('type', ChoiceType::class, [
            'choices' => [
                'Admin' => UserTypes::ADMIN_USER,
                'Free User' => UserTypes::FREE_USER,
                'Basic User' => UserTypes::BASIC_USER,
                'Premium User' => UserTypes::PREMIUM_USER,
                'Exclusive User' => UserTypes::EXCLUSIVE_USER
            ],
            'placeholder' => '-- Choose an option --'
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
        $resolver->setRequired('is_new');
    }
}