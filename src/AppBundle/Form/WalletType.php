<?php

namespace AppBundle\Form;

use AppBundle\Data\BlockChainTypes;
use AppBundle\Entity\Wallet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $builder->add('bcType', TextType::class, [
            'attr' => [
                'placeholder' => '-- Please Choose --'
            ]
        ]);

        $builder->add('bcType', ChoiceType::class, [
            'choices' => [
                'Bit Coin' => BlockChainTypes::BIT_COIN,
                'Etherium' => BlockChainTypes::ETHERIUM
            ],
            'placeholder' => '-- Choose an option --'
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Wallet::class,
        ));
    }
}