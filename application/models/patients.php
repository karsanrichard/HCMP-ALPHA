<?php
/*
@author Karsan
*/
class Patients extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('id', 'int');
		$this -> hasColumn('firstname', 'varchar', 45);
		$this -> hasColumn('lastname', 'varchar', 45);
		$this -> hasColumn('date_of_birth', 'date');
		$this -> hasColumn('gender', 'int');
		$this -> hasColumn('telephone', 'varchar',45);
		$this -> hasColumn('email','varchar',45);
		$this -> hasColumn('home_address','varchar',65);
		$this -> hasColumn('work_address','varchar',65);
		$this -> hasColumn('patient_number','varchar',45);
		$this -> hasColumn('system_patient_number','varchar',45);
		$this -> hasColumn('facility_code', 'varchar',45);
		$this -> hasColumn('date_created', 'datetime');
        $this -> hasColumn('date_updated', 'datetime');
		$this -> hasColumn('status', 'int');
		
	}

	public function setUp() {
		$this -> setTableName('patient_details');
	}


	public function get_all(){
		$facility_code = $this -> session -> userdata('facility_id');		
		$sql = "select id,firstname, lastname,date_of_birth,gender,telephone,email,home_address,work_address,patient_number,date_created from patient_details where facility_code ='$facility_code' and status = '1'";
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll($sql);
		return $query;
	}

	public function get_patient_details($id = NULL){
		$magufuli = isset($id)? "p.id = $id":NULL;

		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll("
			SELECT 
				p.id,
			    p.patient_names,
			    p.age
			FROM
			    patients p
			WHERE
			    $magufuli
			    GROUP BY id
			");

		return $query;
	}

	public function get_one_patient($patient_number){
		$sql = "select id,firstname, lastname, date_of_birth, gender, patient_number from patient_details where patient_number = '$patient_number' limit 0,1";
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll($sql);
		return $query;
	}

	//comment

	public function filter_patient($patient_number){
		$sql = "select id,firstname, lastname, date_of_birth, gender, patient_number from patient_details where patient_number like '%$patient_number%' or firstname like '%$patient_number%' or lastname like '%$patient_number%'";
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll($sql);
		return $query;
	}

	public function get_patient_commodity_info($id = NULL){
		$magufuli = isset($id)? "AND p.id = $id":NULL;
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll("
			SELECT 
			    c.commodity_code,
			    p.patient_names,
			    c.commodity_name,
			    c.unit_size,
			    c.total_commodity_units,
			    i.issue_type_desc,
			    p.id,
			    p.age,
			    pi.amount,
			    pi.date_issued
			FROM
			    commodities c,
			    patients p,
			    patient_issues pi,
			    issue_type i
			WHERE
			    p.id = pi.patient_id
			        AND pi.commodity_id = c.id
			        AND i.id = pi.issue_type_id
			        $magufuli
			");

		return $query;
	}

	public function get_patient_data($patient_id){
		$dawa_sawa = isset($patient_id)? "WHERE id = $patient_id":NULL;
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll("
			SELECT * FROM patients $dawa_sawa
			");

		return $query;
	}


	public function save_patient($data_array,$patient_number,$date_created,$facility_code){
		$o = new Patients ();
	    $o->fromArray($data_array);	    
		$o->save();		
		$sql = "select id from patient_details where patient_number='$patient_number' and date_created='$date_created' limit 0,1";		
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll($sql);
		
		foreach ($query as $key => $value) {
			$id = $value['id'];
			$system_patient_number = $facility_code.'P'.$id;
			$sql_u = "update patient_details set system_patient_number='$system_patient_number' where id='$id'";
			$q = Doctrine_Manager::getInstance()->getCurrentConnection()->execute($sql_u);	
		}
		
		return TRUE;
	}

	public function get_patient_history($patient_id = NULL){
		$filter = isset($patient_id)? "AND pd.id = $patient_id":NULL;
		$sql = "SELECT 
				    dr.patient_id,
				    dr.commodity_id,
				    c.commodity_name,
				    dr.units_dispensed,
				    dr.date_created,
				    pd.firstname,
				    pd.lastname
				FROM
				    dispensing_records dr,commodities c,patient_details pd
				    WHERE dr.commodity_id = c.id AND dr.patient_id = pd.id $filter";		
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll($sql);

		return $query;
	}
	

}
?>