<?php

namespace Pim\Bundle\ImportExportBundle\Transformer;

/**
 * Transforms a label
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface LabelTransformerInterface
{
    /**
     * Returns an array of information about a label
     *
     * The array contains the following fields
     *  - label:            the full label
     *  - name:             the raw name of the attached property
     *  - propertyPath:     the real name of the attached property
     *  - locale:           the locale of the label
     *  - scope:            the scope of the label
     *
     * @param string       $class
     * @param string|array $label
     *
     * @return columnInfo|array
     */
    public function transform($class, $label);
}