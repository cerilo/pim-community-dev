<?php

namespace spec\Pim\Bundle\CatalogBundle\AttributeType;

use Akeneo\Bundle\MeasureBundle\Manager\MeasureManager;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;
use Pim\Bundle\CatalogBundle\Factory\MetricFactory;
use Pim\Bundle\CatalogBundle\Model\AttributeInterface;
use Pim\Bundle\CatalogBundle\Model\ProductValueInterface;
use Pim\Bundle\CatalogBundle\Model\Metric;
use Pim\Bundle\CatalogBundle\Validator\AttributeConstraintGuesser;
use Prophecy\Argument;
use Symfony\Component\Form\FormFactory;

class MetricTypeSpec extends ObjectBehavior
{
    function let(
        AttributeConstraintGuesser $guesser,
        MeasureManager $manager,
        MetricFactory $metricFactory,
        ProductValueInterface $value,
        AttributeInterface $size
    ) {
        $value->getAttribute()->willReturn($size);

        $this->beConstructedWith(
            AbstractAttributeType::BACKEND_TYPE_METRIC,
            'pim_enrich_metric',
            $guesser,
            $manager,
            $metricFactory
        );
    }

    function it_builds_the_attribute_forms(FormFactory $factory, $size)
    {
        $size->getId()->willReturn(42);
        $size->getProperties()->willReturn([]);
        $size->setProperty(Argument::any(), Argument::any())->shouldBeCalled();
        $this->buildAttributeFormTypes($factory, $size)->shouldHaveCount(10);
    }

    function it_prepares_the_product_value_form($value, $size)
    {
        $size->getBackendType()->willReturn(AbstractAttributeType::BACKEND_TYPE_METRIC);
        $this->prepareValueFormName($value)->shouldReturn(AbstractAttributeType::BACKEND_TYPE_METRIC);
    }

    function it_prepares_the_product_value_form_alias($value)
    {
        $this->prepareValueFormAlias($value)->shouldReturn('pim_enrich_metric');
    }

    function it_prepares_the_product_value_form_options($value, $size, $manager)
    {
        $size->getLabel()->willReturn('size');
        $size->isRequired()->willReturn(false);
        $manager->getUnitSymbolsForFamily('Weight')->willReturn('Kg');
        $size->getMetricFamily()->willReturn('Weight');
        $size->getDefaultMetricUnit()->willReturn('KILOGRAM');

        $this->prepareValueFormOptions($value)->shouldHaveCount(7);
    }

    function it_prepares_the_product_value_form_constraints($value, $size, $guesser)
    {
        $guesser->supportAttribute($size)->willReturn(true);
        $guesser->guessConstraints($size)->willReturn([]);

        $this->prepareValueFormConstraints($value)->shouldHaveCount(1);
    }

    function it_prepares_the_product_value_form_data($value)
    {
        $metric = new Metric();
        $value->getData()->willReturn($metric);
        $this->prepareValueFormData($value)->shouldReturn($metric);
    }
}
