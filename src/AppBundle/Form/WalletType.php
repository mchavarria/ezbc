<?php

namespace AppBundle\Form;

use AppBundle\Data\BcModes;
use AppBundle\Data\UserTypes;
use AppBundle\Entity\BlockChain;
use AppBundle\Entity\User;
use AppBundle\Entity\Wallet;
use AppBundle\Repository\BlockChainRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WalletType
 */
class WalletType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('walletKey', TextType::class, [
            'attr' => [
                'placeholder' => 'Wallet Key'
            ]
        ]);
        $builder->add('pKey', TextType::class, [
            'label' => 'Private Key',
            'attr' => [
                'placeholder' => 'Wallet Private Key'
            ]
        ]);
        $builder->add(
            'bcType',
            EntityType::class,
            [
                'label' => 'Blockchain',
                'class' => BlockChain::class,
                'query_builder' => function (BlockChainRepository $bcr) {
                    return $bcr->createQueryBuilder('bc')
                        ->orderBy('bc.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => '-- Choose a Blockchain --'
            ]
        );

        /** @var User $user */
        $user = $options['user'];
        if ($user->getType() === UserTypes::FREE_USER) {
            $choices = [
                'Test Net' => BcModes::TEST_NET
            ];
        } else {
            $choices = [
                'Main Net' => BcModes::MAIN_NET,
                'Test Net' => BcModes::TEST_NET
            ];
        }
        $builder->add('bcMode', ChoiceType::class, [
            'label' => 'Blockchain Mode',
            'choices' => $choices,
            'placeholder' => '-- Choose an option --',
            'required' => true
        ]);
        $builder->add('description', TextareaType::class, [
            'attr' => [
                'placeholder' => 'Some description about the Wallet.'
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Wallet::class]);
        $resolver->setRequired('user');
    }
}