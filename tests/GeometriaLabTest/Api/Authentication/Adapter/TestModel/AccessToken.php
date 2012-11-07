<?php

namespace GeometriaLabTest\Api\Authentication\Adapter\TestModel;

use GeometriaLab\Api\Authentication\OAuthTokenInterface,
    GeometriaLab\Model\AbstractModel;

/**
 * @property string     $id
 * @property string     $clientId
 * @property integer    $expiresAt
 * @property string     $data
 */
class AccessToken extends AbstractModel implements OAuthTokenInterface
{
    /**
     * @return bool
     */
    public function hasExpired()
    {
        return time() > $this->expiresAt;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getData()
    {
        if (isset($this->propertyValues['data'])) {
            return $this->propertyValues['data'];
        }

        return null;
    }
}
