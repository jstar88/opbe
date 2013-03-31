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
abstract class Math
{
    public static function divide(Number $num, Number $denum, $real = false)
    {
        if ($real)
        {
            if ($denum->result == 0)
                throw new Exception();
            $shots = floor($num->result / $denum->result);
            $rest = Math::rest($num->result, $denum->result);
            return new Number($shots, $rest);
        }
        else
        {
            $shots = $num->result / $denum->result;
            return new Number($shots);
        }
    }
    public static function multiple(Number $first, Number $second, $real = false)
    {
        $result = $first->result * $second->result;
        if ($real)
        {
            return new Number(round($result));
        }
        return new Number($result);
    }
    public static function heaviside($x, $y)
    {
        if ($x >= $y)
        {
            return 1;
        }
        return 0;
    }
    public static function rest($dividendo, $divisore, $real = true)
    {
        while ($divisore < 1)
        {
            $divisore *= 10;
            $dividendo *= 10;
        }
        if (!$real)
        {
            $decimal = (int)$dividendo - $dividendo;
            return $divisore % $dividendo + $decimal;
        }
        return $dividendo % $divisore;
    }
    public static function tryEvent($probability, $callback, $callbackParam)
    {
        if(!is_callable($callback))
        {
            throw new Exception();
        }
        if (mt_rand(0, 99) <= $probability)
            return call_user_func($callback, $callbackParam);
        return false;
    }
    public static function recursive_sum($array)
    {
        $sum = 0;
        $array_obj = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        foreach ($array_obj as $key => $value)
        {
            $sum += $value;
        }
        return $sum;
    }
    /*
    public static function matrix_scalar_moltiplication($matrix, $scalar)
    {
        $func = function ($value)
        {
            return $value * $scalar;
        }
        ;
        return array_map($func, $matrix);
    }  */
}

?>