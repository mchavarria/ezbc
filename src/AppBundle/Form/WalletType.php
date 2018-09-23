<?php

namespace AppBundle\Form;

use AppBundle\Data\BcModes;
use AppBundle\Entity\BlockChain;
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
        $builder->add(
            'bcType',
            EntityType::class,
            [
                'label' => 'Block Chain',
                'class' => BlockChain::class,
                'query_builder' => function (BlockChainRepository $bcr) {
                    return $bcr->createQueryBuilder('bc')
                        ->orderBy('bc.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => '-- Choose a Block Chain --'
            ]
        );
        $builder->add('bcMode', ChoiceType::class, [
            'label' => 'Block Chain Mode',
            'choices' => [
                'Main Net' => BcModes::MAIN_NET,
                'Test Net' => BcModes::TEST_NET
            ],
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
        $resolver->setDefaults(array(
            'data_class' => Wallet::class,
        ));
    }
}