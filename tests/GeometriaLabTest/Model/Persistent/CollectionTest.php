<?php

namespace GeometriaLabTest\Model\Persistent;

use GeometriaLab\Model\Persistent\Collection,
    GeometriaLabTest\Model\Persistent\TestModels\Model;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testFetchRelations()
    {
        $this->setExpectedException('\RuntimeException', 'Not implemented yet!');
        $c = new Collection();
        $c->fetchRelations(array('foo'));
    }
}