<?php
namespace Buzzle\Test;

use Buzz\Client\Curl;
use Buzz\Message\Factory\Factory;
use Buzzle\Browser;

require_once __DIR__ . '/../../../lib/Buzzle/Browser.php';

//TODO extend Buzz TestCase
class BrowserTest extends \PHPUnit_Framework_TestCase{

//    private $client;
//    private $factory;
    private $browser;

    protected function setUp()
    {
//        $this->client = $this->getMock('Buzz\Client\ClientInterface');
//        $this->factory = $this->getMock('Buzz\Message\Factory\FactoryInterface');
//        $this->browser = new Browser($this->client, $this->factory);
          $this->browser = new Browser(new Curl(), new Factory());
          /*
          TODO create Cacher, set cacher
          TODO clear cache
          */
    }

    /**
     * @dataProvider provideMethods
     */
    public function testBasicMethods($method, $content)
    {
        $headers = array('X-Foo: bar');
        $actual = $this->browser->$method('http://google.com/', $headers, $content);
        $this->assertSame(200, $actual->getStatusCode());
    }

    public function provideMethods()
    {
        return array(
            array('get',    ''),
            array('head',   ''),
//            array('post',   'content'),
//            array('put',    'content'),
//            array('delete', 'content'),
        );
    }

} 