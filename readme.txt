
1.Enable Curl and openssl in php.ini
extension=php_curl.dll
extension=php_openssl.dll

2.change database settings in application/config/database.php
$db['default']['hostname'] 
$db['default']['username'] 
$db['default']['password'] 
$db['default']['database'] 

3.All the application related config details present in application/config/phptask.php

//Braintree config
$config['braintree_basecurrency']  //need to set initially ex.USD,SGD
$config['braintree_environment']   //ex.sandbox
$config['braintree_merchantId'] 
$config['braintree_publicKey']  
$config['braintree_privateKey'] 

$config['payment_braintree'] = 'BRAINTREE'; // order detail payment type - need to set initially

//Paypal Config
$config['paypal_clientid']  //Client ID
$config['paypal_clientsecret']  //need to set initially

$config['payment_paypal'] = 'PAYPAL'; // order detail payment type - need to set initially


