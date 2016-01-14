[![Build Status](https://travis-ci.org/Opifer/FormBundle.svg)](https://travis-ci.org/Opifer/FormBundle)

Opifer FormBundle
================

Installation
------------

Add OpiferForm to your composer.json:

    $ composer require opifer/form-bundle "~0.1"

Register the bundle in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Opifer\EavBundle\OpiferEavBundle(),
        new Opifer\FormBundle\OpiferFormBundle()
    );
}

```

Install the `OpiferEavBundle` according to its [documentation](https://github.com/Opifer/EavBundle/blob/master/README.md).

Extend the Form & Post models;

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\FormBundle\Model\Form as BaseForm;

/**
 * @ORM\Entity()
 * @ORM\Table(name="form")
 */
class Form extends BaseForm
{

}

```

```php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\FormBundle\Model\Post as BasePost;

/**
 * @ORM\Entity()
 * @ORM\Table(name="post")
 */
class Post extends BasePost implements EntityInterface
{

}

```

Define these in the config along with your admin email:

```yaml
opifer_form:
    from_email: noreply@yourcompany.com
    form:
        class: AppBundle\Entity\Form
    post:
        class: AppBundle\Entity\Post

```

To be able to manage and use your forms, add some routes to your `app/config/routing.yml`:

```yaml
opifer_form:
    resource: "@OpiferFormBundle/Resources/config/routing.yml"

opifer_form_admin:
    resource: "@OpiferFormBundle/Resources/config/routing_admin.yml"
    prefix: /admin
```

Usage
-----

Once you created your forms in your admin panel, you might want to display them on the frontend.
To do this, you'd have to create a controller action that retrieves the form and displays it on
the frontend

```php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function contactAction()
    {
        $form = $this->get('opifer.form.form_manager')->getRepository()->find(1);
        
        return $this->render('Page/contact.html.twig', array(
            'form' => $form
        ));
    }
}
```

```twig
{% app/Resources/views/Page/contact.html.twig %}

{% set form = create_form_view(form) %}
{{ form_start(form) }}
    {{ form_widget(form) }}
    <input type="submit" value="save">
{{ form_end(form) }}

```
