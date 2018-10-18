<?php
// 这里有各种菜系和好吃的，现在要把这些东西分分组，这样可以更好地找到自己想吃的，也方便观察，还可以计算价格

//array:新建一个数组
$food = array("plants","animals","vagetables","fruits");
print_r($food);

// result:
// Array
// (
//     [0] => plants
//     [1] => animals
//     [2] => vagetables
//     [3] => fruits
// )

// 现在有个饭店要展示这些品种，菜单比较高级，要求单词都是大写的，咋整？这个数组很容易记住：array_change_key_case.
// array array_change_key_case ( array $array [, int $case = CASE_LOWER ] )这个是默认的
// array array_change_key_case ( array $array [, int $case = CASE_UPPER ] )

// print_r(array_change_key_case($value,CASE_UPPER));
foreach ($food as $key => $value) {
	print_r($value);
	// print_r(array_change_key_case($value,CASE_UPPER));
}


//哎呀，出错了。没有出现想要的结果！哦，这个是将数组中的所有键名修改

//所以可以酱紫，将键值和键名对换，使用array_flip

print_r(array_change_key_case(array_flip($food),CASE_UPPER));

// result:
// (
//     [PLANTS] => 0
//     [ANIMALS] => 1
//     [VAGETABLES] => 2
//     [FRUITS] => 3
// )

//老板觉得这个种类太少了，不能吸引顾客，要在后面添加一些品种，这个用啥加呢？
// array_unshift()在数组开头插入一个或多个单元
// array_shift()将数组开头的单元移出数组

array_unshift($food,"meats","flowers");
print_r($food);

// result:
// Array
// (
//     [0] => meats
//     [1] => flowers
//     [2] => plants
//     [3] => animals
//     [4] => vagetables
//     [5] => fruits
// )

//老板又提要求了，说这个菜单都是品种分类，不行，要加点东西

