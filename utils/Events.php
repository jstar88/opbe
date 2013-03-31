<?php
/**
 * 
 * 
 * @package   
 * @author Jstar
 * @copyright Jstar
 * @version 2013
 * @access public
 */
abstract class Events
{

    public static function event_moon($moonProb)
    {
        $SizeMin = MOON_MIN_START_SIZE + ($moonProb * MOON_MIN_FACTOR);
        $SizeMax = MOON_MAX_START_SIZE + ($moonProb * MOON_MAX_FACTOR);
        $size = rand($SizeMin, $SizeMax);
        $fields = floor(pow($size / 1000, 2));
        return array('size' => $size, 'fields' => $fields);
    }
}

?>