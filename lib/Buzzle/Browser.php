<?php

namespace Buzzle;

use Buzz\Browser as BuzzBrowser;
use Buzz\Client\ClientInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

/**
 * Extends the buzz browser.
 *
 * @author Hugo Dozois-Caouette
 * @author Ivo Bathke <ivo.bathke at gmail.com>
 */
class Browser extends BuzzBrowser {
    
    protected $cacher;
    protected $data;
    protected $fromCache = true;

    private $factory;

    public function __construct(ClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client, $factory);
        $this->factory = $this->getMessageFactory();
    }
    
    public function setCacher(Cacher $cacher) {
        $this->cacher = $cacher;
    }
        
    /**
     * Sends a request.
     *
     * @param string $url     The URL to call
     * @param string $method  The request method to use
     * @param array  $headers An array of request headers
     * @param string $content The request content
     * @param int $cacheLifetime serverside cache lifetime
     *
     * @return MessageInterface The response object
     */
    public function call($url, $method, $headers = array(), $content = '', $cacheLifetime = null) {

        if($this->cacher == null){
            return parent::call($url, $method, $headers, $content, $cacheLifetime);
        }

        $request = $this->factory->createRequest($method);

        if (!$url instanceof Url) {
            $url = new Url($url);
        }

        $url->applyToRequest($request);

        $request->addHeaders($headers);
        $request->setContent($content);

        $this->data = $this->cacher->retrieveCachedResponse($request);
        if (!$this->data) {
            $this->fromCache = false;
            $this->send($request);
            $this->data = array(
                'request' => parent::getLastRequest(),
                'response' => parent::getLastResponse()
            );
            $this->cacher->cacheResponse($this->data['request'], $this->data['response'], $cacheLifetime);
        }

        return $this->data['response'];
    }

    public function getLastRequest()
    {
        if($this->fromCache){
            return $this->data['request'];
        }
        return parent::getLastRequest();
    }

    public function getLastResponse()
    {
        if($this->fromCache){
            return $this->data['response'];
        }
        return parent::getLastResponse();
    }

    public function get($url, $headers = array(), $cacheLifetime = 0)
    {
        return $this->call($url, RequestInterface::METHOD_GET, $headers, '', $cacheLifetime);
    }

    public function post($url, $headers = array(), $content = '', $cacheLifetime = 0)
    {
        return $this->call($url, RequestInterface::METHOD_POST, $headers, $content, $cacheLifetime);
    }

    public function head($url, $headers = array(), $cacheLifetime = 0)
    {
        return $this->call($url, RequestInterface::METHOD_HEAD, $headers, '', $cacheLifetime);
    }

    
}
