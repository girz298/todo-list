<?php

namespace AppBundle\Service;


use Symfony\Bundle\FrameworkBundle\Routing\Router;

class AmazingRouteGenerator extends Router
{
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $result = parent::generate($name, $parameters, $referenceType);
        return preg_replace('/.*\.php/','http://localhost',$result);;
    }
}