<?php

namespace GeometriaLab\Api\Authentication;

interface OAuthTokenInterface
{
    /**
     * @return bool
     */
    public function hasExpired();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @return string
     */
    public function getData();
}
