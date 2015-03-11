<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


// 1. Autoload the SDK Package. This will include all the files and classes to your autoloader
require __DIR__  . '/PayPal-PHP-SDK/autoload.php';
require __DIR__  . '/PayPal-PHP-SDK/paypal/rest-api-sdk-php/sample/common.php';

use PayPal\Api\Amount; 
use PayPal\Api\Details;
use PayPal\Api\Item; 
use PayPal\Api\ItemList; 
use PayPal\Api\CreditCard; 
use PayPal\Api\Payer; 
use PayPal\Api\Payment; 
use PayPal\Api\FundingInstrument; 
use PayPal\Api\Transaction;

// 2. Provide your Secret Key. Replace the given one with your app clientId, and Secret
// https://developer.paypal.com/webapps/developer/applications/myapps
$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        'AXWz-hCwOCiCkIqk4N_2vYcX4h_RXQbJeTCLWyfgKDK7IEiD6bIOKaHmJH1q',     // ClientID
        'ECgfOxBsbXA0ItBk4Dt8Vzyvc9lIrcTpwcABNJI18SvOoZtP4OKldVsXfcYz'      // ClientSecret
    )
);

// Step 2.1 : Between Step 2 and Step 3
$apiContext->setConfig(
  array(
    'log.LogEnabled' => true,
    'log.FileName' => 'PayPal.log',
    'log.LogLevel' => 'FINE'
  )
);


$card = new CreditCard(); $card->setType("visa") ->setNumber("4148529247832259") ->setExpireMonth("11") ->setExpireYear("2019") ->setCvv2("012") ->setFirstName("Joe") ->setLastName("Shopper");

$fi = new FundingInstrument(); $fi->setCreditCard($card);
$payer = new Payer(); $payer->setPaymentMethod("credit_card") ->setFundingInstruments(array($fi));

$item1 = new Item(); $item1->setName('Ground Coffee 40 oz') ->setDescription('Ground Coffee 40 oz') ->setCurrency('USD') ->setQuantity(1) ->setTax(0.3) ->setPrice(7.50); $item2 = new Item(); $item2->setName('Granola bars') ->setDescription('Granola Bars with Peanuts') ->setCurrency('USD') ->setQuantity(5) ->setTax(0.2) ->setPrice(2);

$itemList = new ItemList(); $itemList->setItems(array($item1, $item2));

$details = new Details(); $details->setShipping(1.2) ->setTax(1.3) ->setSubtotal(17.5);

$amount = new Amount(); $amount->setCurrency("USD") ->setTotal(20) ->setDetails($details);

$transaction = new Transaction(); $transaction->setAmount($amount) ->setItemList($itemList) ->setDescription("Payment description") ->setInvoiceNumber(uniqid());

$payment = new Payment(); $payment->setIntent("sale") ->setPayer($payer) ->setTransactions(array($transaction));

$request = clone $payment;

try { $payment->create($apiContext); } 
catch (Exception $ex) { //ResultPrinter::printError('Create Payment Using Credit Card. If 500 Exception, try creating a new Credit Card using <a href="https://ppmts.custhelp.com/app/answers/detail/a_id/750">Step 4, on this link</a>, and using it.', 'Payment', null, $request, $ex); 
echo "<pre>";
$error_object = json_decode($ex->getData());
switch ($error_object->name)
{
    case 'VALIDATION_ERROR':
        //echo "Payment failed due to invalid Credit Card details:\n";
        foreach ($error_object->details as $e)
        {
            echo "\t" . $e->field . "\n\t" . $e->issue . "\n\n";
        }
        break;
}
exit(1); } 
//ResultPrinter::printResult('Create Payment Using Credit Card', 'Payment', $payment->getId(), $request, $payment); 
//return $payment;
echo "<pre>";
//print_r($payment->get($payment->getId(),$apiContext));
//print_r($payment->getTransactions());
 $payments = $payment->get($payment->getId(), $apiContext);
    $payments->getTransactions();
    $obj = $payments->toJSON();//I wanted to look into the object
    $paypal_obj = json_decode($obj);//I wanted to look into the object
	$transaction_id = $paypal_obj->transactions[0]->related_resources[0]->sale->id;
	$totalamount = $paypal_obj->transactions[0]->related_resources[0]->sale->amount->total;
	$currency = $paypal_obj->transactions[0]->related_resources[0]->sale->amount->currency;
	$status = $paypal_obj->transactions[0]->related_resources[0]->sale->state;
	echo ' transaction_id '.$transaction_id.' totalamount '.$totalamount.'  currency  '.$currency.' status  '.$status;
exit;
