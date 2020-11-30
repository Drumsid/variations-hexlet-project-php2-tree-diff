<?php

require('lib.php');

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

$deep = '{
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

// $beforeArr = transformToArr(json_decode($before));
// $afterArr = transformToArr(json_decode($after));


$beforeNew = json_decode($before, true);
$deepDeep = json_decode($deep, true);
function mb($arr)
{
  $path = "";
  $test  = array_map(function ($name, $value) use($path){
    if (is_array($value)) {
        $value = mb($value, $path . '.' . $name);
        return [
            'name' => $name,
            'path' => $path . '.' . $name,
            'type' => 'parent',
            'value'  => $value
        ];      
    } else {
    $value = boolOrNullToString($value);
        return [
            'name' => $name,
            'path' => $path . '.' . $name,
            'value'  => $value
        ];        
    }
  }, array_keys($arr), $arr);
  return $test;
}

print_r(transformToArr($deepDeep));
print_r(mb($deepDeep));
var_dump(mb($deepDeep) == transformToArr($deepDeep)); // false