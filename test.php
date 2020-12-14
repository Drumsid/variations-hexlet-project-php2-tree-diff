<?php

// тестирую тут функции в дебагере

// $arr = [
//   [
//     'name' => 1,
//     'value' => 2,
//   ],
//   [
//     'name' => 234,
//     'type' => 'parent',
//     'value' => [['name' => 123,'value' => 4234]],
//   ],
//   [
//     'name' => 3,
//     'value' => 4,
//   ]
// ];
// print_r($arr);

// function test($arr){
//   $res = [];
//   foreach ($arr as $v) {
//         if (is_array($v) && array_key_exists('type', $v) && $v['type'] == 'parent'){
//         $res[$v['name']] = test($v['value']); 
//         } else {
//             $res[$v['name']] = $v['value'];
//         }
//     }
    
//   return $res;
// }

// print_r(test($arr));

// ================================================
// $arr = [1,2,3,[11,12,13],5,6,7,8];

// function test($arr, $deep = 1)
// {
//   $res = "";
//   // $x = $deep;
//   foreach($arr as $key => $val){
//     if (is_array($val)) {
//       $tmp = test($val, $deep + 1);
//       $res .= $tmp . " - ";
//     } else {
//       $res .= $val * $deep . " - ";
//     }
//   }
//   return $res;
// }

// print_r(test($arr));

class Node
{
    public function __construct($value, Node $node = null)
    {
        $this->next = $node;
        $this->value = $value;
    }

    public function getNext()
    {
        return $this->next;
    }

    public function getValue()
    {
        return $this->value;
    }
}

$numbers = new Node(1, new Node(2, new Node(3)));

// var_dump($numbers->getNext()->getNext()->getNext());
print_r($numbers);
function getValues($nodes)
{
  $res = [];
  $res[] = $nodes->getValue();
  $node = $nodes->getNext();
  while (! is_null($node)) {
    $res[] = $node->getValue();
    $node = $node->getNext();
  }
  return $res;
}

$nodeValues = getValues($numbers);

function makeNodes($values)
{
  $res = null;
  $count = count($values);
    for ($i = 0; $i < $count; $i++) {
      $res = new Node($values[$i], $res);
    }
  

  return $res;
}

$node = makeNodes($nodeValues);
// print_r(makeNodes($nodeValues));

function reverse($list)
{
   return makeNodes(getValues($list));
}

print_r(reverse($numbers));