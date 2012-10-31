<?php

namespace GeometriaLabTest\Api\Authentication\Adapter;

use GeometriaLabTest\Api\Authentication\Adapter\TestModel\AccessToken;

use GeometriaLab\Api\Authentication\Adapter\OAuth as AuthenticationAdapter;

use Zend\Http\PhpEnvironment\Request as ZendRequest,
    Zend\Http\Headers as ZendHeaders,
    Zend\Stdlib\Parameters as ZendParameters,
    Zend\Authentication\Result as ZendAuthenticationResult;

class OAuthTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEmptyToken()
    {
        $request = new ZendRequest();
        $token = AuthenticationAdapter::getBearerToken($request);

        $this->assertNull($token);
    }

    public function testGetTokenFromQuery()
    {
        $request = new ZendRequest();
        $request->setQuery(new ZendParameters(array(
            'access_token' => '123456qwerty',
        )));

        $token = AuthenticationAdapter::getBearerToken($request);

        $this->assertEquals('123456qwerty', $token);
    }

    public function testGetTokenFromPost()
    {
        $request = new ZendRequest();
        $request->setPost(new ZendParameters(array(
            'access_token' => '123456qwerty',
        )));

        $token = AuthenticationAdapter::getBearerToken($request);

        $this->assertEquals('123456qwerty', $token);
    }

    public function testGetTokenFromHeaders()
    {
        $headers = new ZendHeaders();
        $headers->addHeaderLine('Authorization', 'Bearer 123456qwerty');

        $request = new ZendRequest();
        $request->setHeaders($headers);

        $token = AuthenticationAdapter::getBearerToken($request);

        $this->assertEquals('123456qwerty', $token);
    }

    public function testGetTokenFromHeaderFirst()
    {
        $headers = new ZendHeaders();
        $headers->addHeaderLine('Authorization', 'Bearer Header');

        $request = new ZendRequest();
        $request->setHeaders($headers);
        $request->setQuery(new ZendParameters(array(
            'access_token' => 'Query',
        )));
        $request->setPost(new ZendParameters(array(
            'access_token' => 'Post',
        )));

        $token = AuthenticationAdapter::getBearerToken($request);

        $this->assertEquals('Header', $token);
    }

    public function testAuthenticateWithEmptyToken()
    {
        $adapter = new AuthenticationAdapter();
        $result = $adapter->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);

        $expected = new ZendAuthenticationResult(
            ZendAuthenticationResult::FAILURE_CREDENTIAL_INVALID,
            null,
            array('The access token provided is invalid.')
        );

        $this->assertEquals($expected, $result);
    }

    public function testAuthenticateWithExpiredToken()
    {
        $token = new AccessToken(array(
            'id'        => '123456qwerty',
            'clientId'  => '1',
            'expiresAt' => time() - 10,
        ));

        $adapter = new AuthenticationAdapter();
        $result = $adapter->setAccessToken($token)->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);

        $expected = new ZendAuthenticationResult(
            ZendAuthenticationResult::FAILURE,
            null,
            array('The access token provided has expired.')
        );

        $this->assertEquals($expected, $result);
    }

    public function testAuthenticateWithoutScope()
    {
        $token = new AccessToken(array(
            'id'        => '123456qwerty',
            'clientId'  => '1',
            'expiresAt' => time() + 10,
            'data'      => '1'
        ));

        $adapter = new AuthenticationAdapter();

        $result = $adapter->setAccessToken($token)->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);

        $expected = new ZendAuthenticationResult(
            ZendAuthenticationResult::SUCCESS,
            '1'
        );

        $this->assertEquals($expected, $result);
    }

    public function testAuthenticateWithBadScope()
    {
        $token = new AccessToken(array(
            'id'        => '123456qwerty',
            'clientId'  => '1',
            'expiresAt' => time() + 10,
            'scope'     => 'Foo',
        ));

        $adapter = new AuthenticationAdapter();

        $result = $adapter->setScope('Bar')->setAccessToken($token)->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);

        $expected = new ZendAuthenticationResult(
            ZendAuthenticationResult::FAILURE,
            null,
            array('The request requires higher privileges than provided by the access token.')
        );

        $this->assertEquals($expected, $result);
    }

    public function testAuthenticateWithScope()
    {
        $token = new AccessToken(array(
            'id'        => '123456qwerty',
            'clientId'  => '1',
            'expiresAt' => time() + 10,
            'scope'     => 'Foo',
            'data'      => '1'
        ));

        $adapter = new AuthenticationAdapter();

        $result = $adapter->setScope('Foo')->setAccessToken($token)->authenticate();

        $this->assertInstanceOf('\Zend\Authentication\Result', $result);

        $expected = new ZendAuthenticationResult(
            ZendAuthenticationResult::SUCCESS,
            '1'
        );

        $this->assertEquals($expected, $result);
    }
}
