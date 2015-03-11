<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
 */
class Transaction extends CI_Model
{
	//private $table_name			= 'transactions';			// user accounts
		
	function __construct()
	{
		parent::__construct();
				
		//$ci =& get_instance();
	}

	function create($table_name,$data,$orddata)
	{
	
		if($orddata['order_status']=='SUCCESS')
		{
			if ($this->db->insert($table_name, $data))
			 {
				$payment_id = $this->db->insert_id();
				$orddata['payment_id'] = $payment_id;
				$this->db->insert($this->config->item('order_table'), $orddata);
				$order_id = $this->db->insert_id(); 
				return array('order_id' => $order_id);
			}
		}	
		
		$this->db->insert($table_name, $data);
		$payment_id = $this->db->insert_id();	
		$orddata['payment_id'] = $payment_id;
		$this->db->insert($this->config->item('order_table'), $orddata);
		$order_id = $this->db->insert_id(); 
		return array('order_id' => $order_id);
	}

	function create_subscription($data)
	{
		if ($this->db->insert('subscriptions', $data))
		 {
			$subscription_id = $this->db->insert_id();
			return array('subscription_id' => $subscription_id); 
		}
		return NULL;
	}
	
	function getorderlist()
	{
		$selqry = "SELECT orderid,payment_type,price,orderq.currency,customername,order_status";
		//paypal fields
		$selqry .= ",paypal.amount as paypal_amount,paypal.createdat as paypal_createdat,paypal.cardholder_name as paypal_cardholder_name,  
		paypal.card_last_4 as paypal_card_last_4,paypal.trans_status as paypal_trans_status,paypal.tans_id as paypal_tans_id,
		paypal.fail_reason as paypal_fail_reason,paypal.currency as paypal_currency,paypal.parent_payment as paypal_parent_payment";
		//braintree fields
		$selqry .= ",braintree.amount as braintree_amount,braintree.createdat as braintree_createdat,braintree.cardholder_name as braintree_cardholder_name,braintree.card_last_4 as braintree_card_last_4,braintree.trans_status as braintree_trans_status,braintree.tans_id as braintree_tans_id,braintree.fail_reason as braintree_fail_reason,braintree.currency as braintree_currency";
		
		$selqry .= " FROM ".$this->config->item('order_table')." As orderq";
		
		//paypal join
		$selqry .= " LEFT JOIN ".$this->config->item('paypal_table')." as paypal ON payment_id=paypalid AND payment_type = '".$this->config->item('payment_paypal')."'";
		
		//braintree join
		$selqry .= " LEFT JOIN ".$this->config->item('braintree_table')." as braintree ON payment_id=braintreeid AND payment_type = '".$this->config->item('payment_braintree')."'";
		
		$res = $this->db->query($selqry);
		return $res;
	}
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */
