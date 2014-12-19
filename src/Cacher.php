<?php

namespace Buzzle;

use Buzz\Message\Request;
use Buzz\Message\Response;
use Buzzle\Validators\CacheValidatorInterface;
use Doctrine\Common\Cache\Cache;

/**
 * Cacher validates if an entry is cached or can be cached, and then caches it.
 * The two classes it uses can be changed as long as they implement the correct interfaces
 *
 * @author Hugo Dozois-Caouette
 * @author Ivo Bathke <ivo.bathke at gmail.com>
 */
class Cacher implements CacherInterface
{

    /**
     *
     * @var Cache
     */
    private $cache;

    /**
     *
     * @var CacheValidatorInterface
     */
    private $validator;

    function __construct(Cache $cache, CacheValidatorInterface $validator)
    {
        $this->cache = $cache;
        $this->validator = $validator;
    }

    /**
     *
     * @param \Buzz\Message\Request $request
     * @return array|false array(request,response) if it exists, false if the request is not cached
     */
    public function retrieveCachedResponse(Request $request)
    {
        if ($this->validator->isRequestCacheable($request)) {
            $key = $this->buildKey($request);
            $data = unserialize($this->cache->fetch($key));
            if ($data && !$this->validator->isExpired($data['response'])) {
                $header = 'X-Buzzle-Cache: fresh';
                if ($this->validator->getForceCache()) {
                    $header .= '/forced';
                }
                $data['response']->addHeader($header);
                return $data;
            } else {
                $this->cache->delete($key);
            }
        }
        return false;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param null $lifetime
     */
    public function cacheResponse(Request $request, Response $response, $lifetime = null)
    {
        if ($this->validator->isCacheable($request, $response) && is_numeric($lifetime)) {
            $key = $this->buildKey($request);
            $this->cache->save($key, serialize(array('request' => $request, 'response' => $response)), $lifetime);
        }
    }

    /**
     *
     * @param \Buzz\Message\Request $request
     * @return string the key specific to the request
     */
    protected function buildKey(Request $request)
    {
        return md5(serialize($request));
    }

}
