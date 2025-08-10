<?php

include($_SERVER['DOCUMENT_ROOT'] . '/fstudy/wp-load.php');
    define('SITE_URL', home_url('/', is_ssl() ? 'https' : 'http'));

    echo "<script>const siteurl = '" . SITE_URL . "';</script>";





date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
  
$vnp_TmnCode = "OT88GMQJ"; //Mã định danh merchant kết nối (Terminal Id)
$vnp_HashSecret = "ETJQX1GWW2TQ9H330Q6GX0M1FLIF586S"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = SITE_URL. "/contents/checkout_gateway/token/completed_order_vnpay.php";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
