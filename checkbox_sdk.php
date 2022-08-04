<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
@ini_set('display_errors', true);
date_default_timezone_set("Europe/Kiev");
ini_set('memory_limit', '2048M');
define('RDD', __DIR__);
header('Content-Type: text/html; charset=utf-8');

require(RDD . "/checkbox-in-ua-php-sdk/vendor/autoload.php");

        $server = 'https://api.checkbox.in.ua/api/v1';
        $login  = 'vera_tychina';
        $pass   = 'Qwerty456852z';
        $lkey   = '6c387c0dd48603b314054ace';

$config = new \igorbunov\Checkbox\Config([
    \igorbunov\Checkbox\Config::API_URL     => $server,
    \igorbunov\Checkbox\Config::LOGIN       => $login,
    \igorbunov\Checkbox\Config::PASSWORD    => $pass,
    \igorbunov\Checkbox\Config::LICENSE_KEY => $lkey
]);

$api = new \igorbunov\Checkbox\CheckboxJsonApi($config);

$api->signInCashier();

if ($api->getCashierShift() === null) {
    $api->createShift();
}

$cashier_name = iconv("windows-1251", "UTF-8", "�����");
$department = iconv("windows-1251", "UTF-8", "�����");
$name1 = iconv("windows-1251", "UTF-8", "������");
$name2 = iconv("windows-1251", "UTF-8", "������ 2");

$receipt = new \igorbunov\Checkbox\Models\Receipts\SellReceipt(
    $cashier_name, // ������
    $department, // �����
    new \igorbunov\Checkbox\Models\Receipts\Goods\Goods(
        [
            new \igorbunov\Checkbox\Models\Receipts\Goods\GoodItemModel( // ����� 1
                new \igorbunov\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-123', // good_id
                    50 * 100, // 50 ���
                    $name1 // �������� ������
                ),
                1 * 1000 // ���-�� ������  1 ��
            ),
            new \igorbunov\Checkbox\Models\Receipts\Goods\GoodItemModel( // ����� 2
                new \igorbunov\Checkbox\Models\Receipts\Goods\GoodModel(
                    'vm-124', // good_id
                    20 * 100, // 20 ���
                    $name2 // �������� ������
                ),
                2 * 1000 // ���-�� ������ 2 ��
            )
        ]
    ),
    'admin@gmail.com', // ���� ���������� ��� �� �����
    new \igorbunov\Checkbox\Models\Receipts\Payments\Payments([
        new \igorbunov\Checkbox\Models\Receipts\Payments\CardPaymentPayload( // ����������� ������
            40 * 100 // 40 ���
        ),
        new \igorbunov\Checkbox\Models\Receipts\Payments\CashPaymentPayload( // �������� ������
            50 * 100 // 50 ���
        )
    ])
);

//$api->createSellReceipt($receipt); // ��������� ������

//$payments = [];
//foreach ($arr["payments"]["results"] as $item) {
//    $payments[] = iconv("UTF-8", "windows-1251", $item["label"]);
//}
//$payments = implode(",", $payments);
//var_dump($payments);
//
//$arr = json_decode(json_encode($api->getReceipts()), true);
//foreach ($arr["results"] as $key => $val) {
//    var_dump($key . " = " . $val["id"]);
//}
//var_dump($api->getReceiptHtml('93f33f0c-8e13-4072-8b7b-b54a78dbeb9d'));
//
//$arr = json_decode(json_encode($api->getReceipt('1e7eef25-b5d9-4752-aca2-90bd2d176956')), true);
//var_dump($arr['fiscal_code']);

var_dump($api->getReceipts());

$api->closeShift();

$api->signOutCashier();