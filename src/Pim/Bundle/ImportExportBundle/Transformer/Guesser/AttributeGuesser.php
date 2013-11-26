<?php

namespace Pim\Bundle\ImportExportBundle\Transformer\Guesser;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Pim\Bundle\ImportExportBundle\Transformer\ColumnInfo;
use Pim\Bundle\ImportExportBundle\Transformer\Guesser\GuesserInterface;
use Pim\Bundle\ImportExportBundle\Transformer\Property\PropertyTransformerInterface;

/**
 * Description of AttributeBackendTypeGuesser
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeGuesser implements GuesserInterface
{
    /**
     * @var PropertyTransformerInterface
     */
    protected $transformer;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $backendType;

    /**
     * Constructor
     *
     * @param PropertyTransformerInterface $transformer
     * @param string                       $class
     * @param string                       $backendType
     */
    public function __construct(
        PropertyTransformerInterface $transformer,
        $class,
        $backendType
    ) {
        $this->transformer = $transformer;
        $this->class = $class;
        $this->backendType = $backendType;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformerInfo(ColumnInfo $columnInfo, ClassMetadataInfo $metadata)
    {
        if ($this->class !== $metadata->getName() ||
            !isset($columnInfo['attribute']) ||
            $this->backendType !== $columnInfo['attribute']->getBackendType()
        ) {
            return;
        }

        return array($this->transformer, array());
    }
}