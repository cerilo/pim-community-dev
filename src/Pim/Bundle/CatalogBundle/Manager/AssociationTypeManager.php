<?php

namespace Pim\Bundle\CatalogBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Akeneo\Component\Persistence\SaverInterface;
use Akeneo\Component\Persistence\RemoverInterface;
use Pim\Bundle\CatalogBundle\Entity\Repository\AssociationTypeRepository;
use Pim\Bundle\CatalogBundle\Event\AssociationTypeEvents;
use Pim\Bundle\CatalogBundle\Model\AssociationTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Association type manager
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociationTypeManager implements SaverInterface, RemoverInterface
{
    /** @var AssociationTypeRepository $repository */
    protected $repository;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * Constructor
     *
     * @param AssociationTypeRepository $repository
     * @param ObjectManager             $objectManager
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(
        AssociationTypeRepository $repository,
        ObjectManager $objectManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repository      = $repository;
        $this->objectManager   = $objectManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get association types
     *
     * @return array
     */
    public function getAssociationTypes()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, array $options = [])
    {
        if (!$object instanceof AssociationTypeInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects an "Pim\Bundle\CatalogBundle\Model\AssociationTypeInterface", "%s" provided',
                    ClassUtils::getClass($object)
                )
            );
        }

        $options = array_merge(['flush' => true], $options);
        $this->objectManager->persist($object);
        if ($options['flush']) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object, array $options = [])
    {
        if (!$object instanceof AssociationTypeInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects an "Pim\Bundle\CatalogBundle\Model\AssociationTypeInterface", "%s" provided',
                    ClassUtils::getClass($object)
                )
            );
        }
        $this->eventDispatcher->dispatch(
            AssociationTypeEvents::PRE_REMOVE,
            new GenericEvent($object)
        );

        $options = array_merge(['flush' => true], $options);
        $this->objectManager->remove($object);
        if ($options['flush']) {
            $this->objectManager->flush();
        }
    }
}
