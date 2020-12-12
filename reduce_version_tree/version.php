<?php

require('lib.php');
require('old_function.php');

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

$dBefore = '{
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
$dAfter = '{
  "common": {
    "follow": false,
    "setting1": "Value 1",
    "setting3": null,
    "setting4": "blah blah",
    "setting5": {
      "key5": "value5"
    },
    "setting6": {
      "key": "value",
      "ops": "vops",
      "doge": {
        "wow": "so much"
      }
    }
  },
  "group1": {
    "foo": "bar",
    "baz": "bars",
    "nest": "str"
  },
  "group3": {
    "fee": 100500,
    "deep": {
      "id": {
        "number": 45
      }
    }
  }
}';

$beforeArr = transformToArr(json_decode($before));
$afterArr = transformToArr(json_decode($after));

$dBeforeArr = transformToArr(json_decode($dBefore));
$dAfterArr = transformToArr(json_decode($dAfter));

function reduce_differ($beforeTree, $afterTree)
{
    $comparedData = array_reduce($beforeTree, function ($acc, $before) use ($afterTree) {
        $after = findSameName($before, $afterTree);
        if ($after) {
            if (
                ($before['name'] == $after['name']) && array_key_exists('type', $after) &&
                $after['type'] == 'parent'
            ) {
                $before['value'] = reduce_differ($before['value'], $after['value']);
                $acc[] = $before;
            } elseif (($before['name'] == $after['name']) && ($before['value'] == $after['value'])) {
                $before['status'] = 'dontChange';
                $before['plain'] = 'plain';
                $acc[] = $before;
            } elseif (($before['name'] == $after['name']) && ($before['value'] != $after['value'])) {
                $before['status'] = 'changed';
                $before['plain'] = 'plain';
                $before['beforeValue'] = $before['value'];
                $before['afterValue'] = $after['value'];
                if (array_key_exists('type', $before)) {
                    $before['type'] = 'skip';
                }
                $acc[] = $before;
            }
        } else {
            $before['status'] = 'removed';
            $before['plain'] = 'plain';
            if (array_key_exists('type', $before)) {
                $before['type'] = 'skip';
            }
            $acc[] = $before;
        }
        return $acc;
    }, []);

    $result = array_reduce($afterTree, function ($acc, $after) use ($beforeTree) {
        $find = findSameName($after, $beforeTree);
        if (! $find) {
            $after['status'] = 'added';
            $after['plain'] = 'plain';
            if (array_key_exists('type', $after)) {
                $after['type'] = 'skip';
            }
            $acc[] = $after;
        }
        return $acc;
    }, $comparedData);

    usort($result, function ($item1, $item2) {
        if ($item1['name'] == $item2['name']) {
            return 0;
        }
        return ($item1['name'] < $item2['name']) ? -1 : 1;
    });

    return $result;
}


// print_r($beforeArr);
// print_r(reduce_differ($dBeforeArr, $dAfterArr));
// print_r(deepDiff($dBeforeArr, $dAfterArr));
// var_dump(reduce_differ($dBeforeArr, $dAfterArr) == deepDiff($dBeforeArr, $dAfterArr));
print_r(reduce_differ($beforeArr, $afterArr));
