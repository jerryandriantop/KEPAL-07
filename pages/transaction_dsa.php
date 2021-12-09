<?php
session_start();
require '../vendor/autoload.php';
require_once('../app/DSA_TRANSACTION.php');
include('../config/dbconn.php');

use Tools\DSA_Transaction;

$dsa_transaction = new DSA_Transaction();
$order_id = $_POST['order_id'];
$query =  "SELECT * FROM `order` WHERE order_id = $order_id";

$result = mysqli_query($dbconn, $query);

while ($res = mysqli_fetch_array($result)) {
    $data = [
        'cipher' => $res['cipher'],
        'private' => $res['private_key'],
        'order_id' => $order_id,
    ];
    $dsa_transaction->setSignaiv($res['signaiv']);
    $dsa_transaction->setEncryption($res['encrypt_ky']);
    $dsa_transaction->verify($data);
}
// var_dump($data);

// print("<pre>".print_r($data,true)."</pre>");