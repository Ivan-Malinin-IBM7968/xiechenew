<?php

/**
 * 格式化打印输出
 *
 * @param $arr
 */
function dd($arr)
{
    echo '<pre>';

    foreach (func_get_args() as $v) {
        if (!$v) {
            echo 'Null', '<br/>';
            continue;
        }

        print_r($v);
        echo '<br/>';
    }

    die('</pre>');
}

/**
 * 获取多维数组中的指定key的值
 *
 * @param $array
 * @param $key
 * @param null $default
 * @return null
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) return $array;

    if (isset($array[$key])) return $array[$key];

    foreach (explode('.', $key) as $segment)
    {
        if ( ! is_array($array) || ! array_key_exists($segment, $array))
        {
            return $default;
        }

        $array = $array[$segment];
    }

    return $array;
}