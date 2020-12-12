<?php

function transformToArr($tree, $path = "")
{
    $res = [];

    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = [
                'name' => $key,
                'type' => 'parent',
                'path' => $path . '.' . $key,
                'value' => transformToArr($val, $path . '.' . $key)
            ];
        } else {
            $res[] = [
                'name' => $key,
                'path' => $path . '.' . $key,
                'value' => boolOrNullToString($val)
            ];
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
function findSameName($findArr, $dataArrs)
{
    if (! is_array($findArr) || ! is_array($dataArrs)) {
        return false;
    }
    if (! array_key_exists('name', $findArr)) {
        return false;
    }
    ['name' => $findName] = $findArr;
    foreach ($dataArrs as $dataArr) {
        if (is_array($dataArr) && array_key_exists('name', $dataArr) && $findName == $dataArr['name']) {
            return $dataArr;
        }
    }
    return false;
}
