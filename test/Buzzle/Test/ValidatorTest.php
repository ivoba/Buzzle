<?php

namespace Buzzle\Test;

use Buzzle\Validators\CacheValidator;
use Buzzle\Validators\CacheValidatorInterface;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{

    private $client;
    private $factory;

    protected function setUp()
    {
        $this->client = $this->getMock('Buzz\Client\ClientInterface');
        $this->factory = $this->getMock('Buzz\Message\Factory\FactoryInterface');
    }

    /**
     * @dataProvider provideMethods
     */
    public function testValidator($method, $content)
    {

        $request = $this->getMock('Buzz\Message\Request'); //Interface
        $response = $this->getMock('Buzz\Message\Response');

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));
        $request->expects($this->once())
            ->method('getHeader')
            ->will($this->returnValue(null));

        $expires = new \DateTime('tomorrow');
        $response->expects($this->once())
            ->method('getHeader')
            ->will($this->returnValue($expires->format('c')));

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));


        $validator = new CacheValidator();
        $validator->setForceCache(true);

        $check = $validator->isCacheable($request, $response);
        $this->assertTrue($check);

    }

    public function provideMethods()
    {
        return array(
            array('get',    '')
        );
    }
} 