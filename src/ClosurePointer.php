<?php

namespace SparkLib\SparkRouter;

class ClosurePointer
{

    /**
     * File path of route file that contain closure
     */
    public $filePath;

    /**
     * Index of staticRoutes or dynamicRoutes of pointed RouteFactory object
     */
    public $index;

    /**
     * Magic function for export object and cached files
     * @param array $array
     */
    public static function __set_state(array $array) {
        $object = new ClosurePointer;
        $object->filePath = $array['filePath'];
        $object->index = $array['index'];
        return $object;
    }

}
