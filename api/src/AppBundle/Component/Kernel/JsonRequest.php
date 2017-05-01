<?php

namespace AppBundle\Component\Kernel;


use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonRequest
 * @package AppBundle\Component\Kernel
 */
class JsonRequest extends Request
{
    /**
     * @param string $uri
     * @param string $method
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return static
     */
    public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {

        $jsonRequest = json_decode(key($method), true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return new static($uri, $jsonRequest, $parameters, $cookies, $files, $server, $content);
        }
        return new static($uri, $method, $parameters, $cookies, $files, $server, $content);
    }
}