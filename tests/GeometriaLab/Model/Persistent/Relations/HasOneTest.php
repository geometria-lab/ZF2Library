<?php

namespace GeometriaLabTest\Model\Persistent\Relations;

use GeometriaLab\Model\Persistent\Model,
    GeometriaLab\Model\Persistent\Schema\Schema;

class HasOneTest extends \PHPUnit_Framework_TestCase
{
    protected $man;
    protected $woman;

    public function setUp()
    {
        $manSchema = new Schema();
        $manSchema->

        $this->man = new Model();
        $this->man->setSchema();
    }
}
