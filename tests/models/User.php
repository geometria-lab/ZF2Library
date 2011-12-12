<?php

/**
 * @property        $id
 * @property string $firstName  { "defaultValue" : "Ivan" }
 * @property string $secondName { "defaultValue" : "Shumkov" }
 *
 * @mapper mongo { "connection" : "default", "collection" : "UserName" }
 */
class User extends GeometriaLab_Model_Persistent
{

}