<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class order extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('cc_validation');
		$this->load->library('session');
		$this->load->model('transaction');
	}

	
	//display form
	function index()
	{		
		$datas = array();
		$this->load->helper('form');
		$this->load->library('form_validation');
		
				
		if($this->input->post('submit')!='')
		{
			$this->form_validation->set_rules('price', 'Price','trim|required|xss_clean|integer');
			$this -> form_validation -> set_rules('currency', 'Currency','trim|required|xss_clean');
			$this -> form_validation -> set_rules('customername', 'Customer Name','trim|required|xss_clean');
			$this -> form_validation -> set_rules('ccholdername', 'Credit card holder name','trim|required|xss_clean');
			$this -> form_validation -> set_rules('ccnumber', 'Credit card number','trim|required|xss_clean|integer|callback_validatecreditcard_number');
			$this -> form_validation -> set_rules('ccexpmonth', 'Credit card expiration Month','trim|required|xss_clean');
			$this -> form_validation -> set_rules('ccexpyear','Credit card expiration Year','trim|required|xss_clean');
			$this -> form_validation -> set_rules('cvvnumber','CVV','trim|required|xss_clean|callback_validatecvv_number');
			if ($this -> form_validation -> run() == TRUE) 
			{
				$cardinfo['ccnumber'] = $this->input->post('ccnumber');
				$cardtype = $this->cc_validation->validateCreditcard_number($cardinfo['ccnumber']);
				$cardinfo['cardtype'] = $cardtype['card_type'];
				$cardinfo['ccexpmonth'] =  $this->input->post('ccexpmonth');
				$cardinfo['ccexpyear'] =  $this->input->post('ccexpyear');
				$cardinfo['cvvnumber'] =  $this->input->post('cvvnumber');
				
				$cardinfo['ccholdername'] =  $this->input->post('ccholdername');
				
				$customername = $this->input->post('customername');
				
					
				$currency = $this->input->post('currency');
				$price = $this->input->post('price');
				
				if(($cardinfo['cardtype']=='amex') || ($currency=='USD' || $currency=='EUR' || $currency=='AUD'))
				{
					$this->paypal($cardinfo,$currency,$price,$customername);
				}
				else
				{
				
					$braincard_info = array('number' => $cardinfo['ccnumber'],'expirationMonth' => $cardinfo['ccexpmonth'],'expirationYear' => $cardinfo['ccexpyear'],'cardholderName'=>$cardinfo['ccholdername']);
					
					//braintree accept single currency format 
					//convert to braintree currency format
					$this->load->library('currencyconverterlib');
					$braintree_price = $this->currencyconverterlib->convert($currency, $this->config->item('braintree_basecurrency'), $price, 1, 1);
										
					
					$this->braintree($braincard_info,$currency,$braintree_price,$customername,$price);
				}	
			}		
		}
		$this -> load -> view('order', $datas);
	}
	
	//validate credit card
	function validatecreditcard_number($number) {
		
		$creditvalid = $this->cc_validation->validateCreditcard_number($number);

		if(!($creditvalid['status']) )
		{
			$this->form_validation->set_message('validatecreditcard_number', 'Invalid Credit Card Number');
			return FALSE;
		}
		else
		{
			//currency USD and card type AMEX Check
			$currency = $this->input->post('currency');
			if(($currency !=='USD') && ($creditvalid['card_type'] == 'amex'))
			{
				$this->form_validation->set_message('validatecreditcard_number', 'AMEX is possible to use only for USD');
				return FALSE;
			}
			return TRUE;
		}

	}
	
	//validate cvv number
	function validatecvv_number($cvvnumber)
	{
		$this->load->library('cc_validation');
		$ccnumber = $this->input->post('ccnumber');
		$creditvalid = $this->cc_validation->validate_cvv($ccnumber,$cvvnumber);

		if(!($creditvalid) )
		{
			$this->form_validation->set_message('validatecvv_number', 'Invalid CVV Number');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	//paypal transactions
	public function paypal($cardinfo,$currency,$amount,$customername)
	{
		$this->load->library('paypallib');
		$this->load->helper('url');
		$card = $this->paypallib->create_card($cardinfo);
		$fi = $this->paypallib->funding_instrument($card);
		$payer = $this->paypallib->payer($fi);
		$amnt = $this->paypallib->amount($currency,$amount);
		$trans = $this->paypallib->transaction($amnt);
		$payment = $this->paypallib->payment($payer,$trans);
	
		$data['payment_type']  = $this->config->item('payment_paypal');
		$this->insertorder($payment,$data,$currency,$amount,$customername);
	}
		
	//braintree transactions
	public function braintree($cardinfo,$currency,$amount,$customername,$ordamount)
	{
		$this->load->library('braintreelib');
		$transaction = $this->braintreelib->transaction($cardinfo,$amount);
				
		$data['payment_type']  = $this->config->item('payment_braintree');
		$this->insertorder($transaction,$data,$currency,$ordamount,$customername);
	}
	
	//insert order,transaction details
	public function insertorder($payment,$data=array(),$currency,$amount,$customername)
	{
		$data['price']  = $amount;
		$data['currency']  = $currency;
		$data['customername']  = $customername;	
			
		if($payment['status'] == 'completed')
		{
			$data['amount'] = $payment['amount'];
			$data['createdat'] = $payment['createdat'];
			$data['cardholder_name'] = $payment['cardholder_name'];
			$data['card_last_4'] = $payment['card_last_4'];
			$data['trans_status'] = $payment['trans_status'];
			$data['tans_id'] = $payment['tans_id'];
			$data['trans_currency'] = $payment['currency'];
			if(isset($payment['parent_payment']))
			{
				$data['parent_payment'] = $payment['parent_payment'];
			}	
			
			$data['order_status']  = 'COMPLETED';
			$order = $this->transaction->create($data);
			
			$msg = 'Your Order '.$order['order_id'].' has been Completed Successfully<br>Transaction Status :'.$data['trans_status'];
			
		}
		else
		{
			$data['order_status']  = 'FAILED';
			
			if(isset($transaction['trans_status']))
			{
				$data['amount'] = $transaction['amount'];
				$data['cardholder_name'] = $transaction['cardholder_name'];
				$data['card_last_4'] = $transaction['card_last_4'];
				$data['trans_status'] = $transaction['trans_status'];
				$data['tans_id'] = $transaction['tans_id'];
				$data['trans_currency'] = $transaction['currency'];
			}
			
			$data['createdat'] = $payment['createdat'];
			$data['fail_reason'] = $payment['fail_reason'];
			$order = $this->transaction->create($data);
			$msg = 'Your Order '.$order['order_id'].' has Failed<br>Reason :'.$data['fail_reason'];
		}
		$this->session->set_flashdata('msg',$msg);
		redirect(base_url('index.php/order/paymentresult'));
	}
	
	//display payment result page
	public function paymentresult() {
		$data = array();
		$this->load->view('paymentresult',$data);
	}
	
	//display order list
	public function orderlist() 
	{
		$orderlistall = $this->transaction->getorderlist();
			
		$this->load->library("pagination");
				
		$start = $this->uri->segment(3,0);
		$configpage["per_page"] = $this->config->item('per_page');
		$limit[0]			 = $start;
		$limit[1]			 = $configpage["per_page"];
				
		$configpage["base_url"] = base_url("index.php/order/orderlist");
		$configpage["total_rows"] = $orderlistall->num_rows;
		$configpage["uri_segment"] = 3;
		
		$this->pagination->initialize($configpage);
	 
		$orderlist = $this->transaction->getorderlist($limit);
		
		$orders = array();
		if($orderlist->num_rows>0)
		{
			$orders=$orderlist->result_array();
		}
		$data['orders'] = $orders;
		$data["links"] = $this->pagination->create_links();
 		
		$this->load->view('orderlist',$data);
	}
}	

