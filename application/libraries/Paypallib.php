<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	require APPPATH.'third_party/PayPal-PHP-SDK/autoload.php';
	require APPPATH.'third_party/PayPal-PHP-SDK/paypal/rest-api-sdk-php/sample/common.php';

	use PayPal\Api\Amount; 
	use PayPal\Api\Details;
	use PayPal\Api\Item; 
	use PayPal\Api\ItemList; 
	use PayPal\Api\CreditCard; 
	use PayPal\Api\Payer; 
	use PayPal\Api\Payment; 
	use PayPal\Api\FundingInstrument; 
	use PayPal\Api\Transaction;

class Paypallib
{

var $CI;
var $card;
var $apiContext;

function __construct()
{	
	// 1. Autoload the SDK Package. This will include all the files and classes to your autoloader
	$this->CI =& get_instance();


	// 2. Provide your Secret Key. Replace the given one with your app clientId, and Secret
	// https://developer.paypal.com/webapps/developer/applications/myapps
	$this->apiContext = new \PayPal\Rest\ApiContext(
		new \PayPal\Auth\OAuthTokenCredential(
			$this->CI->config->item('paypal_clientid'),     // ClientID
			$this->CI->config->item('paypal_clientsecret')      // ClientSecret
		)
	);

	// Step 2.1 : Between Step 2 and Step 3
	$this->apiContext->setConfig(
	  array(
		'log.LogEnabled' => true,
		'log.FileName' => 'PayPal.log',
		'log.LogLevel' => 'FINE'
	  )
	);
}

//create card details
function create_card($cardinfo)
{
		$names = explode( ' ', $cardinfo['ccholdername'] );	
		$data['firstName'] 	= isset($names[0]) ? $names[0] : NULL;
		$data['lastName'] 	= isset($names[1]) ? $names[1] : NULL;


	$card = new CreditCard(); 
	$card->setType($cardinfo['cardtype']) 
		->setNumber($cardinfo['ccnumber'])
		->setExpireMonth($cardinfo['ccexpmonth']) 
		->setExpireYear($cardinfo['ccexpyear']) 
		->setCvv2($cardinfo['cvvnumber']) 
		->setFirstName($data['firstName']) 
		->setLastName($data['lastName']);
	
	return $card;	
}		

//create funding instrument
function funding_instrument($card) 
{
	$fi = new FundingInstrument();
	$fi->setCreditCard($card);
	return $fi;	
}	

//create payer
function payer($fi) 
{
	$payer = new Payer(); 
	$payer->setPaymentMethod("credit_card") ->setFundingInstruments(array($fi));
	return $payer;
}

//create amount
function amount($currency,$amnt)
{
	$amount = new Amount(); 
	$amount->setCurrency($currency)
		  ->setTotal($amnt);
	return $amount;	  
}

//create transaction
function transaction($amount)
{
	$transaction = new Transaction(); 
	$transaction->setAmount($amount)
				->setDescription("Payment description") 
				->setInvoiceNumber(uniqid());
	return 	$transaction;		
}
	
//function process transaction
function payment($payer,$transaction)
{

	$payment = new Payment(); 
	$payment->setIntent("sale")
		->setPayer($payer) 
		->setTransactions(array($transaction));
    
			
	$request = clone $payment;
	$trans = array();
	try 
	{ 
		$payment->create($this->apiContext); 
	} 
	catch (Exception $ex) 
	{ 
		$error_object = json_decode($ex->getData());
		$trans['fail_reason'] = 'Unknown Error';
		if(isset($error_object->name))
		{
			$trans['fail_reason']  = $error_object->name;
			switch ($error_object->name)
			{
				case 'VALIDATION_ERROR':
				$fail_reason = array();
				foreach ($error_object->details as $e)
				{
				   $fail_reason[]= $e->field. "-". $e->issue ;
				}
				$trans['fail_reason']  = implode(', ',$fail_reason);
				break;						
			}
		}
		else
		{
			if(isset($error_object->error_description))
			{
				$trans['fail_reason'] = $error_object->error_description;
			}
		}

		$trans['status'] = 'failure';
		$trans['createdat'] = date('Y-m-d H:i:s');

		return $trans;
		exit();
	} 
	

	$payments = $payment->get($payment->getId(), $this->apiContext);
 
    $payments->getTransactions();
    $obj = $payments->toJSON();//I wanted to look into the object
    $paypal_obj = json_decode($obj);//I wanted to look into the object
	$trans['tans_id'] = $paypal_obj->transactions[0]->related_resources[0]->sale->id;
	$trans['amount'] = $paypal_obj->transactions[0]->related_resources[0]->sale->amount->total;
	$trans['currency'] = $paypal_obj->transactions[0]->related_resources[0]->sale->amount->currency;
	$trans['trans_status'] = $paypal_obj->transactions[0]->related_resources[0]->sale->state;
	$trans['createdat'] = date('Y-m-d H:i:s');
	$trans['cardholder_name'] = $paypal_obj->payer->funding_instruments[0]->credit_card->first_name.'  '.$paypal_obj->payer->funding_instruments[0]->credit_card->last_name;
	$trans['card_last_4'] = substr($paypal_obj->payer->funding_instruments[0]->credit_card->number, -4);
	$trans['parent_payment'] = $paypal_obj->transactions[0]->related_resources[0]->sale->parent_payment;
	$trans['status']  = 'completed';
	return $trans;
}
}