<?php

namespace AppBundle\Service;


use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Class AmazingRouteGenerator
 * @package AppBundle\Service
 */
class AmazingRouteGenerator extends Router
{
    private $container;

    /**
     * @param string $name
     * @param array $parameters
     * @param int $referenceType
     * @return mixed
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $result = parent::generate($name, $parameters, $referenceType);
        if ($referenceType == 0) {
            return preg_replace('/.*\.php/', $this->container->getParameter('base_url'), $result);
        }
        return preg_replace('/.*\.php/', '', $result);
    }

    public function __construct(ContainerInterface $container, $resource, array $options = array(), RequestContext $context = null)
    {
        parent::__construct($container, $resource, $options, $context);
        $this->container = $container;
    }
}