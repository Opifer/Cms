<?php

namespace Opifer\FormBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Opifer\CmsBundle\Entity\Locale;
use Opifer\EavBundle\Form\Type\SchemaType;
use Opifer\MediaBundle\Entity\MediaDirectory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type.
 *
 * This formtype defines the form fields necessary to define the form on the event page.
 */
class FormType extends AbstractType
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $postClass;

    protected $recaptchaSiteKey;
    protected $recaptchaSecretKey;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param $postClass
     * @param string|null $recaptchaSiteKey
     * @param string|null $recaptchaSecretKey
     */
    public function __construct(EntityManager $em, $postClass, $recaptchaSiteKey = null, $recaptchaSecretKey = null)
    {
        $this->em = $em;
        $this->postClass = $postClass;

        $this->recaptchaSiteKey = $recaptchaSiteKey;
        $this->recaptchaSecretKey = $recaptchaSecretKey;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $recaptchaKeysHelptext = '';
        if (!$this->recaptchaSiteKey || !$this->recaptchaSecretKey) {
            $recaptchaKeysHelptext = '<span class="text-danger">You don\'t have your recaptcha keys correctly setup in the config yet.</span>';
        }

        $builder
            ->add('name')
            ->add('notificationEmail', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'example@email.com',
                    'help_text' => 'Comma-separate email address for multiple notification emails. e.g. "info@domain.com, support@domain.com"',
                ],
            ])
            ->add('requiresConfirmation', ChoiceType::class, [
                'choices' => [
                    'Do not send confirmation' => false,
                    'Send confirmation' => true,
                ],
                'attr' => [
                    'help_text' => 'When a confirmation is required, a confirmation email will be sent to all email field values',
                ],
            ])
            ->add('redirectUrl', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '/success',
                    'help_text' => 'Where should we send a user to after a succesful form submission. Must be a fully specified URL or a relative path, starting with a "/"',
                ],
            ])
            ->add('uploadDirectory', EntityType::class, [
                'label' => 'Upload directory',
                'class'    => MediaDirectory::class,
                'property' => 'name',
                'attr'     => [
                    'help_text'   => 'In case this form supports uploads, we can place the upload in a specified directory. If empty, the file will be placed in the root of the media manager',
                ],
                'required' => false,
            ])
            ->add('locale', EntityType::class, [
                'label' => 'label.language',
                'class'    => Locale::class,
                'choice_label' => 'name',
                'attr'     => [
                    'help_text'   => 'help.content_language',
                ],
            ])
            ->add('recaptchaEnabled', ChoiceType::class, [
                'label' => 'Recaptcha',
                'choices' => [
                    'Disabled' => false,
                    'Enabled' => true,
                ],
                'attr' => [
                    'help_text' => 'Enables a hidden spamcheck on form submissions. '.$recaptchaKeysHelptext,
                ],
            ])
            ->add('schema', SchemaType::class, [
                'object_class' => $this->postClass,
            ])
        ;
    }
}
