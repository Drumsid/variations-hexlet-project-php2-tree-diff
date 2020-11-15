<?php

// начал писать третий фариант дифа деревьев но че то не понравился подход, надо допиливать предыдущий вариант

$deepTree = '{
  "host": "hexlet.io",
  "timeout": 50,
  "obj": {
      "test": "value",
      "anower": {
          "pupu": "fer"
      },
      "io": "ss"
  },
  "proxy": "123.234.53.22",
  "follow": false
}';

$beforeTree = '{
    "host": "hexlet.io",
    "timeout": 50,
    "proxy": "123.234.53.22",
    "follow": false
  }';
  $afterTree = '{
    "timeout": 20,
    "verbose": true,
    "host": "hexlet.io"
  }';

$objBeforeTree = transformToArr(json_decode($beforeTree));
$objAfterTree  = transformToArr(json_decode($afterTree));

// print_r($objTree);

function transformToArr($tree)
{
    $res = [];

    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = ['name' => $key,  'type' => 'parent', 'value' => transformToArr($val)];
        } else {
            $res[] = ['name' => $key, 'value' => boolOrNullToString($val)];
        }
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

function differ($beforeTree, $afterTree)
{
    $res = [];
    foreach ($beforeTree as $beforeValue) {
        foreach ($afterTree as $afterValue) {
            if (($beforeValue['name'] == $afterValue['name']) && ($beforeValue['value'] == $afterValue['value'])) {
                $beforeValue['status'] = 'dont-change';
                $res[] = $beforeValue;
            }
            if (($beforeValue['name'] == $afterValue['name']) && ($beforeValue['value'] != $afterValue['value'])) {
                $beforeValue['status'] = 'changed';
                $beforeValue['beforeValue'] = $beforeValue['value'];
                $beforeValue['afterValue'] = $afterValue['value'];
                unset($beforeValue['value']);
                $res[] = $beforeValue;
            }
        }
    }
    return $res;
}
// print_r(transformToArr($objBeforeTree));
// print_r(transformToArr($objAfterTree));


print_r(differ($objBeforeTree, $objAfterTree));
