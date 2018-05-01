<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

$firebug = Fire\Bug::get();
$firebug->enable();

$pdo = new PDO('sqlite:' . __DIR__ . '/demo.db');
$db = new Fire\Sql($pdo);
$collection = $db->collection('TestCollection');

$filter = new Fire\Sql\Filter('{"rand": [">4", 4]}');
$filter->where('rand')->eq(6);
$filter->and('rand')->gteq(6);
$filter->or('rand')->eq(5);
$filter->and('rand')->gt(10);

$result = $collection->find($filter);
debugger($result);
debugger(count($result));

$firebug->render();
