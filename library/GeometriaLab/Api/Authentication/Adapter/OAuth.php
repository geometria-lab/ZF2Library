<?php

namespace GeometriaLab\Api\Authentication\Adapter;

use GeometriaLab\Api\Authentication\Event;

use OAuth2\OAuth2,
    OAuth2\OAuth2AuthenticateException;

use Zend\ServiceManager\ServiceManagerAwareInterface as ZendServiceManagerAwareInterface,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,

    Zend\EventManager\EventManagerAwareInterface as ZendEventManagerAwareInterface,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\EventManager\EventManager as ZendEventManager,
    Zend\EventManager\Event as ZendEvent,

    Zend\Authentication\Adapter\AdapterInterface as ZendAdapterInterface,
    Zend\Authentication\Result as ZendAuthenticationResult;


class OAuth implements ZendAdapterInterface, ZendEventManagerAwareInterface, ZendServiceManagerAwareInterface
{
    const AUTHENTICATE_EVENT = 'authenticate';

    /**
     * @var ZendEventManagerInterface
     */
    protected $eventManager;
    /**
     * @var ZendServiceManager
     */
    protected $serviceManager;
    /**
     * @var Event
     */
    protected $event;
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * Inject an EventManager instance
     *
     * @param ZendEventManagerInterface $eventManager
     * @return OAuth
     */
    public function setEventManager(ZendEventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(__CLASS__, get_called_class()));
        $this->eventManager = $eventManager;

        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return ZendEventManagerInterface
     */
    public function getEventManager()
    {
        if ($this->eventManager === null) {
            $this->setEventManager(new ZendEventManager());
        }

        return $this->eventManager;
    }

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
     * Get the auth event
     *
     * @return Event
     */
    public function getEvent()
    {
        if ($this->event === null) {
            $this->setEvent(new Event());
            $this->event->setTarget($this);
        }

        return $this->event;
    }

    /**
     * Set an event to use during dispatch
     *
     * By default, will re-cast to AdapterChainEvent if another event type is provided.
     *
     * @param ZendEvent $e
     * @return OAuth
     */
    public function setEvent(ZendEvent $e)
    {
        if ($e instanceof ZendEvent && !$e instanceof Event) {
            $eventParams = $e->getParams();
            $e = new Event();
            $e->setParams($eventParams);
            unset($eventParams);
        }

        $this->event = $e;

        return $this;
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
        $authResult = new ZendAuthenticationResult(
            ZendAuthenticationResult::SUCCESS,
            $data['id']
        );

        $result = $this->getEventManager()->trigger(self::AUTHENTICATE_EVENT, $this->getEvent(), $authResult);

        /*if ($result->stopped()) {
            if($result->last() instanceof Response) {
                return $result->last();
            } else {
                // throw new Exception('Auth event was stopped without a response.');
            }
        }*/

        return $authResult;
    }
}
