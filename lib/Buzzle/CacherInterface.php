<?php

namespace Buzzle;


use Buzz\Message\Request;
use Buzz\Message\Response;

interface CacherInterface
{

    /**
     *
     * @param \Buzz\Message\Request $request
     * @return array|false array(request,response) if it exists, false if the request is not cached
     */
    public function retrieveCachedResponse(Request $request);

    /**
     * @param Request $request
     * @param Response $response
     * @param null $lifetime
     */
    public function cacheResponse(Request $request, Response $response, $lifetime = null);

} 