<?php

namespace spec\Pim\Bundle\CatalogBundle\Doctrine\ORM\Filter;

use PhpSpec\ObjectBehavior;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Pim\Bundle\CatalogBundle\Doctrine\InvalidArgumentException;
use Pim\Bundle\CatalogBundle\Model\AttributeInterface;
use Pim\Bundle\CatalogBundle\Doctrine\Common\ObjectIdResolverInterface;
use Pim\Bundle\CatalogBundle\Validator\AttributeValidatorHelper;
use Prophecy\Argument;

class OptionsFilterSpec extends ObjectBehavior
{
    function let(QueryBuilder $qb, AttributeValidatorHelper $attrValidatorHelper, ObjectIdResolverInterface $objectIdResolver)
    {
        $this->beConstructedWith($attrValidatorHelper, $objectIdResolver, ['pim_catalog_multiselect'], ['IN', 'EMPTY']);
        $this->setQueryBuilder($qb);
    }

    function it_is_a_field_filter()
    {
        $this->shouldImplement('Pim\Bundle\CatalogBundle\Doctrine\Query\AttributeFilterInterface');
    }

    function it_supports_operators()
    {
        $this->getOperators()->shouldReturn(['IN', 'EMPTY']);
        $this->supportsOperator('IN')->shouldReturn(true);
        $this->supportsOperator('EMPTY')->shouldReturn(true);
        $this->supportsOperator('FAKE')->shouldReturn(false);
    }

    function it_supports_multi_select_attribute(AttributeInterface $attribute)
    {
        $attribute->getAttributeType()->willReturn('pim_catalog_multiselect');
        $this->supportsAttribute($attribute)->shouldReturn(true);

        $attribute->getAttributeType()->willReturn(Argument::any());
        $this->supportsAttribute($attribute)->shouldReturn(false);
    }

    function it_adds_a_filter_to_the_query($qb, $attrValidatorHelper, AttributeInterface $attribute)
    {
        $attrValidatorHelper->validateLocale($attribute, Argument::any())->shouldBeCalled();
        $attrValidatorHelper->validateScope($attribute, Argument::any())->shouldBeCalled();

        $attribute->getId()->willReturn(42);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->isScopable()->willReturn(false);
        $attribute->getBackendType()->willReturn('options');
        $attribute->getCode()->willReturn('options_code');

        $qb->getRootAlias()->willReturn('r');
        $qb->expr()->willReturn(new Expr());

        $qb->innerJoin(
            'r.values',
            'filteroptions_code',
            'WITH',
            'filteroptions_code.attribute = 42'
        )->shouldBeCalled()->willReturn($qb);
        $qb->innerJoin(
            'filteroptions_code.options',
            'filterOoptions_code',
            'WITH',
            'filterOoptions_code.id IN(\'22\', \'42\')'
        )->shouldBeCalled()->willReturn($qb);

        $this->addAttributeFilter($attribute, 'IN', ['22', '42'], null, null, ['field' => 'options_code.id']);
    }

    function it_adds_an_empty_filter_to_the_query($qb, $attrValidatorHelper, AttributeInterface $attribute)
    {
        $attrValidatorHelper->validateLocale($attribute, Argument::any())->shouldBeCalled();
        $attrValidatorHelper->validateScope($attribute, Argument::any())->shouldBeCalled();

        $attribute->getId()->willReturn(42);
        $attribute->isLocalizable()->willReturn(false);
        $attribute->isScopable()->willReturn(false);
        $attribute->getBackendType()->willReturn('options');
        $attribute->getCode()->willReturn('options_code');

        $qb->getRootAlias()->willReturn('r');
        $qb->expr()->willReturn(new Expr());

        $qb->leftJoin(
            'r.values',
            'filteroptions_code',
            'WITH',
            'filteroptions_code.attribute = 42'
        )->shouldBeCalled()->willReturn($qb);
        $qb->leftJoin('filteroptions_code.options', 'filterOoptions_code')->shouldBeCalled()->willReturn($qb);
        $qb
            ->andWhere(
                'filterOoptions_code.id IS NULL'
            )
            ->shouldBeCalled()
        ;

        $this->addAttributeFilter($attribute, 'EMPTY', null, null, null, ['field' => 'options_code.id']);
    }

    function it_throws_an_exception_if_value_is_not_an_array(AttributeInterface $attribute)
    {
        $attribute->getCode()->willReturn('options_code');
        $this->shouldThrow(InvalidArgumentException::arrayExpected('options_code', 'filter', 'options'))
            ->during('addAttributeFilter', [$attribute, 'IN', 'WRONG', null, null, ['field' => 'options_code.id']]);
    }

    function it_throws_an_exception_if_the_content_of_value_are_not_numeric(AttributeInterface $attribute)
    {
        $attribute->getCode()->willReturn('options_code');
        $this->shouldThrow(InvalidArgumentException::numericExpected('options_code', 'filter', 'options'))
            ->during('addAttributeFilter', [$attribute, 'IN', [123, 'not numeric'], null, null, ['field' => 'options_code.id']]);
    }
}
