<?php
/**
 * User form type.
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{
    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['max_length' => 100],
                    'label' => $this->translator->trans('label.email'),
                ]
            );
    }

    /**
     * Configure options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * Get block prefix.
     */
    public function getBlockPrefix(): string
    {
        return 'user';
    }
}
