<?php

namespace Opifer\CmsBundle\Menu;

use Knp\Menu\MenuFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Opifer\CmsBundle\Entity\Content;

class ContentMenu
{
    /** @var \Symfony\Bundle\FrameworkBundle\Translation\Translator */
    protected $translator;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    protected $router;

    /** @var array */
    protected $locales;

    /** @var string */
    protected $defaultLocale;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface     $router
     * @param array               $locales
     * @param string              $defaultLocale
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, $locales, $defaultLocale)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Build the menu
     *
     * @param Content $content
     *
     * @return \Knp\Menu\MenuItem
     */
    public function build(Content $content)
    {
        $menu = null;
        if (count($this->locales) > 1) {
            $factory = new MenuFactory();

            $menu = $factory->createItem('ContentMenu', $this->locales);
            $menu->addChild('default', [
                'uri' => $this->router->generate('opifer.cms.content.edit', [
                    'id' => ($content->getId()) ? $content->getId() : 0,
                    'locale' => null
                ]),
                'label' => $this->translator->trans('Default')
            ]);

            foreach ($this->locales as $plocale) {
                if ($plocale === $this->defaultLocale) {
                    continue;
                }

                $menu->addChild($plocale, [
                    'uri' => $this->router->generate('opifer.cms.content.edit', [
                        'id' => ($content->getId()) ? $content->getId() : 0,
                        'locale' => $plocale
                    ]),
                    'label' => $this->translator->trans($plocale)
                ]);
            }

            foreach ($menu->getChildren() as $menuChild) {
                if ($menuChild->getName() == $locale) {
                    $menuChild->setCurrent(true);
                }
                if ($menuChild->getName() == 'default' && $locale == null) {
                    $menuChild->setCurrent(true);
                }
            }
        }

        return $menu;
    }
}
