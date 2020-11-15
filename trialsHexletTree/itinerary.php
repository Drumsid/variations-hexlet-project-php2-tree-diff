<?php

$tree = ['Moscow', [
  ['Smolensk'],
  ['Yaroslavl'],
  ['Voronezh', [
    ['Liski'],
    ['Boguchar'],
    ['Kursk', [
      ['Belgorod', [
        ['Borisovka'],
      ]],
      ['Kurchatov'],
    ]],
  ]],
  ['Ivanovo', [
    ['Kostroma'], ['Kineshma'],
  ]],
  ['Vladimir'],
  ['Tver', [
    ['Klin'], ['Dubna'], ['Rzhev'],
  ]],
]];


// function test($tree, $acc, $parent = 'non')
function test($tree)
{
    $name = $tree[0];
    $branches = $tree[1];
    $res = array_reduce(
        $branches,
        function ($acc, $item) use ($name) {
            if (! isset($item[1])) {
                $acc[$item[0]] =  [$name, ['none']];
            }
            $acc[$item[0]] = test($item);
            return $acc;
        },
        []
    );
    return $res;
    // if ($tree[1] != null) {
    // if (isset($tree[1])) {
    //     [$name, $branches] = $tree;
    //     $children = [];
    //     $acc[$name] = [$parent, $children];
    //     foreach ($branches as $branch) {
    //         $name = test($branch, $acc, $name);
    //         array_push($children, $name);
    //     }
    // } else {
    //     [$tree[0], []];
    //     $children = [];
    //     $acc[$tree[0]] = [$parent, $children];
    // }
  

    // return $acc;
}
print_r(test($tree));


// var_dump($arr[1]);
