<?php

namespace Pim\Bundle\CommentBundle\Form\Type;

use Pim\Bundle\CommentBundle\Repository\CommentRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Comment type
 *
 * @author    Olivier Soulet <olivier.soulet@akeneo.com>
 * @author    Julien Janvier <julien.janvier@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CommentType extends AbstractType
{
    /** @var CommentRepositoryInterface */
    protected $repository;

    /** @var TranslatorInterface  */
    protected $translator;

    /** @var string */
    protected $className;

    /**
     * @param CommentRepositoryInterface $repository
     * @param TranslatorInterface        $translator
     * @param string                     $className
     */
    public function __construct(CommentRepositoryInterface $repository, TranslatorInterface $translator, $className)
    {
        $this->repository = $repository;
        $this->translator = $translator;
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $placeholder = (true === $options['is_reply']) ? 'comment.placeholder.reply' : 'comment.placeholder.new';
        $placeholder = $this->translator->trans($placeholder);

        $builder
            ->add(
                'body',
                'textarea',
                ['label' => false, 'attr' => ['placeholder' => $placeholder, 'class' => 'exclude']]
            )
            ->add('resourceName', 'hidden')
            ->add('resourceId', 'hidden');

        if (true === $options['is_reply']) {
            $builder->add('parent', 'pim_object_identifier', ['multiple' => false, 'repository' => $this->repository]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->className,
                'is_reply' => false
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pim_comment_comment';
    }
}
