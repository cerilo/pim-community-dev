<?php

namespace spec\Akeneo\Bundle\StorageUtilsBundle\Cursor\MongoDBODM;

use PhpSpec\ObjectBehavior;
use Doctrine\ODM\MongoDB\Query\Builder;

class MongoDBODMCursorFactorySpec extends ObjectBehavior
{
    const DEFAULT_BATCH_SIZE = 100;

    public function let()
    {
        $this->beConstructedWith('Akeneo\Bundle\StorageUtilsBundle\Cursor\MongoDBODM\MongoDBODMCursor', self::DEFAULT_BATCH_SIZE);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\Bundle\StorageUtilsBundle\Cursor\MongoDBODM\MongoDBODMCursorFactory');
        $this->shouldImplement('Akeneo\Bundle\StorageUtilsBundle\Cursor\CursorFactoryInterface');
    }

    public function it_create_a_cursor(Builder $queryBuilder)
    {
        $cursor = $this->createCursor($queryBuilder);
        $cursor->shouldBeAnInstanceOf('Akeneo\Bundle\StorageUtilsBundle\Cursor\CursorInterface');
    }
}
