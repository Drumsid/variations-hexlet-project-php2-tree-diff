<?php

$tree = '{
  "host": "hexlet.io",
  "timeout": 50,
  "proxy": "123.234.53.22",
  "follow": false
}';

$deepTree = '{
  "common": {
    "setting1": "Value 1",
    "setting2": 200,
    "setting3": true,
    "setting6": {
      "key": "value",
      "doge": {
        "wow": ""
      }
    }
  },
  "group1": {
    "baz": "bas",
    "foo": "bar",
    "nest": {
      "key": "value"
    }
  },
  "group2": {
    "abc": 12345,
    "deep": {
      "id": 45
    }
  }
}';

$objTree = json_decode($tree);

// print_r($objTree);

function test($tree)
{
    $res = [];

    foreach ($tree as $key => $val) {
        if (is_object($val)) {
            $res[] = ['name' => $key, /*'value' => $val,*/ 'meta' => [], 'type' => 'dir', 'children' => test($val)];
        } else {
            $res[] = ['name' => $key, 'meta' => ['value' => boolOrNullToString($val)], 'type' => 'file'];
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
print_r(test($objTree));
