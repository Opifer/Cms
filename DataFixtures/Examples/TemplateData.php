<?php

//namespace AppBundle\DataFixtures\ORM\Schemas;
//
//use Opifer\CmsBundle\DataFixtures\Abstracts\SchemaFixtures;

/**
 * Example class for adding layouts, templates, attributes & options
 *
 * As you can see in namespace it should be in app bundle
 */
//class SchemaData extends SchemaFixtures
//{
    /**
     * must implement this method
     */
//    protected function update()
//    {
//        $layoutEntity = $this->addLayout('page', [
//            'description' => 'Page Layout',
//            'filename' => 'page.html.twig',
//            'placeholderkeys' => [],
//        ]);
//
//        $templateEntity = $this->addSchema('page', [
//            'displayName' => 'Page Schema',
//            'objectClass' => 'Opifer\CmsBundle\Entity\Content'
//        ], $layoutEntity);
//
//        $attributes = [
//            0 => [
//                'type' => 'nested',
//                'name' => 'content_blocks',
//                'displayName' => 'Content blocks',
//                'sort' => 10
//            ],
//            1 => [
//                'type' => 'html',
//                'name' => 'excerpt',
//                'displayName' => 'Excerpt',
//                'sort' => 20
//            ],
//            2 => [
//                'type' => 'media',
//                'name' => 'thumbnail',
//                'displayName' => 'Thumbnail',
//                'sort' => 30
//            ],
//            3 => [
//                'type' => 'menu_group',
//                'name' => 'main_menu',
//                'displayName' => 'Main menu',
//                'sort' => 40
//            ],
//            4 => [
//                'type' => 'menu_group',
//                'name' => 'footer_menu',
//                'displayName' => 'Footer menu',
//                'sort' => 50
//            ],
//            5 => [
//                'type' => 'nested',
//                'name' => 'hero_slider',
//                'displayName' => 'Hero block',
//                'sort' => 60
//            ],
//
//        ];
//
//        $this->addSchemaAttributes($templateEntity, $attributes);
//    }

    /**
     * {@inheritDoc}
     */
//    public function getOrder()
//    {
//        return 1; // the order in which fixtures will be loaded
//    }
//}
