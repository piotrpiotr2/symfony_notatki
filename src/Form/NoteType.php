<?php
/**
 * Note type.
 */

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Note;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class NoteType.
 */
class NoteType extends AbstractType
{
    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * User.
     */
    private ?UserInterface $user;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     * @param Security            $security
     */
    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->user = $security->getUser();
        $this->translator = $translator;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['max_length' => 255],
                    'label' => $this->translator->trans('label.title'),
                ]
            )
            ->add(
                'content',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['max_length' => 1000],
                    'label' => $this->translator->trans('label.content'),
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'class' => Tag::class,
                    'label' => $this->translator->trans('label.tags'),
                    'placeholder' => $this->translator->trans('label.none'),
                    'required' => false,
                    'expanded' => true,
                    'choice_label' => function ($tag): string {
                        return $tag->getName();
                    },
                    'multiple' => true,
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'label' => $this->translator->trans('label.category'),
                    'placeholder' => $this->translator->trans('label.none'),
                    'required' => true,
                    'expanded' => false,
                    'choice_label' => function ($category): string {
                        return $category->getName();
                    },
                    'multiple' => false,
                    'query_builder' => function (CategoryRepository $repository): QueryBuilder {
                        return $repository->queryByAuthor($this->user);
                    },
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Note::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'note';
    }
}
