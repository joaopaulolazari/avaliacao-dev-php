<?php

namespace AppBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

class TypeHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $methods = array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Object',
                'format' => 'json',
                'method' => 'objectHandler',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Geometry',
                'format' => 'json',
                'method' => 'geometryHandler',
            ),
        );

        return $methods;
    }

    public function objectHandler(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        return (object) $visitor->visitArray($collection->toArray(), $type, $context);
    }

    /**
     * Formata campos do tipo geometry para array
     *
     * @param  VisitorInterface $visitor
     * @param  Polygon          $polygon
     * @param  array            $type
     * @param  Context          $context
     * @return array                        array formatado contendo coordenadas
     */
    public function geometryHandler(VisitorInterface $visitor, Polygon $polygon, array $type, Context $context)
    {
        $geometrys = $polygon->toArray();
        if (empty($geometrys)) {
            return array();
        }
        return $geometrys[0];
    }
}
