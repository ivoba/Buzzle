<?php
namespace Buzzle\Test\Unit;

use Buzzle\Browser;

class BrowserTest extends \PHPUnit_Framework_TestCase{

    private $client;
    private $factory;
    private $browser;

    protected function setUp()
    {
        $this->client = $this->getMock('Buzz\Client\ClientInterface');
        $this->factory = $this->getMock('Buzz\Message\Factory\FactoryInterface');
        $this->browser = new Browser($this->client, $this->factory);
    }

    /**
     * we run here the Buzz test again just to make sure
     *
     * @dataProvider provideMethods
     */
    public function testBasicMethods($method, $content)
    {
        $headers = array('X-Foo: bar');
        $request = $this->getMock('Buzz\Message\RequestInterface');
        $response = $this->getMock('Buzz\Message\MessageInterface');
        $this->factory->expects($this->once())
            ->method('createRequest')
            ->with(strtoupper($method))
            ->will($this->returnValue($request));
        $request->expects($this->once())
            ->method('setHost')
            ->with('http://google.com');
        $request->expects($this->once())
            ->method('setResource')
            ->with('/');
        $request->expects($this->once())
            ->method('addHeaders')
            ->with($headers);
        $request->expects($this->once())
            ->method('setContent')
            ->with($content);
        $this->factory->expects($this->once())
            ->method('createResponse')
            ->will($this->returnValue($response));
        $this->client->expects($this->once())
            ->method('send')
            ->with($request, $response);

        $actual = $this->browser->$method('http://google.com/', $headers, $content);

        $this->assertSame($response, $actual);
    }

    public function provideMethods()
    {
        return array(
            array('get',    ''),
            array('head',   ''),
            array('post',   'content'),
            array('put',    'content'),
            array('delete', 'content'),
        );
    }

} 