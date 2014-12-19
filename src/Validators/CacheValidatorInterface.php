<?php

namespace Buzzle\Validators;

use Buzz\Message\Response;
use Buzz\Message\Request;

/**
 *
 * @author Ivo Bathke <ivo.bathke at gmail.com>
 * 
 * The class implementing this interface provides a way to check if a request and its reponse is cacheable or not.
 */
interface CacheValidatorInterface {

    /**
     * @param Request $request The request
     * @param Response $response The response
     * @return boolean
     */
    public function isCacheable(Request $request, Response $response);

    /**
     * @param Request $request The request
     * @return boolean
     */
    public function isRequestCacheable(Request $request);

    /**
     * @param Response $response The response
     * @return boolean
     */
    public function isResponseCacheable(Response $response);

    /**
     * 
     * @param Response $response The response
     * @return boolean
     */
    public function isExpired(Response $response);
}