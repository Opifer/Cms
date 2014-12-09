<?php

namespace Opifer\ContentBundle\RulesEngine;

use Doctrine\ORM\EntityManager;
use Opifer\RulesEngineBundle\Provider\AbstractProvider;
use Opifer\RulesEngineBundle\Provider\ProviderInterface;
use Opifer\RulesEngine\Rule\Condition\Condition;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
use Opifer\RulesEngine\Rule\Condition\ContentCondition;
use Opifer\RulesEngine\Rule\Condition\CheckListValueCondition;
use Opifer\RulesEngine\Rule\Condition\StringValueCondition;
use Opifer\RulesEngine\Rule\Condition\AddressValueCityCondition;
use Opifer\RulesEngine\Rule\Condition\TemplateCondition;
use Opifer\RulesEngine\Rule\RuleSet;
use Opifer\EavBundle\Model\TemplateManager;

class ContentRulesProvider extends AbstractProvider implements ProviderInterface
{
    /** @var TemplateManager */
    protected $templateManager;

    /** @var string */
    protected $contentClass;

    /**
     * Constructor
     *
     * @param TemplateManager $templateManager
     */
    public function __construct(TemplateManager $templateManager, $contentClass)
    {
        $this->templateManager = $templateManager;
        $this->contentClass = $contentClass;
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

        $condition = new TemplateCondition();
        $condition->setGroup('Content')->setName('Template')->setEntity($this->templateManager->getClass())->setAttribute('id');

        $templateRepository = $this->templateManager->getRepository();
        $templates = $templateRepository->findBy(['objectClass' => $this->contentClass]);

        $options = array();
        foreach ($templates as $template) {
            $options[$template->getId()] = $template->getDisplayName();
        }

        $condition->setOptions($options);
        $rules[] = $condition;

        $condition = new ContentCondition();
        $condition->setGroup('Content')->setName('Content')->setOperatorOpts(array('in', 'notin'))->setEntity($this->contentClass)->setAttribute('id');
        $rules[] = $condition;

        $condition = new AttributeCondition();
        $condition->setGroup('Content')->setName('Title')->setOperatorOpts(array('equals', 'notequals', 'contains'))->setEntity($this->contentClass)->setAttribute('title');
        $rules[] = $condition;

        $condition = new AttributeCondition();
        $condition->setGroup('Content')->setName('Description')->setOperatorOpts(array('equals', 'notequals', 'contains'))->setEntity($this->contentClass)->setAttribute('title');
        $rules[] = $condition;

        $condition = new AttributeCondition();
        $condition->setGroup('Content')->setName('Slug')->setOperatorOpts(array('equals', 'notequals', 'contains'))->setEntity($this->contentClass)->setAttribute('title');
        $rules[] = $condition;

        // $condition = new AttributeCondition();
        // $condition->setGroup('Content')->setName('Directory')->setOperatorOpts(array('equals', 'notequals'))->setOperator('equals')->setEntity($this->contentClass)->setAttribute('title');
        // $rules[] = $condition;

        $templates = $templateRepository->findBy(['objectClass' => $this->contentClass]);

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
                        ->setEntity($this->contentClass)
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
                            ->setEntity($this->contentClass)
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
