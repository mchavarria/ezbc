<?php

namespace AppBundle\Form;

use AppBundle\Data\UserTypes;
use AppBundle\Entity\ApiEndPoint;
use AppBundle\Entity\User;
use AppBundle\Entity\Wallet;
use AppBundle\Repository\WalletRepository;
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

        /** @var User $user */
        $user = $options['user'];
        if ($user->getType() === UserTypes::ADMIN_USER) {
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
        } else {
            $builder->add(
                'wallet',
                EntityType::class,
                [
                    'class' => Wallet::class,
                    'query_builder' => function (WalletRepository $er) use ($user) {
                        return $er->createQueryBuilder('w')
                            ->where('w.user = :user')
                            ->orderBy('w.id', 'ASC')
                            ->setParameter('user', $user);
                    },
                    'choice_label' => 'formLabel',
                    'placeholder' => '-- Choose a Wallet --'
                ]
            );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => ApiEndPoint::class]);
        $resolver->setRequired('user');
    }
}