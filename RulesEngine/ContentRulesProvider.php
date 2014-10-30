<?php

namespace Opifer\ContentBundle\RulesEngine;

use Doctrine\ORM\EntityManager;
use Opifer\RulesEngineBundle\Provider\AbstractProvider;
use Opifer\RulesEngineBundle\Provider\ProviderInterface;
use Opifer\RulesEngine\Rule\Condition\Condition;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
use Opifer\RulesEngine\Rule\Condition\EntityCondition;
use Opifer\RulesEngine\Rule\Condition\CheckListValueCondition;
use Opifer\RulesEngine\Rule\Condition\StringValueCondition;
use Opifer\RulesEngine\Rule\Condition\AddressValueCityCondition;
use Opifer\RulesEngine\Rule\Condition\TemplateCondition;
use Opifer\RulesEngine\Rule\RuleSet;

class ContentRulesProvider extends AbstractProvider implements ProviderInterface
{
    private $entityManager;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Build rules
     *
     * @return array
     */
    public function buildRules()
    {
        $rules = array();
        $rules[] = new RuleSet();

        $entity = 'Opifer\ContentBundle\Entity\Content';

        $condition = new TemplateCondition();
        $condition->setGroup('Content')->setName('Template')->setEntity($entity)->setAttribute('id');
        $templates = $this->entityManager->getRepository('OpiferEavBundle:Template');
        $templates = $templates->findBy(array('objectClass' => 'Opifer\ContentBundle\Entity\Content'));

        $options = array();
        foreach ($templates as $template) {
            $options[$template->getId()] = $template->getDisplayName();
        }

        $condition->setOptions($options);
        $rules[] = $condition;

        $condition = new EntityCondition();
        $condition->setGroup('Content')->setName('Content')->setOperatorOpts(array('in', 'notin'))->setEntity($entity)->setAttribute('id');
        $rules[] = $condition;

        $condition = new AttributeCondition();
        $condition->setGroup('Content')->setName('Title')->setOperatorOpts(array('equals', 'notequals', 'contains'))->setEntity($entity)->setAttribute('title');
        $rules[] = $condition;

        $condition = new AttributeCondition();
        $condition->setGroup('Content')->setName('Description')->setOperatorOpts(array('equals', 'notequals', 'contains'))->setEntity($entity)->setAttribute('title');
        $rules[] = $condition;

        $condition = new AttributeCondition();
        $condition->setGroup('Content')->setName('Slug')->setOperatorOpts(array('equals', 'notequals', 'contains'))->setEntity($entity)->setAttribute('title');
        $rules[] = $condition;

        // $condition = new AttributeCondition();
        // $condition->setGroup('Content')->setName('Directory')->setOperatorOpts(array('equals', 'notequals'))->setOperator('equals')->setEntity($entity)->setAttribute('title');
        // $rules[] = $condition;

        $repo = $this->entityManager->getRepository('OpiferEavBundle:Template');
        $templates = $repo->findBy(['objectClass' => $entity]);

        foreach ($templates as $template) {
            foreach ($template->getAttributes() as $attribute) {
                $condition = false;
                switch ($attribute->getValueType()) {
                    case 'Opifer\EavBundle\Entity\TextValue':
                    case 'Opifer\EavBundle\Entity\HtmlValue':
                    case 'Opifer\EavBundle\Entity\StringValue':
                        $condition = new StringValueCondition();
                        $condition->setOperatorOpts(array('equals', 'notequals', 'contains'));
                        break;
                    case 'Opifer\EavBundle\Entity\CheckListValue':
                        $condition = new CheckListValueCondition();
                        $options = array();
                        foreach ($attribute->getOptions() as $option) {
                            $options[$option->getId()] = $option->getDisplayName();
                        }
                        $condition->setOptions($options);
                        break;
                }
                if ($condition) {
                    $condition
                        ->setGroup($template->getDisplayName())
                        ->setName($attribute->getDisplayName())
                        ->setEntity($entity)
                        ->setAttribute($attribute->getName())
                    ;

                    $rules[] = $condition;
                }

                switch ($attribute->getValueType()) {
                    case 'Opifer\EavBundle\Entity\AddressValue':
                        $condition = new AddressValueCityCondition();
                        $condition
                            ->setGroup($template->getDisplayName())
                            ->setName($attribute->getDisplayName() . ' – City')
                            ->setEntity($entity)
                            ->setAttribute($attribute->getName())
                        ;

                        $rules[] = $condition;
                        break;
                }
            }
        }

        return $rules;
    }
}
