<?php

class StringHelper
{
    public static function classFromDotPath($class)
    {
        if (is_array($class)) {
            $class = $class['class'];
        }

        $pos = strrpos($class, '.');
        if ($pos === false) {
            return $class;
        }

        return substr($class, $pos + 1);
    }
}
