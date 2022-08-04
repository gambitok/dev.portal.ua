<?php
$month_from=date("Y-m-01", strtotime("-1 month"));
$month_to=date("Y-m-31", strtotime("-1 month"));
$month=date("Y-m-00", strtotime("-1 month"));

$data_to = date("Y-m-d", strtotime("-1 month"));
var_dump($data_to);
$data_to = date("Y-m-t", strtotime($data_to));

var_dump($data_to);

var_dump($month_from);
var_dump($month_to);
var_dump($month);
