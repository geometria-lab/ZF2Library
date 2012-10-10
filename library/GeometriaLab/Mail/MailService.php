<?php

namespace GeometriaLab\Mail;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\Mail\Message as ZendMessage,
    Zend\Mail\Transport\Sendmail as ZendSendmail;

class MailService implements ZendFactoryInterface
{
    /**
     * @var array
     */
    private $config = array();
    /**
     * @var string
     */
    private $encoding;
    /**
     * @var string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable
     */
    private $from;
    /**
     * @var string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable
     */
    private $to;
    /**
     * @var string
     */
    private $subject;
    /**
     * @var string
     */
    private $body;

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return MailService
     * @throws \InvalidArgumentException
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (!isset($config['mail'])) {
            throw new \InvalidArgumentException('Need "mail" param in config');
        }

        $this->setConfig($config['mail']);

        return $this;
    }

    /**
     * @param $config
     * @return MailService
     * @throws \InvalidArgumentException
     */
    public function setConfig($config)
    {
        $this->config = $config;

        foreach ($this->config as $name => $value) {
            $method = "set{$name}";
            if (!method_exists($this, $method)) {
                throw new \InvalidArgumentException("Undefined param '$name'");
            }
            $this->$method($value);
        }

        return $this;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $from
     * @return MailService
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string|\Zend\Mail\Address\AddressInterface|array|\Zend\Mail\AddressList|\Traversable $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return array|string|\Traversable|\Zend\Mail\Address\AddressInterface|\Zend\Mail\AddressList
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send mail
     */
    public function send()
    {
        $mail = new ZendMessage();
        $mail->setEncoding($this->getEncoding());
        $mail->setFrom($this->getFrom());
        $mail->setTo($this->getTo());
        $mail->setSubject($this->getSubject());
        $mail->setBody($this->getBody());

        $transport = new ZendSendmail();
        $transport->send($mail);
    }
}