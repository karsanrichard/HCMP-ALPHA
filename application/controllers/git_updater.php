<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Git_updater extends MY_Controller {

	function __construct() {
		parent::__construct();
	
	}

	public function index()
	{
		// $this->load->view('welcome_message');
		echo "Welcome to the the git updater";
	}

	public function github_update_status(){
		// echo "I WAS HERE";
		$res = $this->github_updater->has_update();

		echo "<pre>"; print_r($res);
	}

	public function github_update(){
		// echo "I WAS HERE";
		$res = $this->github_updater->update();
		// $this->unzip->extract();
		echo "<pre>"; print_r($res);
	}
	
	public function get_hash(){
		// echo "I WAS HERE";
		$res = $this->github_updater->get_hash();
		// echo "<pre>"; print_r($res);
		return $res;
	}

	public function extract_files(){
		$hash = $this -> get_hash();

		$unzip_status = $this->unzip->extract($hash.'.zip');

		// echo "<pre>"; print_r($unzip_status);echo "</pre>";
		$sanitized_directory = array();
		foreach ($unzip_status as $unzip) {
			$unzip = substr($unzip, 2);
			// echo "<pre>".$unzip;
			$del = "/";
			$trimmed=strpos($unzip, $del);
			$important=substr($unzip, $trimmed+strlen($del)-1, strlen($unzip)-1);
			$important = substr($important, 1);

			$sanitized_directory[] = $important;
		}
			// echo "<pre>";print_r($sanitized_directory);
			$ignored = $this->ignored_files();
			$squeaky = $this->array_cleaner($sanitized_directory,$ignored);

			echo "<pre>";print_r($squeaky);

			$status = $this->copy_and_replace($squeaky);
			$set_hash = $this->github_updater_set_config_hash($hash);


	}

	public function ignored_files(){
		$ignored = $this->github_updater->list_ignored();

		// echo "<pre>";print_r($ignored);
		return $ignored;
	}

	public function array_cleaner($dirty_array,$dirt){
		foreach ($dirty_array as $key => $leaving_elem) {
		    foreach ($dirt as $keys => $value) {
		    	if (strpos($leaving_elem,$value) !== false) {
				    // echo 'true';
			        unset($dirty_array[$key]);
				}
			    // echo $leaving_elem;
		    }
		}
		// echo "<pre>DIRTY ARR";print_r($dirty_array);
		// echo "<pre>DIRT";print_r($dirt);

		return $dirty_array;
		// echo FCPATH;
	}

	public function copy_and_replace($directories){
		foreach ($directories as $dir) {
			$copy_status = copy(FCPATH.$dir, FCPATH.$dir);
		}
		return $copy_status;
	}

	public function delete_residual_files($path){
		$delete_status = unlink($path);
		delete_files('./path/to/directory/', TRUE);
		return $delete_status;
	}

	public function tester(){
		echo FCPATH;
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */