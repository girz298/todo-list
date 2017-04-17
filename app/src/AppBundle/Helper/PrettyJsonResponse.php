<?php

namespace AppBundle\Helper;


use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class PrettyJsonResponse
 * @package AppBundle\Helper
 */
class PrettyJsonResponse extends JsonResponse
{
    /**
     * @inheritdoc
     */
    public function __construct($data = null, $status = 200, array $headers = array(), $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
        $this->setEncodingOptions(JSON_PRETTY_PRINT);
    }
}