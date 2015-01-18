<?php

namespace spec\Pim\Bundle\BaseConnectorBundle\Reader;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Doctrine\MongoDB\Cursor;
use Doctrine\MongoDB\Query\Query;
use Doctrine\ORM\AbstractQuery;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Entity\Repository\GroupRepository;
use Pim\Bundle\CatalogBundle\Repository\ReferableEntityRepositoryInterface;

class RepositoryReaderSpec extends ObjectBehavior
{
    function let(StepExecution $stepExecution, GroupRepository $repository)
    {
        $this->beConstructedWith($repository, 'findAll');
        $this->setStepExecution($stepExecution);
    }

    function it_is_a_configurable_step_execution_aware_reader()
    {
        $this->shouldBeAnInstanceOf('Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement');
        $this->shouldImplement('Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface');
        $this->shouldImplement('Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface');
    }

    function it_reads_records_one_by_one($repository)
    {
        $repository->findAll()
            ->shouldBeCalled()
            ->willReturn(array('foo','bar'));

        $this->read()->shouldReturn('foo');
        $this->read()->shouldReturn('bar');
        $this->read()->shouldReturn(null);
    }

    function it_increments_read_count_for_each_record_reading($stepExecution, $repository)
    {
        $repository->findAll()
            ->shouldBeCalled()
            ->willReturn(array('foo','bar'));

        $stepExecution->incrementSummaryInfo('read')->shouldBeCalledTimes(2);

        $this->read();
        $this->read();
        $this->read();
    }

    function it_does_not_expose_any_configuration_fields()
    {
        $this->getConfigurationFields()->shouldHaveCount(0);
    }
}