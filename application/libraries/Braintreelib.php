<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


/* Braintree CI Lib
*********************************************************************************************
@author: Munir Ahmad

Please if you do improve it send me a pull request

P.S. If in Doubt, don't follow your heart but http://www.braintreepayments.com/docs/php
*********************************************************************************************
*/
//include APPPATH.'third_party/Braintree/Braintree.php';



class Braintreelib
{
	var $last_errors;			// array of last errors encountered
	var $CI;							// PAPA
	
	function __construct()
	{
		//include 'third_party/Braintree.php';
		include APPPATH.'third_party/Braintree/Braintree.php';
		$this->CI =& get_instance();
		$this->CI->load->model('transaction');

		// Braintree settings
		Braintree_Configuration::environment($this->CI->config->item('braintree_environment'));
		Braintree_Configuration::merchantId($this->CI->config->item('braintree_merchantId'));
		Braintree_Configuration::publicKey($this->CI->config->item('braintree_publicKey'));
		Braintree_Configuration::privateKey($this->CI->config->item('braintree_privateKey'));	
	}

	//make transaction
	function transaction( $card_info = array(), $amount )
	{
		$result = Braintree_Transaction::sale( array(	'amount' => $amount,'creditCard' =>$card_info
		,
																									'options' => array( 'submitForSettlement' => true ),
																									
																							 )
																					);
	
		if ( $result->success === TRUE  )
		{
					
			$trans = $this->_log_transaction( $result->transaction );
			//var_dump($result);
			
			return $trans;
		}
		
		
	
		// func still running means errors!
		$trans = $this->_parse_errors($result);
		
		return $trans;
	}

    //make quick transaction
	function quick_transaction( $cust_id, $token, $amount )
	{
		$result = Braintree_Transaction::sale( array('amount' => $amount, 'customerId' => $cust_id, 'paymentMethodToken' => $token) );

		if ( $result->success )
		{
			$this->_log_transaction( $result->transaction );
			return true;
		}

		// func still running means errors!
		$this->_parse_errors($result);

		return false;
	}	

	function last_errors()
	{
		return $this->last_errors;
	}

	// saves a card no in your vault and returns token on success
	function create_card($card_info)
	{
		$result = Braintree_CreditCard::create( $card_info );

		if ( $result->success === true )
		{
			# Generated credit card token
			return $result->creditCard->token;
		}

		$this->_parse_errors($result);
		return false;
	}


	function create_customer_with_card( $card_info )
	{
		$names = explode( ' ', $card_info['cardholderName'] );
		$data['firstName'] 	= isset($names[0]) ? $names[0] : NULL;
		$data['lastName'] 	= isset($names[1]) ? $names[1] : NULL;
		$data['creditCard']	= $card_info;

		$result = Braintree_Customer::create($data);

		if ($result->success === true)
    	return array( 'cust_id' => $result->customer->id, 'card_token' => $result->customer->creditCards[0]->token );

		$this->_parse_errors($result);
		return false;
	}


	function create_subscription( $card_token, $plan_id, $custom = '' )
	{
		$result = Braintree_Subscription::create( array('paymentMethodToken' => $card_token, 'planId' => $plan_id) );

	
		if ( $result->success === true )
		{
			$this->_log_subscription( $result->subscription, $custom );

			
			return true;
		}


		$this->_parse_errors($result);
		return false;
	}

	function cancel_subscription($sub_id)
	{
		$result = Braintree_Subscription::cancel($sub_id);

		if ($result->success === true)
    	return true;

		$this->_parse_errors($result);
		return false;
	}


	function set_validation_rules()
	{
		$this->CI->form_validation->set_rules('cardno', 'Card No', 'trim|required');
		$this->CI->form_validation->set_rules('holder', 'Card Holder Name', 'trim|required');
		$this->CI->form_validation->set_rules('cvv', 'CVV', 'trim|required');
		$this->CI->form_validation->set_rules('expiry', 'Expiry Date', 'trim|required');
	}


	function get_data_from_form()
	{
		$data = array();

		$data['number'] 					= $this->CI->input->post('cardno');
		$data['expirationDate'] 	= $this->CI->input->post('expiry');
		$data['cvv'] 							= $this->CI->input->post('cvv');
		$data['cardholderName'] 	= $this->CI->input->post('holder');

		return $data;
	}

	private function _log_transaction( $transaction )
	{
		$trans = array( 'status'=>'completed','trans_status' => $transaction->status,
										'card_last_4' 		=> $transaction->creditCardDetails->last4,
										'cardholder_name' => $transaction->creditCardDetails->cardholderName,
										'currency' => $transaction->currencyIsoCode,
										'createdat'			=> $transaction->createdAt->format('Y-m-d H:i:s'),
										'amount'					=> $transaction->amount,
										'tans_id'							=> $transaction->id,
									);
		return 	$trans;						
		
	}

	private function _log_subscription( $subscription, $custom = '' )
	{
		$sub 	= array( 'sub_id'=>$subscription->id, 'user_id'=>$custom, 'json'=>json_encode($subscription) ); 
		$this->CI->transaction->create_subscription( $sub );
	}

	// parses errors from Braintree result object and saves them for later use
	private function _parse_errors($result)
	{
		$this->last_errors = array();
		
		$res = array();
		$res['status'] = 'FAILED';
		$res['createdat'] = date('Y-m-d H:i:s');
		
		foreach($result->errors->deepAll() AS $error)
		{
		  $this->last_errors[] = $error->code.': '.$error->message;
		}
		
		if(count($this->last_errors)>0)
		{
			
			$res['fail_reason'] = implode(', ',$this->last_errors);
		}
		else
		{
			if(isset($result->transaction)) 
			{
				$transaction = $result->transaction;
				$res['trans_status'] = $transaction->status;
				$res['card_last_4']  = $transaction->creditCardDetails->last4;
				$res['cardholder_name']  = $transaction->creditCardDetails->cardholderName;
				$res['currency']  = $transaction->currencyIsoCode;
				$res['createdat']  =  $transaction->createdAt->format('Y-m-d H:i:s');
				$res['amount']  = $transaction->amount;
				$res['tans_id']  =  $transaction->id;
				$res['fail_reason']  = $transaction->processorResponseText;
			}
			else
			{
				$res['fail_reason'] = 'Unknown Error';
			}	
		}
		return $res;
	}

}



?>
