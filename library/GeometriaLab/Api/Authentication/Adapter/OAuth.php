<?php

namespace GeometriaLab\Api\Authentication\Adapter;

use OAuth2\OAuth2,
    OAuth2\OAuth2AuthenticateException;

use Zend\ServiceManager\ServiceManagerAwareInterface as ZendServiceManagerAwareInterface,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\Authentication\Adapter\AdapterInterface as ZendAdapterInterface,
    Zend\Authentication\Result as ZendAuthenticationResult;


class OAuth implements ZendAdapterInterface, ZendServiceManagerAwareInterface
{
    const AUTHENTICATE_EVENT = 'authenticate';

    /**
     * @var ZendServiceManager
     */
    protected $serviceManager;
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * Set Service Manager
     *
     * @param ZendServiceManager $serviceManager
     * @return OAuth
     */
    public function setServiceManager(ZendServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Get Service Manager
     *
     * @return ZendServiceManager
     * @throws \RuntimeException
     */
    public function getServiceManager()
    {
        if ($this->serviceManager === null) {
            throw new \RuntimeException('Need set Service Manager');
        }

        return $this->serviceManager;
    }

    /**
     * Set access token
     *
     * @param string $accessToken
     * @return OAuth
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Performs an authentication attempt
     *
     * @return ZendAuthenticationResult
     */
    public function authenticate()
    {
        /* @var OAuth2 $Oauth */
        $Oauth = $this->getServiceManager()->get('Oauth');

        try {
            /* @var \OAuth2\Model\IOAuth2Token $accessToken */
            $accessToken = $Oauth->verifyAccessToken($this->getAccessToken());
        } catch (OAuth2AuthenticateException $ex) {

        }

        $data = $accessToken->getData();

        return new ZendAuthenticationResult(
            ZendAuthenticationResult::SUCCESS,
            $data['id']
        );
    }
}
