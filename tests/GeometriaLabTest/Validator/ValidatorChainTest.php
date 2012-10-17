<?php

namespace GeometriaLabTest\Validator;

use GeometriaLab\Validator\ValidatorChain;

use Zend\Validator\Digits as DigitsValidator,
    Zend\Validator\EmailAddress as EmailAddressValidator;

class ValidatorChainTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanupMessages()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->isValid('Foo');

        $expected = array(
            'emailAddressInvalidFormat' => 'The input is not a valid email address. Use the basic format local-part@hostname'
        );

        $this->assertEquals($expected, $validatorChain->getMessages());

        $validatorChain->cleanupMessages();

        $this->assertEmpty($validatorChain->getMessages());
    }

    public function testAddValidatorByIndexWithNegativeIndex()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Index too small');

        $validatorChain = new ValidatorChain();
        $validatorChain->addValidatorByIndex(-1, new DigitsValidator());
    }

    public function testAddValidatorByIndexWithBigIndex()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Index too large');

        $validatorChain = new ValidatorChain();
        $validatorChain->addValidatorByIndex(2, new DigitsValidator());
    }

    public function testAddValidatorByIndexToTheBeginning()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidatorByIndex(0, new DigitsValidator());

        $validators = $validatorChain->getValidators();

        $this->assertInstanceOf('\Zend\Validator\Digits', $validators[0]['instance']);
    }

    public function testAddValidatorByIndexToTheMiddle()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidatorByIndex(1, new DigitsValidator());

        $validators = $validatorChain->getValidators();

        $this->assertInstanceOf('\Zend\Validator\Digits', $validators[1]['instance']);
    }

    public function testAddValidatorByIndexToTheEnd()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidatorByIndex(2, new DigitsValidator());

        $validators = $validatorChain->getValidators();

        $this->assertInstanceOf('\Zend\Validator\Digits', $validators[2]['instance']);
    }

    public function testRemoveValidatorByIndexWithBadIndex()
    {
        $this->setExpectedException('\InvalidArgumentException', "Invalid index '2'");

        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->removeValidatorByIndex(2);
    }

    public function testRemoveValidatorByIndexFromTheBeginning()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new DigitsValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->removeValidatorByIndex(0);

        $validators = $validatorChain->getValidators();

        $this->assertCount(2, $validators);

        foreach ($validators as $validatorData) {
            $this->assertInstanceOf('\Zend\Validator\EmailAddress', $validatorData['instance']);
        }
    }

    public function testRemoveValidatorByIndexFromTheEnd()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new DigitsValidator());
        $validatorChain->removeValidatorByIndex(2);

        $validators = $validatorChain->getValidators();

        $this->assertCount(2, $validators);

        foreach ($validators as $validatorData) {
            $this->assertInstanceOf('\Zend\Validator\EmailAddress', $validatorData['instance']);
        }
    }

    public function testRemoveValidatorByIndexFromTheMiddle()
    {
        $validatorChain = new ValidatorChain();
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new DigitsValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->addValidator(new EmailAddressValidator());
        $validatorChain->removeValidatorByIndex(2);

        $validators = $validatorChain->getValidators();

        $this->assertCount(4, $validators);

        foreach ($validators as $validatorData) {
            $this->assertInstanceOf('\Zend\Validator\EmailAddress', $validatorData['instance']);
        }
    }
}
