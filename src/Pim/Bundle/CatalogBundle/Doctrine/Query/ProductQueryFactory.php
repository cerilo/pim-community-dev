<?php

namespace Pim\Bundle\CatalogBundle\Doctrine\Query;

use Doctrine\Common\Persistence\ObjectManager;
use Pim\Bundle\CatalogBundle\Entity\Repository\AttributeRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Aims to wrap the creation and configuration of the product query builder
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductQueryFactory implements ProductQueryFactoryInterface
{
    /** @var string */
    protected $pqbClass;

    /** @var ObjectManager */
    protected $om;

    /** @var string */
    protected $productClass;

    /** @var AttributeRepository */
    protected $attributeRepository;

    /** QueryFilterRegistryInterface */
    protected $filterRegistry;

    /** QuerySorterRegistryInterface */
    protected $sorterRegistry;

    /**
     * @param string                       $pqbClass
     * @param ObjectManager                $om
     * @param string                       $productClass
     * @param AttributeRepository          $attributeRepository
     * @param QueryFilterRegistryInterface $filterRegistry
     * @param QuerySorterRegistryInterface $sorterRegistry
     */
    public function __construct(
        $pqbClass,
        ObjectManager $om,
        $productClass,
        AttributeRepository $attributeRepository,
        QueryFilterRegistryInterface $filterRegistry,
        QuerySorterRegistryInterface $sorterRegistry
    ) {
        $this->pqbClass = $pqbClass;
        $this->om = $om;
        $this->productClass = $productClass;
        $this->attributeRepository = $attributeRepository;
        $this->filterRegistry = $filterRegistry;
        $this->sorterRegistry = $sorterRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        $pqb = $this->createProductQueryBuilder(
            [
                'locale' => $options['default_locale'],
                'scope' => $options['default_scope']
            ]
        );

        $qb = $this->createQueryBuilder($options);
        $pqb->setQueryBuilder($qb);

        return $pqb;
    }

    /**
     * @param array $options
     *
     * @return ProductQueryBuilderInterface
     */
    protected function createProductQueryBuilder(array $options)
    {
        $pqb = new $this->pqbClass(
            $this->attributeRepository,
            $this->filterRegistry,
            $this->sorterRegistry,
            $options
        );

        return $pqb;
    }

    /**
     * @param array $options
     *
     * @return \Doctrine\ORM\QueryBuilder|\Doctrine\ODM\MongoDB\Query\Builder
     */
    protected function createQueryBuilder(array $options)
    {
        $repository = $this->om->getRepository($this->productClass);
        $method = $options['repository_method'];
        $parameters = $options['repository_parameters'];
        $qb = $repository->$method($parameters);

        return $qb;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(
            [
                'repository_method',
                'repository_parameters',
                'currentGroup',
                'product',
                'default_locale',
                'default_scope'
            ]
        );
        $resolver->setDefaults(
            [
                'repository_method' => 'createQueryBuilder',
                'repository_parameters' => 'o',
                'default_locale' => null,
                'default_scope' => null
            ]
        );
    }
}
