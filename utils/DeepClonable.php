<?php
/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version alpha(2013-2-4)
 * @link https://github.com/jstar88/opbe
 */
class DeepClonable implements Iterator
{
    public static $useSerialization = true;
    public function __clone()
    {
        if (self::$useSerialization)
        {
            foreach ($this as $name => $value)
            {
                if (is_object($value) || (is_array($value)))
                {
                    $this->{$name} = unserialize(serialize($value));
                }
            }
        }
        else
        {
            foreach ($this as $name => $value)
            {
                //echo 'cloning ' . get_class($this) . ':' . $name . '<br>';
                if (is_object($value))
                {
                    $this->$name = clone $value;
                }
                elseif (is_array($value))
                {
                    $this->$name = $this->arrayClone($value);
                }
            }
        }
    }
    private function arrayClone($value)
    {
        $array = array();
        if ($this->is_assoc($value))
        {
            foreach ($value as $id => $content)
            {
                if (is_object($content))
                {
                    $array[$id] = clone $content;
                }
                elseif (is_array($content))
                {
                    $array[$id] = $this->arrayClone($content);
                }
                else
                {
                    $array[$id] = $content;
                }
            }
        }
        else
        {
            foreach ($value as $content)
            {
                if (is_object($content))
                {
                    $array[] = clone $content;
                }
                elseif (is_array($content))
                {
                    $array[] = $this->arrayClone($content);
                }
                else
                {
                    $array[] = $content;
                }
            }
        }
        return $array;
    }
    public static function cloneIt($var)
    {
        return unserialize(serialize($var));
    }
    public function cloneIt2($var)
    {
        return eval('return ' . var_export($var, true) . ';');
    }
    private function is_assoc($array)
    {
        //return (bool)count(array_filter(array_keys($array), 'is_string'));
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /* Iterator functions */

    public function rewind()
    {
        reset($this->array);
    }

    public function current()
    {
        return current($this->array);
    }

    public function key()
    {
        return key($this->array);
    }

    public function next()
    {
        return next($this->array);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

}
