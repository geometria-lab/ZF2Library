<?php

namespace GeometriaLab\Api\Authentication;

use Zend\EventManager\Event as ZendEvent;

class Event extends ZendEvent
{
    /**
     * Get Identity
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getParam('identity');
    }

    /**
     * Set Identity
     *
     * @param mixed $identity
     * @return Event
     */
    public function setIdentity($identity = null)
    {
        if (null === $identity) {
            // Setting the identity to null resets the code and messages.
            $this->setCode();
            $this->setMessages();
        }
        $this->setParam('identity', $identity);

        return $this;
    }

    /**
     * Get Code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->getParam('code');
    }

    /**
     * Set Code
     *
     * @param int $code
     * @return Event
     */
    public function setCode($code = null)
    {
        $this->setParam('code', $code);

        return $this;
    }

    /**
     * Get Messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->getParam('messages') ?: array();
    }

    /**
     * Set Messages
     *
     * @param array $messages
     * @return Event
     */
    public function setMessages($messages = array())
    {
        $this->setParam('messages', $messages);

        return $this;
    }
}
