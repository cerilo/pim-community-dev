<?php

namespace Pim\Bundle\CatalogBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Unique variant group constraint
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UniqueVariantGroup extends Constraint
{
    /**
     * Violation message for already have a variant group
     *
     * @var string
     */
    public $message = 'You cannot add the product "%product%" to "%group%". It is already in the variant group "%product_variant_group%".';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'pim_unique_variant_group_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 
