<?php

namespace GeometriaLabTest\Permissions\Assertion\Roles;

use GeometriaLab\Permissions\Assertion\Roles\ResourceRoles;

class RolesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SampleRoles\Roles
     */
    protected static $roles;

    public static function setUpBeforeClass()
    {
        static::$roles = new SampleRoles\Roles(array(
            'id'            => 1,
            'resourceRoles' => array(
                new ResourceRoles(array(
                    'resourceName'      => 'Foo',
                    'resourcesRoles'    => array(
                        1 => 'manager',
                        2 => 'admin',
                    ),
                    'citiesRoles'       => array(
                        1 => 'manager',
                        2 => 'admin',
                    ),
                )),
                new ResourceRoles(array(
                    'resourceName'      => 'Bar',
                    'resourcesRoles'    => array(
                        0 => 'admin',
                    ),
                    'citiesRoles'       => array(
                        1 => 'admin',
                    ),
                )),
            ),
        ));
        static::$roles->save();
    }

    public function testHasRolesForResource()
    {
        $foo1 = new SampleResource\Foo(array('id' => 1));
        $foo2 = new SampleResource\Foo(array('id' => 2));

        $this->assertTrue(static::$roles->hasRole('manager', $foo1));
        $this->assertTrue(static::$roles->hasRole('admin', $foo2));
    }

    public function testHasNonExistentRoleForResource()
    {
        $foo = new SampleResource\Foo(array('id' => 1));

        $this->assertFalse(static::$roles->hasRole('badRole', $foo));
    }

    public function testHasRoleForNonExistentResource()
    {
        $baz = new SampleResource\Baz(array('id' => 1));

        $this->assertFalse(static::$roles->hasRole('admin', $baz));
    }

    public function testHasRoleForResourceWithNonExistentId()
    {
        $foo = new SampleResource\Foo(array('id' => 3));

        $this->assertFalse(static::$roles->hasRole('admin', $foo));
    }

    public function testHasRoleForBadResource()
    {
        $this->setExpectedException('\\GeometriaLab\\Permissions\\Assertion\\Exception\\RuntimeException');

        $badModel = new SampleResource\ModelWithoutId();

        static::$roles->hasRole('admin', $badModel);
    }

    public function testHasSuperManagerRoleForResource()
    {
        $bar = new SampleResource\Bar(array('id' => 1));

        $this->assertTrue(static::$roles->hasRole('admin', $bar));
    }
}
