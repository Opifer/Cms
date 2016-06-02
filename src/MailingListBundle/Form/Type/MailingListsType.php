<?php

namespace Opifer\MailingListBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Opifer\MailingListBundle\Form\DataTransformer\MailingListToArrayTransformer;
use Opifer\MailingListBundle\Manager\MailingListManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * MailingListsType Type.
 */
class MailingListsType extends AbstractType
{
    /** @var MailingListManager */
    protected $mailingListManager;

    /**
     * Constructor
     *
     * @param MailingListManager $mailingListManager
     */
    public function __construct(MailingListManager $mailingListManager)
    {
        $this->mailingListManager = $mailingListManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new MailingListToArrayTransformer($this->mailingListManager->getManager()));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => $this->mailingListManager->getClass(),
            'choice_label' => 'displayName',
            'expanded' => true,
            'multiple' => true,
            'query_builder' => function (EntityRepository $repository) {
                return $repository->createQueryBuilder('m')
                    ->orderBy('m.displayName');
            },
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
