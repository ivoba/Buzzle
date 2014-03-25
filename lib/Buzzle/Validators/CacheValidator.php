<?php

namespace Buzzle\Validators;

use Buzz\Message\Response;
use Buzz\Message\Request;

/**
 * This valids if a buzz response is cacheable or not.
 *
 * @todo use Interfaces of Request and Response
 * @author Hugo Dozois-Caouette
 * @author Ivo Bathke <ivo.bathke at gmail.com>
 */
class CacheValidator implements CacheValidatorInterface
{

    protected static $CACHEABLE_HTTP_METHODS = array('GET', 'HEAD');
    protected static $CACHEABLE_STATUS_CODES = array('200', '203', '204', '205', '300', '301', '410');

    private $forceCache = false;

    /**
     *
     * @param \Buzz\Message\Request $request
     * @param \Buzz\Message\Response $response
     * @return boolean
     */
    public function isCacheable(Request $request, Response $response)
    {
        if ($this->isRequestCacheable($request) && $this->isResponseCacheable($response)) {
            return true;
        }

        //add more
        return false;
    }

    /**
     * @param Response $response
     * @param int $minFresh
     * @return bool
     */
    public function isExpired(Response $response, $minFresh = 5)
    {
        $expires = $response->getHeader('expires');
        $parsedExpires = strtotime($expires);
        if ($expires !== null) {
            if ($parsedExpires === false || (time() + $minFresh) > $parsedExpires) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param \Buzz\Message\Request $request
     * @return boolean
     */
    public function isRequestCacheable(Request $request)
    {
        if (!$this->isHTTPMethodCacheable($request->getMethod())) {
            return false;
        }

        //[rfc2616-14.8]
        if ($request->getHeader("authorization")) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Buzz\Message\Response $response
     * @return boolean
     */
    public function isResponseCacheable(Response $response)
    {
        if ($this->isExpired($response)) {
            return false;
        }
        if (!$this->isStatusCodeCacheable($response->getStatusCode())) {
            return false;
        }

        if (!$this->forceCache) {
            if ($response->getHeader('etag')) {
                return false;
            }
            if ($response->getHeader('vary')) {
                return false;
            }

            if (!$this->isCacheControlCacheable($response->getHeader('cache-control'))) {
                return false;
            }
            $pragma = $response->getHeader('pragma');
            if ($pragma == 'no-cache' || strpos($pragma, 'no-cache') !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param string $statusCode
     * @return boolean
     */
    public function isStatusCodeCacheable($statusCode)
    {
        if (!in_array($statusCode, self::$CACHEABLE_STATUS_CODES)) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $HTTPMethod
     * @return boolean
     */
    public function isHTTPMethodCacheable($HTTPMethod)
    {
        if (!in_array($HTTPMethod, self::$CACHEABLE_HTTP_METHODS)) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $cacheControl
     * @return boolean
     */
    public function isCacheControlCacheable($cacheControl)
    {
        // parse CacheControl
        $pCC = $this->parseCacheControl($cacheControl);

        if (isset($pCC['private']) || isset($pCC['no-store']) || isset($pCC['no-cache'])) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param type $cacheControl
     * @return array
     */
    private function parseCacheControl($cacheControl)
    {
        $arrayCacheControl = explode(', ', $cacheControl);
        $parsedCC = array();
        foreach ($arrayCacheControl as $value) {
            $pos = strpos($value, '=');
            if ($pos !== false) {
                $parsedCC[substr($value, 0, $pos)] = substr($value, $pos + 1);
            } else {
                $parsedCC[$value] = true;
            }
        }
        return $parsedCC;
    }

    /**
     * sometimes we need to ignore the response directives
     *
     * @param boolean $forceCache
     */
    public function setForceCache($forceCache)
    {
        $this->forceCache = $forceCache;
    }

    /**
     * @return boolean
     */
    public function getForceCache()
    {
        return $this->forceCache;
    }

}
