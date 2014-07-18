<?php
ob_start();
class auto_sms extends MY_Controller 
{
	//change to false once tests have been successful
	var $test_mode=true;
	
	public function update_stock_out_table()
	{
		
		//Get commodity that are out of stock
		$in = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll("
		select date_added,facility_code, commodity_id from facility_stocks 
		where `current_balance`=0 and `status`=1 and initial_quantity > 0
		group by commodity_id,facility_code");
		
		$total_count = count($in);
		
		for($i=0;$i<$total_count;$i++)
		{
			Doctrine_Manager::getInstance()->getCurrentConnection()->execute("
		insert into facility_stock_out_tracker 
		set facility_code='".$in[$i]['facility_code']."', commodity_id='".$in[$i]['commodity_id']."',
		`start_date`='".$in[$i]['date_added']."', commodity_status='stocked out' ");
		
			
		}
	}
	
	public function send_stock_update_sms()
	{
		$facility_code = $this -> session -> userdata('facility_id'); 
		$facility_name = Facilities::get_facility_name2($facility_code);
       	
        $data = Users::get_users_active_in_facility($facility_code)->toArray();

	   	$message= "Stock level for ".$facility_name." has been updated. HCMP";
       
	   	$phone = $this->get_facility_phone_numbers($facility_code);
	   	$phone .=$this->get_ddp_phone_numbers($data[0]['district']);

	   
	   	$this->send_sms(substr($phone,0,-1),$message);

	}
	
	public function send_order_sms()
	{
		$facility_code = $this -> session -> userdata('facility_id'); 
		$facility_name = Facilities::get_facility_name2($facility_code);
		$data = Users::get_users_active_in_facility($facility_code)->toArray();
	   
	   //Message for orders placed
	   	$message = $facility_name." has submitted an order. HCMP";
       
       //Get facility phone numbers and theDPP phone numbers
	   	$phone = $this->get_facility_phone_numbers($facility_code);
	   	$phone .=$this->get_ddp_phone_numbers($data[0]['district']);
	   	
	   	$this->send_sms(substr($phone,0,-1),$message);

	}
	
	public function send_order_approval_sms($facility_code,$status)
	{
		$message = ($status==1)?$facility_name."'s order has been rejected. HCMP":$facility_name."'s order has been approved. HCMP";
 
    	$data = Users::get_users_active_in_facility($facility_code)->toArray();
	   	$phone=$this->get_facility_phone_numbers($facility_code);
	   	$phone .=$this->get_ddp_phone_numbers($data[0]['district']);
	   	
	   	//sends actual text message
	   	$this->send_sms(substr($phone,0,-1),$message);

	}
	
	public function send_stock_decommission_email($message,$subject,$attach_file)
	{
		$facility_code = $this -> session -> userdata('facility_id'); 
	   	$data = Users::get_users_active_in_facility($facility_code)->toArray();
	   
	   	//get the email addresses of users in the system
	   	$email_address=$this->get_facility_email($facility_code);
	   	$email_address .=$this->get_ddp_email($data[0]['district']);
	   	
	   	//send the email
	   	$this->send_email(substr($email_address,0,-1),$message,$subject,$attach_file);
	   
	 
	}
	
	public function get_facility_phone_numbers($facility_code)
	{
		$data=Users::get_user_info($facility_code);
		$phone=""; 
		foreach ($data as $info) 
		{
			$telephone =preg_replace('(^0+)', "254",$info->telephone);
	
		    $phone .=$telephone.'+';	
			
		}
		return $phone;
	}
	
	public function get_facility_email($facility_code)
	{
		$data = Users::get_user_info($facility_code);
		
		$user_email=""; 
		
		foreach ($data as $info) 
		{

			$user_email .= $info->email.',';
		   	
		}
		return $user_email;
	}

	public function get_ddp_phone_numbers($district_id)
	{
		$data = Users::get_dpp_details($district_id);
		$phone=""; 
	
		foreach ($data as $info) 
		{
			$telephone =preg_replace('(^0+)', "254",$info->telephone);
		    $phone .=$telephone.'+';	
		}
		
		return $phone;
	}
	
	public function get_ddp_email($district_id)
	{
		$data = Users::get_dpp_details($district_id);
		$user_email=""; 
		
		foreach ($data as $info) 
		{
			$user_email .=$info->email.',';
		}
		return $user_email;
	}
	
	
	public function send_stock_donate_sms()
	{
		$facility_code = $this -> session -> userdata('facility_id'); 
		$facility_name = Facilities::get_facility_name2($facility_code);
		
       	$data = Users::get_users_active_in_facility($facility_code)->toArray();

	    $phone = $this->get_facility_phone_numbers($facility_code);
	   	$phone .= $this->get_ddp_phone_numbers($data[0]['district']);
		
	    $message = $facility_name."'s have been donated commodities. HCMP";		
		$this->send_sms(substr($phone,0,-1),$message);
	
	}


	public function send_sms($phones,$message) 
	{
	
	   $message=urlencode($message);
	   
	   $spam_sms='254720167245+254726534272+254726416795+254725227833+'.$phones;
	
	 	$phone_numbers=explode("+", $spam_sms);
		//foreach($phone_numbers as $key=>$user_no):
	  //  break;
		//file("http://41.57.109.242:13000/cgi-bin/sendsms?username=clinton&password=ch41sms&to=$user_no&text=$message");
			
		//endforeach;
 		
	}
	
	public function send_order_submission_email($message,$subject,$attach_file)
	{
		//the CC email address
		$cc_email = ($this->test_mode)?'kariukijackson@gmail.com': 'anganga.pmo@gmail.com,sochola06@yahoo.com';
	 	
		$facility_code = $this -> session -> userdata('facility_id'); 
		//$facility_name = Facilities::get_facility_name2($facility_code);
	   	$data = Users::get_users_active_in_facility($facility_code)->toArray();
	   
	  	$email_address = $this->get_facility_email($facility_code);
	   	$email_address .= $this->get_ddp_email($data[0]['district']);
		
		return $this->send_email(substr($email_address,0,-1),$message, $subject,$attach_file,null,$cc_email);
	
	}

	public function send_order_approval_email($message,$subject,$attach_file,$facility_code,$reject_order=null)
	{
		$cc_email=($this->test_mode)?'kariukijackson@gmail.com': 'anganga.pmo@gmail.com,sochola06@yahoo.com';
 		$data = Users::get_users_active_in_facility($facility_code)->toArray();
  		
  		if($reject_order==1):
		  $email_address=$this->get_facility_email($facility_code);
		  $cc_email .=$this->get_ddp_email($data[0]['district']);
	  
	  	else:
		  $email_address=($this->test_mode)?'kariukijackson@gmail.com': 'shamim.kuppuswamy@kemsa.co.ke,
			samuel.wataku@kemsa.co.ke,
			jmunyu@kemsa.co.ke,
			imugada@kemsa.co.ke,
			laban.okune@kemsa.co.ke,
			samuel.wataku@kemsa.co.ke,'; 

	  		$cc_email .= $this->get_ddp_email($data[0]['district']);
	   		$cc_email .= $this->get_facility_email($facility_code); 
  		endif;
	  

		return $this->send_email(substr($email_address,0,-1),$message, $subject,$attach_file,null,substr($cc_email,0,-1));
	
	}
	
	public function send_order_delivery_email($message,$subject,$attach_file=null)
	{
		$cc_email = ($this->test_mode)?'kariukijackson@gmail.com': 'anganga.pmo@gmail.com,sochola06@yahoo.com';

	   	$facility_code = $this -> session -> userdata('facility_id'); 
	   
	   	$data = Users::get_users_active_in_facility($facility_code)->toArray();
	   
	   	$cc_email .=$this->get_facility_email($facility_code);
		
		return $this->send_email(substr($this->get_ddp_email($data[0]['district']),0,-1),$message,$subject,null,null,substr($cc_email,0,-1));
	
	}

	public function send_email($email_address,$message,$subject,$attach_file=NULL,$bcc_email=NULL,$cc_email=NULL)
	{
 
		$mail_list=($this->test_mode)?'kariukijackson@gmail.com,kariukijackson@ymail.com': 'rkihoto@clintonhealthaccess.org,
  		eunicew2000@yahoo.com,
  		gmacharia@clintonhealthaccess.org,
  		Jhungu@clintonhealthaccess.org,
  		nmaingi@strathmore.edu,
  		bwariari@clintonhealthaccess.org,
  		kyalocatherine@gmail.com,
  		ashminneh.mugo@gmail.com,
  		smutheu@clintonhealthaccess.org,
  		kariukijackson@gmail.com,
 		kelvinmwas@gmail.com,';
			
		$fromm='hcmpkenya@gmail.com';
		$messages=$message;
  		$config['protocol']    = 'smtp';
        $config['smtp_host']    = 'ssl://smtp.gmail.com';
        $config['smtp_port']    = '465';
        $config['smtp_timeout'] = '7';
        $config['smtp_user']    = 'hcmpkenya@gmail.com';
        $config['smtp_pass']    = 'healthkenya';
        $config['charset']    = 'utf-8';
        $config['newline']    = "\r\n";
        $config['mailtype'] = 'html'; // or html
        $config['validation'] = TRUE; // bool whether to validate email or not  
		$this->load->library('email', $config);

        $this->email->initialize($config);
		
  		$this->email->set_newline("\r\n");
  		$this->email->from($fromm,'Health Commodities Management Platform'); // change it to yours
  		$this->email->to($email_address); // change it to yours
  		
  		isset($cc_email)? $this->email->cc($cc_email): //
  		
  		(isset($bcc_email))?
  		$this->email->bcc($mail_list.$bcc_email)
  		
  		:
  		$this->email->bcc(substr($mail_list,0,-1))

		;
  		
		 (isset($attach_file))? $this->email->attach($attach_file) :	'';
			
  		$this->email->subject($subject);
 		$this->email->message($messages);
 
		  if($this->email->send())
		  {
			return TRUE;
		  }
		 else
		 {
		 	return FALSE;
		 }

	}
	
		

}
