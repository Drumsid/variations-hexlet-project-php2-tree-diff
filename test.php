<?php

// тестирую тут функции в дебагере

$arr = [
  [
    'name' => 1,
    'value' => 2,
  ],
  [
    'name' => 234,
    'type' => 'parent',
    'value' => [['name' => 123,'value' => 4234]],
  ],
  [
    'name' => 3,
    'value' => 4,
  ]
];
print_r($arr);

function test($arr){
  $res = [];
  foreach ($arr as $v) {
        if (is_array($v) && array_key_exists('type', $v) && $v['type'] == 'parent'){
        $res[$v['name']] = test($v['value']); 
        } else {
            $res[$v['name']] = $v['value'];
        }
    }
    
  return $res;
}

print_r(test($arr));