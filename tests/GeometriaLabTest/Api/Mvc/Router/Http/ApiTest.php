<?php

namespace GeometriaLabTest\Api\Mvc\Router\Http;

use GeometriaLab\Api\Mvc\Router\Http\Api as ApiRouter;

use Zend\Http\PhpEnvironment\Request as ZendRequest,
    Zend\Stdlib\Parameters as ZendParameters,
    Zend\Http\Headers as ZendHeaders;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    public function testGetList()
    {
        $request = new ZendRequest();
        $request->setUri('/v1/foo');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('getList'), $routeMatch->getParams());
    }

    public function testGetListWithSubResource()
    {
        $request = new ZendRequest();
        $request->setUri('/v1/foo/sub');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('getSubList'), $routeMatch->getParams());
    }

    public function testGet()
    {
        $request = new ZendRequest();
        $request->setUri('/v1/foo/123');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('get', '123'), $routeMatch->getParams());
    }

    public function testGetWithSubResource()
    {
        $request = new ZendRequest();
        $request->setUri('/v1/foo/123/sub');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('getSub', '123'), $routeMatch->getParams());
    }

    public function testPost()
    {
        $request = new ZendRequest();
        $request->setMethod('post')->setUri('/v1/foo');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('create'), $routeMatch->getParams());
    }

    public function testPostWithSubResource()
    {
        $request = new ZendRequest();
        $request->setMethod('post')->setUri('/v1/foo/sub-resource');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('subResource'), $routeMatch->getParams());
    }

    public function testPostWithId()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Post is allowed on resources only');

        $request = new ZendRequest();
        $request->setMethod('post')->setUri('/v1/foo/123');

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testPut()
    {
        $request = new ZendRequest();
        $request->setMethod('put')->setUri('/v1/foo/123');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('update', '123'), $routeMatch->getParams());
    }

    public function testPutWithoutId()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Missing identifier');

        $request = new ZendRequest();
        $request->setMethod('put')->setUri('/v1/foo');

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testPutWithSubResource()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Put is allowed on root resource object only');

        $request = new ZendRequest();
        $request->setMethod('put')->setUri('/v1/foo/123/sub');

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testDelete()
    {
        $request = new ZendRequest();
        $request->setMethod('delete')->setUri('/v1/foo/123');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('delete', '123'), $routeMatch->getParams());
    }

    public function testDeleteWithoutId()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Missing identifier');

        $request = new ZendRequest();
        $request->setMethod('delete')->setUri('/v1/foo');

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testDeleteWithSubResource()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Delete is allowed on root resource object only');

        $request = new ZendRequest();
        $request->setMethod('delete')->setUri('/v1/foo/123/sub');

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testOverrideMethodFromQuery()
    {
        $request = new ZendRequest();
        $request->setMethod('get')->setUri('/v1/foo/123');
        $request->setQuery(new ZendParameters(array(
            '_method' => 'delete',
        )));


        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('delete', '123'), $routeMatch->getParams());
    }

    public function testOverrideMethodFromHeader()
    {
        $request = new ZendRequest();
        $request->setMethod('get')->setUri('/v1/foo/123');

        $headers = new ZendHeaders();
        $headers->addHeaderLine('X-HTTP-METHOD-OVERRIDE', 'delete');
        $request->setHeaders($headers);


        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('delete', '123'), $routeMatch->getParams());
    }

    public function testBadMethod()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Invalid HTTP method!');

        $request = new ZendRequest();
        $request->setUri('/v1/foo/123/sub');
        $request->setQuery(new ZendParameters(array(
            '_method' => 'bad',
        )));

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testSetFormat()
    {
        $request = new ZendRequest();
        $request->setUri('/v1/foo.xml');

        $router = new ApiRouter();
        $router->match($request);

        $this->assertEquals('xml', $request->getMetadata('format'));
    }

    public function testBadSubResource()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\BadRequestException', 'Invalid sub resource name');

        $request = new ZendRequest();
        $request->setUri('/v1/foo/123/@');

        $router = new ApiRouter();
        $router->match($request);
    }

    public function testBadApiVersion()
    {
        $request = new ZendRequest();
        $request->setUri('/bad/foo');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('getList', '', ''), $routeMatch->getParams());
    }

    public function testBadController()
    {
        $request = new ZendRequest();
        $request->setUri('/v1/@');

        $router = new ApiRouter();
        $routeMatch = $router->match($request);

        $this->assertEquals($this->getExpectedData('getList', '', 'Api\V1\Controller', ''), $routeMatch->getParams());
    }

    protected function getExpectedData($action, $id = '', $namespace = 'Api\V1\Controller', $controller = 'Foo')
    {
        return array(
            'id'            => $id,
            '__NAMESPACE__' => $namespace,
            'controller'    => $controller,
            'action'        => $action,
        );
    }
}
