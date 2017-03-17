EavBundle
=========

This bundle eases the implementation of an [entity-attribute-value model](https://en.wikipedia.org/wiki/Entity-attribute-value_model)
on an entity.

Model summary

- *EntityInterface.* Implemented by one or more of your own entities.
- *Attribute.* Defines the type of value.
- *Value.* The saved value.
- *Schema.* Defines the attributes for an EntityInterface.
- *ValueSet.* Functions as join table between the entity and its values. 
- *Option.* Defines the options for Select-, Checklist- or RadioValues.

Installation
------------

Add OpiferEavBundle to your composer.json:

    $ composer require opifer/eav-bundle

Register the bundle in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Opifer\EavBundle\OpiferEavBundle(),
    );
}

```

Create the `Attribute`, `Schema`, `ValueSet` and `Option` entities:

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\Attribute as BaseAttribute;

/**
 * @ORM\Entity()
 * @ORM\Table(name="attribute")
 */
class Attribute extends BaseAttribute
{

}

```

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\Schema as BaseSchema;

/**
 * @ORM\Entity()
 * @ORM\Table(name="schema")
 */
class Schema extends BaseSchema
{

}

```

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\ValueSet as BaseValueSet;

/**
 * @ORM\Entity()
 * @ORM\Table(name="valueset")
 */
class ValueSet extends BaseValueSet
{

}

```

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\Option as BaseOption;

/**
 * @ORM\Entity()
 * @ORM\Table(name="option")
 */
class Option extends BaseOption
{

}

```

Define these entities in your config:

```yml
opifer_eav:
    attribute_class: AppBundle\Entity\Attribute
    schema_class: AppBundle\Entity\Schema
    valueset_class: AppBundle\Entity\ValueSet
    option_class: AppBundle\Entity\Option
```

Usage
-----

Connecting the EAV-model to an entity is probably best explained by example.
Say, we got a Page entity that should have dynamic properties. We want to create
the following types of pages:

- Default page (just a title and a textarea)
- News page (title, textarea, author and a date)

First, we'll create the Page entity that implements `EntityInterface`:

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\ValueSetInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="page")
 */
class Page implements EntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var \Opifer\EavBundle\Model\ValueSet
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\ValueSetInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="valueset_id", referencedColumnName="id")
     */
    protected $valueSet;

    public function setValueSet(ValueSetInterface $valueSet)
    {
        $this->valueSet = $valueSet;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }
}

```

Define the `Page` entity in the config:

```yml
opifer_eav:
    # ...
    entities:
        page: AppBundle\Entity\Page
```

Create a the PageController with a createAction:

```php
namespace AppBundle\Controller;

use Opifer\EavBundle\Form\Type\ValueSetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // Fetch a the schema with the predefined attributes
        $schema = $em->getRepository('AppBundle:Schema')->find(1);
        
        // Initialize a new Page entity
        $page = $this->get('opifer.eav.eav_manager')->initializeEntity($schema);
        
        // Add the valueset type to the form
        $form = $this->createFormBuilder($page)
            ->add('valueset', ValueSetType::class)
            ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($page);
            $em->flush();
            
            // Redirect to the page edit/index
        }
        
        return $this->render('Page/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

```
