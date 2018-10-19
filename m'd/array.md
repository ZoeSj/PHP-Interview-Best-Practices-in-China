
## 这里有各种菜系和好吃的，现在要把这些东西分分组，这样可以更好地找到自己想吃的，也方便观察，还可以计算价格

### array:新建一个数组
```
$food = array("plants","animals","vagetables","fruits");
 print_r($food);

 result:
 Array
 (
     [0] => plants
     [1] => animals
     [2] => vagetables
     [3] => fruits
 )
```

## 现在有个饭店要展示这些品种，菜单比较高级，要求单词都是大写的，咋整？这个数组很容易记住：array_change_key_case.
### array array_change_key_case ( array $array [, int $case = CASE_LOWER ] )这个是默认的
### array array_change_key_case ( array $array [, int $case = CASE_UPPER ] )

```
print_r(array_change_key_case($value,CASE_UPPER));
foreach ($food as $key => $value) {
	// print_r($value);
	// print_r(array_change_key_case($value,CASE_UPPER));
}
```

哎呀，出错了。没有出现想要的结果！哦，这个是将数组中的所有键名修改
所以可以酱紫，将键值和键名对换，使用array_flip

```
print_r(array_change_key_case(array_flip($food),CASE_UPPER));
```
```
// result:
// (
//     [PLANTS] => 0
//     [ANIMALS] => 1
//     [VAGETABLES] => 2
//     [FRUITS] => 3
// )
```

## 老板觉得这个种类太少了，不能吸引顾客，要在后面添加一些品种，这个用啥加呢？
### array_unshift()在数组开头插入一个或多个单元
### array_shift()将数组开头的单元移出数组

```
print_r($food);
array_unshift($food,"meats","flowers");
$food = array_change_key_case(array_flip($food));
print_r($food);
```
```
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
```

## 老板又提要求了，说这个菜单都是品种分类，不行，要加点东西,在每个品种的下面添加单品
### 但是用array_fill_keys和array_fill 填充的都是相同的value，怎样分配不同的呢？
```
 $food = array_fill_keys($food,["beef"]);
 print_r($food);
```
```
// result:
// (
//     [meats] => Array
//         (
//             [0] => beef
//         )

//     [flowers] => Array
//         (
//             [0] => beef
//         )

//     [plants] => Array
//         (
//             [0] => beef
//         )

//     [animals] => Array
//         (
//             [0] => beef
//         )

//     [vagetables] => Array
//         (
//             [0] => beef
//         )

//     [fruits] => Array
//         (
//             [0] => beef
//         )

// )
```


### 这里就可以用到回调函数，来达到想要的效果
```
function add_iterm($item){
		switch ($item) {
		case '0':
			return(["pig","beef"]);
		case '1':
			return(["rose"]);
		
		default:
			return(["beef"]);
		}
}
$food_map = array_map("add_iterm", $food);

```
```
// print_r($food_map);
// result:
// Array
// (
//     [meats] => Array
//         (
//             [0] => pig
//             [1] => beef
//         )

//     [flowers] => Array
//         (
//             [0] => rose
//         )

//     [plants] => Array
//         (
//             [0] => beef
//         )

//     [animals] => Array
//         (
//             [0] => beef
//         )

//     [vagetables] => Array
//         (
//             [0] => beef
//         )

//     [fruits] => Array
//         (
//             [0] => beef
//         )

// )
```

## 老板看上面有好多单词啊，懒得数，就问一共有几种品类？
### 可以用count来计算数组中的单元数目，或者对象中的属性个数
### 那如果是统计这个数组的值呢？array_count_values()是查询数组中每个元素的值有几个

```
 print_r(count($food_map));
 print_r(array_count_values($food));
```






































