<?php

// сравнение плоских json (объектами не масивами, массивами я сделал ранее, лежит в репозитории)

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

function myDiff($before, $after)
{
    $beforeObj = json_decode($before);
    $afterObj = json_decode($after);
    $res = (object) [];

    foreach ($beforeObj as $beforeKey => $beforeValue) {
        foreach ($afterObj as $afterKey => $afterValue) {
            if (property_exists($afterObj, $beforeKey)) {
                if ($beforeKey == $afterKey && $beforeValue == $afterValue) {
                    $res->$beforeKey = ['value' => $beforeValue, 'status' => 'dontChange'];
                }
                if ($beforeKey == $afterKey && $beforeValue != $afterValue) {
                    $res->$beforeKey = ['beforeValue' => $beforeValue, 'afterValue' => $afterValue];
                }
            } else {
                $beforeValue = is_bool($beforeValue) ? boolToString($beforeValue) : $beforeValue;
                $res->$beforeKey = ['value' => $beforeValue, 'status' => 'removed'];
            }
        }
    }

    foreach ($afterObj as $afterKey => $afterValue) {
        if (! property_exists($beforeObj, $afterKey)) {
            $afterValue = is_bool($afterValue) ? boolToString($afterValue) : $afterValue;
            $res->$afterKey = ['value' => $afterValue, 'status' => 'added'];
        }
    }
    $diff = sortJson(json_encode($res));

    return parseDiff($diff);
}


function boolToString($bool)
{
    if (! is_bool($bool)) {
        return false;
    }
    if ($bool) {
        return 'true';
    }
    return 'false';
}

function sortJson($json)
{
    $json = json_decode($json, true);
    ksort($json);
    return json_encode($json);
}

$res = myDiff($before, $after);
print_r($res);
print_r(json_decode($res));
print_r(json_decode($res, true));


function parseDiff($diff)
{
    $diff = json_decode($diff, true);
    $res = [];
    foreach ($diff as $key => $array) {
        if (isset($array['status']) && $array['status'] == 'dontChange') {
            $res[' ' . $key] = $array['value'];
        } elseif (isset($array['status']) && $array['status'] == 'removed') {
            $res['-' . $key] = $array['value'];
        } elseif (isset($array['status']) && $array['status'] == 'added') {
            $res['+' . $key] = $array['value'];
        } elseif (isset($array['beforeValue']) && isset($array['afterValue'])) {
            $res['-' . $key] = $array['beforeValue'];
            $res['+' . $key] = $array['afterValue'];
        }
    }
    return json_encode($res);
}

// print_r(parseDiff($res));
print_r(json_decode(parseDiff($res), true));
