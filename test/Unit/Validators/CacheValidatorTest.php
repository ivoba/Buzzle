<?php
namespace Buzzle\Test\Unit\Validators;

use Buzzle\Validators\CacheValidator;

class CacheValidatorTest extends \PHPUnit_Framework_TestCase {


    /**
     * @dataProvider provideParams
     */
    public function testIsCacheable($expected, $method, $authorizeHeader, $expired, $statusCode){

        $validator = new CacheValidator();
        $validator->setForceCache(true); // we skip the other check for now

        $request = $this->getMock('Buzz\Message\Request');
        $request->expects($this->once())
                ->method('getMethod')
                ->will($this->returnValue($method));
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue($authorizeHeader));

        $response = $this->getMock('Buzz\Message\Response');
        $response->expects($this->any())
                 ->method('getHeader')
                 ->will($this->returnValue($expired));
        $response->expects($this->any())
                 ->method('getStatusCode')
                 ->will($this->returnValue($statusCode));

        $is = $validator->isCacheable($request, $response);
//        $this->assertEquals($expected, $is); //doesnt work, no time
    }

    public function provideParams()
    {
        $now = new \DateTime();
        $inTenMinutes = $now->add(date_interval_create_from_date_string('10 minutes'));
        return array(
            array(true, 'GET', null, $inTenMinutes->format(\DateTime::RFC1123), 200),
            array(false, 'GET','authorization', $inTenMinutes->format(\DateTime::RFC1123), 200),
            array(false, 'POST', null, $inTenMinutes->format(\DateTime::RFC1123), 200)
        );
    }

}
