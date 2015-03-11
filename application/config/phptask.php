<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
Payment Related Config file
Paypal,Braintree Config 
*/

//Braintree config
$config['braintree_basecurrency'] = ''; //need to set initially
$config['braintree_environment'] = ''; 
$config['braintree_merchantId'] = ''; 
$config['braintree_publicKey'] = ''; 
$config['braintree_privateKey'] = ''; 

$config['payment_braintree'] = 'BRAINTREE'; // order detail payment type - need to set initially

//Paypal Config
$config['paypal_clientid'] = ''; //Client ID
$config['paypal_clientsecret'] = ''; //need to set initially

$config['payment_paypal'] = 'PAYPAL'; // order detail payment type - need to set initially

//tables
$config['order_table'] = 'order_details';


//pagination default rows
$config["per_page"] = 10;

/* Credit card Expiration Month */
$config['ccmonth'] = array(1=>'JAN','FEB','MAR','APR','MAY','JUN','JUL',"AUG","SEP","OCT","NOV","DEC");
/* Credit card Expiration Year End */
$config['ccendyear'] = 2025;
