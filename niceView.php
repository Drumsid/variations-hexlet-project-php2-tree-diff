<?php

// без глобал пока не рабоатет ((

$resultBefore2AndAfter2 = '{" a":1,"-b":2,"+b":"null","common":{"+follow":"false"," setting1":"Value 1","-setting2":200,"-setting3":"true","+setting3":"null","+setting4":"blah blah","+setting5":{"key5":"value5"},"setting6":{"doge":{"-wow":"","+wow":"so much"}," key":"value","+ops":"vops"}},"-d":3,"group1":{"-baz":"bas","+baz":"bars"," foo":"bar","-nest":{"key":"value"},"+nest":"str"},"-group2":{"abc":12345,"deep":{"id":45}},"+group3":{"fee":100500,"deep":{"id":{"number":45}}},"+z":"add"}';
$ops = json_decode($resultBefore2AndAfter2, true);
$arr = [
  ' test' => 1,
  '-123' => 2,
  '+rew' => 'sdf',
  ' arr' => [
      ' 1' => 1,
      ' 2' => 2,
      ' 3' => 3,
      ' 4' => [
          ' q' => 555
        ],
  ],
  ' end' => true,
  ' wtf' => [
      ' 5' => 123
  ]
];
function formatic($arr)
{
    $deep = 0;
    function test($arr, $deep = 0)
    {
      global $deep;
      $sep = str_repeat('.', $deep);
      $res = "{\n";
      foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $tmp = test($val, $deep += 1);
            $res .= $sep . $key . " : " . $tmp;
        } else {
            $res .= $sep . $key . " : " . $val . "\n";
        }
        
      }
    if($deep > 1){
      $deep = 0;
      return $res . $sep . "}\n";
    }
      return $res . $sep . "}\n";
    }  
    return test($arr);
}


print_r(formatic($arr));
// print_r(formatic($ops));
// print_r(test(json_decode( $resultBefore2AndAfter2, true)));

function nbsp($deep)
{
  $res = "";
  $nbsp = "    ";
  if ($deep <= 1) {
    return "";
  }
  for ($i = 1; $i < $deep; $i++) {
    $res .= $nbsp;
  }
  return $res;
}

// var_dump(nbsp(3));
