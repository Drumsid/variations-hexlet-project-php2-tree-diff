<?php

//Реализуйте функцию flatten(), которая делает плоским вложенный массив.

//Примеры

//$list = [1, 2, [3, 5], [[4, 3], 2]];

//flatten($list); // [1, 2, 3, 5, 4, 3, 2]


$list = [1, 2, [3, 5], [[4, 3], 2]];

// my solution
function flatten($arr)
{
    $res = array_reduce(
        $arr,
        function ($acc, $item) {
            if (is_array($item)) {
                $acc = array_merge($acc, flatten($item));
            } else {
                $acc[] = $item;
            }
            return $acc;
        },
        []
    );
    return $res;
}

print_r(flatten($list));


// hexlet solution
// BEGIN
function flatten2($tree)
{
    return array_reduce(
        $tree,
        fn($acc, $element) =>
            is_array($element)
                ? [...$acc, ...flatten2($element)]
                : [...$acc, $element],
        [],
    );
}
// END
