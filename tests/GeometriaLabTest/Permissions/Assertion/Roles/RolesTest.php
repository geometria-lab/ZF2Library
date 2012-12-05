<?php

namespace GeometriaLabTest\Permissions\Assertion\Roles;

use GeometriaLabTest\Permissions\Assertion\SampleResource\Bar as BarResource;

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
                        0 => 'admin',
                    ),
                )),
                new ResourceRoles(array(
                    'resourceName'      => 'Bar',
                    'resourcesRoles'    => array(
                        0 => 'admin',
                    ),
                    'citiesRoles'       => array(
                        1 => 'manager',
                        2 => 'admin',
                    ),
                )),
            ),
        ));
        static::$roles->save();
    }

    public function testHasRolesForResource()
    {
        $foo1 = new SampleModel\Foo(array('id' => 1));
        $foo2 = new SampleModel\Foo(array('id' => 2));

        $this->assertTrue(static::$roles->hasRole('manager', $foo1));
        $this->assertTrue(static::$roles->hasRole('admin', $foo2));
    }

    public function testHasNonExistentRoleForResource()
    {
        $foo = new SampleModel\Foo(array('id' => 1));

        $this->assertFalse(static::$roles->hasRole('badRole', $foo));
    }

    public function testHasRoleForNonExistentResource()
    {
        $baz = new SampleModel\Baz(array('id' => 1));

        $this->assertFalse(static::$roles->hasRole('admin', $baz));
    }

    public function testHasRoleForResourceWithNonExistentId()
    {
        $foo = new SampleModel\Foo(array('id' => 3));

        $this->assertFalse(static::$roles->hasRole('admin', $foo));
    }

    public function testHasRoleForBadResource()
    {
        $this->setExpectedException('\\GeometriaLab\\Permissions\\Assertion\\Exception\\RuntimeException');

        $badModel = new SampleModel\ModelWithoutId();

        static::$roles->hasRole('admin', $badModel);
    }

    public function testHasSuperManagerRoleForResource()
    {
        $bar = new SampleModel\Bar(array('id' => 1));

        $this->assertTrue(static::$roles->hasRole('admin', $bar));
    }

    public function testHasRolesForResourceInCity()
    {
        $this->assertTrue(static::$roles->hasRoleInCity('manager', 1, 'Bar'));
        $this->assertTrue(static::$roles->hasRoleInCity('admin', 2, 'Bar'));
    }

    public function testHasRolesForResourceInterfaceInCity()
    {
        $bar = new BarResource('Bar');
        $this->assertTrue(static::$roles->hasRoleInCity('manager', 1, $bar));
    }

    public function testHasNonExistentRoleForResourceInCity()
    {
        $this->assertFalse(static::$roles->hasRoleInCity('badRole', 1, 'Bar'));
    }

    public function testHasRoleForNonExistentResourceInCity()
    {
        $this->assertFalse(static::$roles->hasRoleInCity('admin', 1, 'Baz'));
    }

    public function testHasRoleForResourceWithNonExistentCityIdInCity()
    {
        $this->assertFalse(static::$roles->hasRoleInCity('admin', 3, 'Bar'));
    }

    public function testHasSuperManagerRoleForResourceInCity()
    {
        $this->assertTrue(static::$roles->hasRoleInCity('admin', 1, 'Foo'));
    }
}
