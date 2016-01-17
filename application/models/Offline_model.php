<?php
class Offline_model extends Doctrine_Record {
	public function get_prior_records(){
		$git_data = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT * FROM git_log");
		
		return $git_data;
	}
	

}