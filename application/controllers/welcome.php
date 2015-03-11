<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$data = array();
		
		$this->checkordertableexist();
		$this->load->view('welcome',$data);
		
	}
	
	  //check order table exist if not found create new one 	
	  private function checkordertableexist(){
       
	
        if ($this->db->table_exists('order_details') ){
		
            return(true);
        }
        else{
	        $this->load->dbforge();
            $this->dbforge->add_field(array(
                'orderid' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ),
                'payment_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => FALSE
                ),
                'payment_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => FALSE
                ),
                'price' => array(
                    'type' => 'FLOAT',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'currency' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '3',
                    'null' => FALSE
                ),
                'customername' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => FALSE
                ),
                'order_status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => FALSE
                ),
                'amount' => array(
                    'type' => 'FLOAT',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'createdat' => array(
                    'type' => 'datetime',
                    'null' => FALSE
                ),
				'cardholder_name' => array(
                    'type' => 'varchar',
					'constraint' => '250',
                    'null' => FALSE
                ),
					'card_last_4' => array(
                    'type' => 'varchar',
					'constraint' => '10',
                    'null' => FALSE
                ),
					'trans_status' => array(
                    'type' => 'varchar',
					'constraint' => '32',
                    'null' => FALSE
                ),
					'tans_id' => array(
                    'type' => 'varchar',
					'constraint' => '32',
                    'null' => FALSE
                ),
					'fail_reason' => array(
                    'type' => 'text',
					'null' => FALSE
                ),
					'trans_currency' => array(
                    'type' => 'varchar',
					'constraint' => '3',
					'null' => FALSE
                ),
					'parent_payment' => array(
                    'type' => 'varchar',
					'constraint' => '32',
					'null' => FALSE
                ),
            ));

            $this->dbforge->add_key('orderid', TRUE);
            $this->dbforge->create_table('order_details',TRUE);
        } 
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */