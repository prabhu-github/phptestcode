<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 */
class Transaction extends CI_Model
{
			
	function __construct()
	{
		parent::__construct();
	}

	//insert order and payment details
	function create($data)
	{	
		$this->db->insert($this->config->item('order_table'),$data);
		$order_id = $this->db->insert_id(); 
		return array('order_id' => $order_id);
	}

	//get list of orders
	function getorderlist($limit=array())
	{
		if(count($limit)==0)
		{
			$selqry = "SELECT orderid,payment_type,price,currency,customername,order_status,amount,createdat,cardholder_name,card_last_4,trans_status,tans_id,fail_reason,trans_currency,parent_payment FROM ".$this->config->item('order_table');
		}
		else
		{
			$selqry = "SELECT orderid,payment_type,price,currency,customername,order_status,amount,createdat,cardholder_name,card_last_4,trans_status,tans_id,fail_reason,trans_currency,parent_payment FROM ".$this->config->item('order_table')." limit ".$limit[0].",".$limit[1];
		}	
				
		$res = $this->db->query($selqry);
		return $res;
	}
}

