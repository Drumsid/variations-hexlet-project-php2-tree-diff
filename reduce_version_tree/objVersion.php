<?php

$before = '{
  "host": "hexlet.io",
  "timeout": 50,
  "proxy": "123.234.53.22",
  "follow": false
}';
$after = '{
  "timeout": 20,
  "verbose": true,
  "host": "hexlet.io"
}';

$objBefore = json_decode($before);
$objAfter = json_decode($after);

// print_r($objBefore);

$test = array_map(function ($item) {
    
},
    $objBefore);
function transformToObj($tree, $path = "")
{
    $res = [];

    foreach ($tree as $key => $val) {
        $res[] = [
            'name' => $key,
            'path' => $path . '.' . $key,
            'value' => boolOrNullToString($val)
        ];
    }
    return $res;
}
function boolOrNullToString($data)
{
    if (is_null($data)) {
        return 'null';
    }
    if (is_bool($data) && $data === true) {
        return 'true';
    }
    if (is_bool($data) && $data === false) {
        return 'false';
    }
    return $data;
}

// print_r(transformToObj($objBefore));