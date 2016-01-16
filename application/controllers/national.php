<?php
/*
 * @author Kariuki & Mureithi
 */
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class national extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> helper(array('form', 'url', 'file'));
		// $this -> load -> library(array('form_validation','PHPExcel/PHPExcel'));
		$this -> load -> library(array('hcmp_functions', 'form_validation'));
	}

	public function index() {

		$counties = $q = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAll("SELECT distinct c.id,c.kenya_map_id as county_fusion_map_id,c.county,count(c.county) as facility_no FROM facilities f 
			INNER JOIN districts d ON f.district=d.id
			INNER JOIN counties c ON d.county=c.id
						where using_hcmp =1 group by c.county");// change  !!!!!!!!!!!!!
		// change  !!!!!!!!!!!!!
		$county_name = array();
		$map = array();
		$datas = array();
		$status = '';
		// echo "<pre>";print_r($counties);echo "</pre>";exit;
		
		foreach ($counties as $county) {
			$countyMap = (int)$county['county_fusion_map_id'];
			$countyName = $county['county_name'];
			$facility_No = $county['facility_no'];
			array_push($county_name,array($county['id']=>$countyName));
			$datas[] = array('id' => $countyMap, 
				'value' => $countyName, 
				'value' => $facility_No,
				'color' => 'FFCC99', 
				'tooltext' => $countyName,
				"baseFontColor" => "000000", 
				"link" => "Javascript:run('" .$county['id']. "^" .$countyName. "')");
		}
		$map = array( "showlabels"=>'0' , "baseFontColor" => "000000", "canvasBorderColor" => "ffffff", 
			"hoverColor" => "feab3a", "fillcolor" => "F8F8FF", "numbersuffix" => " Facilities", 
			"includevalueinlabels" => "1", "labelsepchar" => ":", "baseFontSize" => "9",
			"borderColor" => "333333 ","showBevel" => "0", 'showShadow' => "0",'showTooltip'=>"1");
		$styles = array("showBorder" => 0 , 'animation'=>"_xScale");
		$finalMap = array('map' => $map, 'data' => $datas, 'styles' => $styles);
		$data['title'] = "National Dashboard";
		$data['maps'] = json_encode($finalMap);
		$data['counties'] = $county_name;
		$this -> load -> view("national/national_v.php", $data);

	}

	public function search() {
		$data['title'] = "National Dashboard";
		$data['c_data'] = Commodities::get_all_2();
		$this -> load -> view("national/national_search_v", $data);
	}

	public function create_json() {
		$facility = $this -> db -> get('counties');

		$facility = $facility -> result_array();

		foreach ($facility as $fac) {
			$facArray[] = array('county' => $fac['county'], 'id' => $fac['id'], 'name' => 'county');
		}
		$data = json_encode($facArray);

		write_file('assets/scripts/typehead/json/counties.json', $data);

		$facility = $this -> db -> get('districts');
		$facility = $facility -> result_array();

		foreach ($facility as $fac) {
			$facArray1[] = array('districts' => $fac['district'], 'name' => 'district', 'county' => $fac['county'], 'id' => $fac['id']);
		}
		$data = json_encode($facArray1);

		write_file('assets/scripts/typehead/json/districts.json', $data);

		$facility = $this -> db -> get('facilities');
		$facility = $facility -> result_array();

		foreach ($facility as $fac) {
			$facArray2[] = array('facilities' => $fac['facility_name'], 'name' => 'facility', 'subcounty' => $fac['district'], 'id' => $fac['facility_code']);
		}
		$data = json_encode($facArray2);

		write_file('assets/scripts/typehead/json/facilities.json', $data);

	}

	public function facility_over_view($county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
		$district_id = ($district_id == "NULL") ? null : $district_id;
		$graph_type = ($graph_type == "NULL") ? null : $graph_type;
		$facility_code = ($facility_code == "NULL") ? null : $facility_code;
		$county_id = ($county_id == "NULL") ? null : $county_id;

		$and = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
		$and .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
		$and = isset($and) ? $and : null;

		if (isset($county_id)) :
			$county_name = counties::get_county_name($county_id);
		$name = $county_name['county'];
		$title = "$name County";
		elseif (isset($district_id)) :
			$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
		$district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
		$title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
		elseif (isset($facility_code)) :
			$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
		$title = $facility_code_['facility_name'];
		else :
			$title = "National";
		endif;

		if ($graph_type != "excel") :
			$q = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT f.`using_hcmp`
				from facilities f, districts d, 
				counties c where f.district=d.id 
				and d.county=c.id and 
				f.`using_hcmp`=1 
				$and
				group by f.facility_name
				");

		echo count($q);
		else :
			$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "facilities rolled out $title", 'file_name' => "facilities rolled out $title");
		$row_data = array();
		$column_data = array("County", "Sub-County", "Facility Name", "Facility Code", "Facility Level","Type", "Date of Activation");
		$excel_data['column_data'] = $column_data;

		$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			c.county, d.district as subcounty, f.facility_name,f.facility_code, f.`level`, f.type,f.date_of_activation
			from
			facilities f,
			districts d,
			counties c
			where
			f.district = d.id and d.county = c.id
			and f.`using_hcmp` = 1
			$and
			group by f.facility_name
			");

		foreach ($facility_stock_data as $facility_stock_data_item) :
			array_push($row_data, array($facility_stock_data_item["county"], $facility_stock_data_item["subcounty"], $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["level"], $facility_stock_data_item["type"],$facility_stock_data_item["date_of_activation"]));
		endforeach;
		$excel_data['row_data'] = $row_data;

		$this -> hcmp_functions -> create_excel($excel_data);
		endif;
	}

	public function hcw($county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
		$district_id = ($district_id == "NULL") ? null : $district_id;
		$graph_type = ($graph_type == "NULL") ? null : $graph_type;
		$facility_code = ($facility_code == "NULL") ? null : $facility_code;
		$county_id = ($county_id == "NULL") ? null : $county_id;
		$and = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
		$and .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
		$and = isset($and) ? $and : null;

		if (isset($county_id)) :
			$county_name = counties::get_county_name($county_id);
		$name = $county_name['county'];
		$title = "$name County";
		elseif (isset($district_id)) :
			$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
		$district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
		$title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
		elseif (isset($facility_code)) :
			$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
		$title = $facility_code_['facility_name'];
		else :
			$title = "National";
		endif;

		if ($graph_type != "excel") :
			$q = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT  
				distinct u.`id`
				from facilities f, 
				districts d, counties c, user u 
				where f.district=d.id and 
				d.county=c.id and 
				f.facility_code=u.facility 
				$and 
				");

		echo count($q);
		else :
			$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "hcw trained $title", 'file_name' => 'hcw trained');
		$row_data = array();
		$column_data = array("County", "Sub-County", "Total");
		$excel_data['column_data'] = $column_data;

		$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT  c.county, d.district as subcounty, count(u.id) as total from 
			facilities f, districts d, counties c, user u 
			where f.district=d.id and d.county=c.id 
			and f.facility_code=u.facility 
			and (u.usertype_id=2 or u.usertype_id=5 or u.usertype_id=3) 
			$and
			group by c.id,d.`id` 
			order by c.county asc,d.district asc
			");

		foreach ($facility_stock_data as $facility_stock_data_item) :
			array_push($row_data, array($facility_stock_data_item["county"], $facility_stock_data_item["subcounty"], $facility_stock_data_item["total"]));
		endforeach;
		$excel_data['row_data'] = $row_data;

		$this -> hcmp_functions -> create_excel($excel_data);
		endif;
	}

	public function order_approval($county_id = null) {
		$and = isset($county_id) ? "and c.id=$county_id" : null;

	}

	public function approval_delivery($county_id = null) {
		$and = isset($county_id) ? "and c.id=$county_id" : null;

	}

	public function get_facility_infor($county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
		$district_id = ($district_id == "NULL") ? null : $district_id;
		$graph_type = ($graph_type == "NULL") ? null : $graph_type;
		$facility_code = ($facility_code == "NULL") ? null : $facility_code;
		$county_id = ($county_id == "NULL") ? null : $county_id;
		$and = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
		$and .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
		$and = isset($and) ? $and : null;

		// echo ; exit;
		$fbo = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT count(*) as total
			FROM  `facilities` f, districts d, counties c  
			WHERE  f.district=d.id and d.county=c.id   $and  and (f.`owner` LIKE  '%fbo%' or f.`owner` LIKE  '%faith%' 
				or f.`owner` LIKE  '%christian%' or f.`owner` LIKE  '%catholic%' or f.`owner` LIKE  '%muslim%' 
				or f.`owner` LIKE  '%episcopal%' or f.`owner` LIKE  '%cbo%') ");

		$private = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT count(*) as total 
			FROM  `facilities` f, districts d, counties c  
			WHERE  f.district=d.id and d.county=c.id   $and  and (f.`owner` LIKE  '%private%' or f.`owner` LIKE  '%non%' or  f.`owner` LIKE  '%ngo%'
				or  f.`owner` LIKE  '%company%' or f.`owner` LIKE  '%armed%') ");

		$public = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT count(*) as total
			FROM  `facilities` f, districts d, counties c
			WHERE  f.district=d.id and d.county=c.id   $and  and (f.`owner` LIKE  '%gok%' or f.`owner` LIKE  '%moh%' or f.`owner` LIKE  '%ministry%'
				or f.`owner` LIKE  '%community%' or f.`owner` LIKE  '%public%' or f.`owner` LIKE  '%local%' or f.`owner` LIKE  '%g.o.k%' ) ");

		$using_hcmp = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT count(f.id) as total, sum(f.`using_hcmp`) as using_hcmp
			from facilities f, districts d, 
			counties c where f.district=d.id 
			and d.county=c.id 
			$and
			");
		$other = $using_hcmp[0]['total'] - $public[0]['total'] - $private[0]['total'] - $fbo[0]['total'];
		echo "
		<table>
			<tr><td># of facilities </td>     <td>" . $using_hcmp[0]['total'] . "</td></tr>
			<tr><td># of public health facilities</td> <td>" . $public[0]['total'] . "</td></tr>
			<tr><td># of private facilities</td>       <td>" . $private[0]['total'] . "</td></tr>
			<tr><td># of faith based facilities</td>   <td>" . $fbo[0]['total'] . "</td></tr>
			<tr><td># of other facilities</td>   <td>" . $other . "</td></tr>
			<tr><td># of facilities using HCMP</td>    <td>" . $using_hcmp[0]['using_hcmp'] . "</td></tr>
		</table>
		";

	}

	public function expiry($year = null, $county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
		$year = ($year == "NULL") ? date('Y') : $year;
		/*//Get the current month

		 $datetime1 = new DateTime('Y-10');
		 $datetime2 = new DateTime('Y-12');
		 $interval = $datetime2->diff($datetime1);
		 echo $interval->format('%R%a days');exit;
		 $current_month = date("Y-m");
		 $end = date('Y-12');

		 $interval = $current_month->diff($end);
		 echo $interval;exit;*/
		//check if the district is set
		 $district_id = ($district_id == "NULL") ? null : $district_id;
		// $option=($optionr=="NULL") ? null :$option;
		 $facility_code = ($facility_code == "NULL") ? null : $facility_code;
		 $county_id = ($county_id == "NULL") ? null : $county_id;
		// $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		 $month_ = isset($month) ? $months[(int)$month - 1] : null;

		 $category_data = array();
		 $series_data = $series_data2 = $series_data_ = $series_data_2 = array();
		 $temp_array = $temp_array2 = $temp_array_ = array();
		 $graph_data = array();
		 $title = '';

		 if (isset($county_id)) :
		 	$county_name = counties::get_county_name($county_id);
		 $name = $county_name['county'];
		 $title = "$name County";
		 elseif (isset($district_id)) :
		 	$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
		 $district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
		 $title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
		 elseif (isset($facility_code)) :
		 	$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
		 $title = $facility_code_['facility_name'];
		 else :
		 	$title = "";
		 endif;

		 $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		 $category_data = array_merge($category_data, $months);
		 $and_data = ($district_id > 0) ? " AND d1.id = '$district_id'" : null;
		 $and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		 $and_data .= ($county_id > 0) ? " AND d1.county='$county_id'" : null;
		 $and_data = isset($and_data) ? $and_data : null;

		 $group_by = ($district_id > 0 && isset($county_id) && !isset($facility_code)) ? " ,d1.id" : null;
		 $group_by .= ($facility_code > 0 && isset($district_id)) ? "  ,f.facility_code" : null;
		 $group_by .= ($county_id > 0 && !isset($district_id)) ? " ,c.id" : null;
		 $group_by = isset($group_by) ? $group_by : " ,c.id";
		 if ($graph_type != "excel") :
		 	$commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select DATE_FORMAT( temp.expiry_date,  '%b' ) AS cal_month,
		 		sum(temp.total) as total
		 		from
		 		districts d1,
		 		facilities f
		 		left join
		 		(select ROUND(SUM(f_s.current_balance / d.total_commodity_units) * d.unit_cost, 1) AS total,
		 			f_s.facility_code,f_s.expiry_date
		 			from
		 			facility_stocks f_s, commodities d
		 			where
		 			f_s.expiry_date < NOW()
		 			and d.id = f_s.commodity_id
		 			and year(f_s.expiry_date) = $year
		 			AND  (f_s.status =1 or f_s.status =2 )
		 			GROUP BY d.id , f_s.facility_code having total > 1) 
		 temp ON temp.facility_code = f.facility_code
		 where
		 f.district = d1.id
		 $and_data
		 and temp.total > 0
		 group by month(temp.expiry_date)");
		 $commodity_array2 = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
		 	select 
		 	DATE_FORMAT( temp.expiry_date,  '%b' ) AS cal_month,
		 	sum(temp.total) as total
		 	from
		 	districts d1,
		 	facilities f
		 	left join
		 	(select 
		 		ROUND(SUM(f_s.current_balance / d.total_commodity_units) * d.unit_cost, 1) AS total,
		 		f_s.facility_code,f_s.expiry_date
		 		from
		 		facility_stocks f_s, commodities d
		 		where
		 		f_s.expiry_date >= NOW()
		 		and d.id = f_s.commodity_id
		 		AND f_s.status = (1 or 2)
		 		AND year(f_s.expiry_date) = $year
		 		GROUP BY d.id , f_s.facility_code
		 		having total > 1) temp ON temp.facility_code = f.facility_code
		 where
		 f.district = d1.id
		 $and_data
		 and temp.total > 0
		 group by month(temp.expiry_date)
		 ");

		 foreach ($commodity_array as $data) :
		 	$temp_array = array_merge($temp_array, array($data["cal_month"] => $data['total']));
		 endforeach;
		 foreach ($commodity_array2 as $data2) :
		 	$temp_array2 = array_merge($temp_array2, array($data2["cal_month"] => $data2['total']));
				//$series_data2 = array_merge($series_data2, array($data2["cal_month"] => (int)$data2['total']));
				//$category_data = array_merge($category_data, array($data2["cal_month"]));
		 endforeach;
			//  echo "<pre>";print_r($temp_array2);echo "</pre>";exit;

		 foreach ($months as $key => $data) :
				//for expiries
		 	$val = (array_key_exists($data, $temp_array)) ? (int)$temp_array[$data] : (int)0;
		 $series_data = array_merge($series_data, array($val));
		 array_push($series_data_, array($data, $val));

				//for potential expiries
		 $val2 = (array_key_exists($data, $temp_array2)) ? (int)$temp_array2[$data] : (int)0;
		 $series_data2 = array_merge($series_data2, array($val2));
		 array_push($series_data_2, array($data, $val2));
		 endforeach;
		 $graph_type = 'column';

		 $graph_data = array_merge($graph_data, array("graph_id" => 'dem_graph_'));
		 $graph_data = array_merge($graph_data, array("graph_title" => "Expiries in $title $year"));
		 $graph_data = array_merge($graph_data, array("color" => "['#AA4643', '#89A54E']"));
		 $graph_data = array_merge($graph_data, array("graph_type" => $graph_type));
		 $graph_data = array_merge($graph_data, array("graph_yaxis_title" => "KSH"));
		 $graph_data = array_merge($graph_data, array("graph_categories" => $category_data));
		 $graph_data = array_merge($graph_data, array("series_data" => array()));

			//$default_expiries=array_merge($default_expiries,array("series_data"=>array()));
		 $graph_data['series_data'] = array_merge($graph_data['series_data'], array("Potential Expiries" => $series_data2, "Actual Expiries" => $series_data));
			//echo "<pre>";print_r($graph_data);echo "</pre>";exit;
		 $data = array();
		 $data['graph_id'] = 'dem_graph_';
		 $data['high_graph'] = $this -> hcmp_functions -> create_high_chart_graph($graph_data);

			// print_r($data['high_graph']);
			//exit;
		 return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);
		 else :
		 	$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "Expiry  $title", 'file_name' => "Stock Expired in $title  $year");
		 $row_data = array();
		 $column_data = array("Commodity", "Unit Size", "Quantity (Packs)", "Quantity (Units)", "Unit Cost (Ksh)", "Total Cost Expired (Ksh)", "Date of Expiry", "Supplier", "Manufacturer", "Facility Name", "Facility Code", "Sub-County", "County");
		 $excel_data['column_data'] = $column_data;
			//echo  ; exit;
		 $facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select  c.county, d1.district as subcounty ,temp.drug_name,
		 	f.facility_code, f.facility_name,temp.manufacture, sum(temp.total) as total_ksh,temp.units,
		 	temp.unit_cost,temp.expiry_date,temp.unit_size,
		 	temp.packs
		 	from districts d1, counties c, facilities f left join
		 	(
		 		select  ROUND( SUM(
		 			f_s.current_balance  / d.total_commodity_units ) * d.unit_cost, 1) AS total,
		 ROUND( SUM( f_s.current_balance  / d.total_commodity_units  ), 1) as packs,
		 SUM( f_s.current_balance) as units,
		 f_s.facility_code,d.id,d.commodity_name as drug_name, f_s.manufacture,
		 f_s.expiry_date,d.unit_size,d.unit_cost

		 from facility_stocks f_s, commodities d
		 where f_s.expiry_date < NOW( ) 
		 and d.id=f_s.commodity_id
		 and year(f_s.expiry_date) !=1970
		 AND (f_s.status =1 or f_s.status =2)
		 GROUP BY d.id,f_s.facility_code having total >1

		 ) temp
		 on temp.facility_code = f.facility_code
		 where  f.district = d1.id
		 and c.id=d1.county
		 and temp.total>0
		 $and_data
		 group by temp.id,f.facility_code
		 order by temp.drug_name asc,temp.total asc, temp.expiry_date desc
		 ");
array_push($row_data, array("The below commodities have expired $title  $year"));
foreach ($facility_stock_data as $facility_stock_data_item) :
	array_push($row_data, array($facility_stock_data_item["drug_name"], $facility_stock_data_item["unit_size"], $facility_stock_data_item["packs"], $facility_stock_data_item["units"], $facility_stock_data_item["unit_cost"], $facility_stock_data_item["total_ksh"], $facility_stock_data_item["expiry_date"], "KEMSA", $facility_stock_data_item["manufacture"], $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["subcounty"], $facility_stock_data_item["county"]));
endforeach;
$excel_data['row_data'] = $row_data;

$this -> hcmp_functions -> create_excel($excel_data);
endif;

}

public function potential($county_id=null, $district_id=null,$facility_code=null,$graph_type=null,$interval=null)

{
	$interval = ((isset($interval))&& ($interval > 0))? $interval:12;//default to select annual
	$and_data =($district_id>0) ?" AND d1.id = '$district_id'" : null;
	$and_data .=($facility_code>0) ?" AND f.facility_code = '$facility_code'" : null;
	$and_data .=($county_id>0) ?" AND d1.county='$county_id'" : null;
	$and_data =isset( $and_data) ?  $and_data:null;
	if( $graph_type!="excel"):
		$commodity_array = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAll("
		select
		DATE_FORMAT( temp.expiry_date,  '%b-%Y' ) AS cal_month,
		sum(temp.total) as total
		from
		districts d1,
		facilities f
		left join
		(select
			ROUND(SUM(f_s.current_balance / d.total_commodity_units) * d.unit_cost, 1) AS total,
			f_s.facility_code,f_s.expiry_date
			from
			facility_stocks f_s, commodities d
			where
			f_s.expiry_date between DATE_ADD(CURDATE(), INTERVAL 1 day) and  DATE_ADD(CURDATE(), INTERVAL $interval MONTH)
			and d.id = f_s.commodity_id
			AND f_s.status = (1 or 2)
			GROUP BY d.id , f_s.facility_code
			having total > 1) temp ON temp.facility_code = f.facility_code
	where
	f.district = d1.id
	$and_data
	and temp.total > 0
	group by month(temp.expiry_date)
	");

	foreach ($commodity_array as $data) :
		$series_data = array_merge($series_data, array($data["cal_month"] => (int)$data['total']));
	$category_data = array_merge($category_data, array($data["cal_month"]));
	endforeach;

	$graph_type='spline';

	$graph_data=array_merge($graph_data,array("graph_id"=>'dem_graph_1'));
	$graph_data = array_merge($graph_data, array("color" => "['#4b0082', '#6AF9C4']"));
	$graph_data=array_merge($graph_data,array("graph_title"=>"Stock Expiring $title in the Next $interval Months"));
	$graph_data=array_merge($graph_data,array("graph_type"=>$graph_type));
	$graph_data=array_merge($graph_data,array("graph_yaxis_title"=>"stock expiring in KSH"));
	$graph_data=array_merge($graph_data,array("graph_categories"=>$category_data ));
	$graph_data=array_merge($graph_data,array("series_data"=>array('total'=>$series_data)));
	$data = array();

	$data['high_graph']= $this->hcmp_functions->create_high_chart_graph($graph_data);
	$data['graph_id']='dem_graph_1';
	return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);

	else:

		$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "Potential Expiry  $title",
			'file_name' => "Stock Expiring $title in the Next $interval Months");
	$row_data = array();
	$column_data = array("Commodity","Date of Expiry","Unit Size","Quantity (Packs)","Quantity (Units)",
		"Unit Cost (Ksh)","Total Cost Expired (Ksh)"
		,"Supplier","Manufacturer","Facility Name","Facility Code","Sub-County","County");
	$excel_data['column_data'] = $column_data;
	 // echo  $and_data; exit;
	$facility_stock_data = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAll("select  c.county, d1.district as subcounty ,temp.drug_name,
		f.facility_code, f.facility_name,temp.manufacture, sum(temp.total) as total_ksh,
		temp.unit_cost,temp.expiry_date,temp.unit_size,temp.units,
		temp.packs
		from districts d1, counties c, facilities f left join
		(
			select  ROUND( SUM(
				f_s.current_balance  / d.total_commodity_units ) * d.unit_cost, 1) AS total,
	ROUND( SUM( f_s.current_balance  / d.total_commodity_units  ), 1) as packs,
	SUM( f_s.current_balance) as units,
	f_s.facility_code,d.id,d.commodity_name as drug_name, f_s.manufacture,
	f_s.expiry_date,d.unit_size,d.unit_cost

	from facility_stocks f_s, commodities d
	where f_s.expiry_date between DATE_ADD(CURDATE(), INTERVAL 1 day) and  DATE_ADD(CURDATE(), INTERVAL $interval MONTH)
	and d.id=f_s.commodity_id
	and year(f_s.expiry_date) !=1970
	AND (f_s.status =1 or f_s.status =2)
	GROUP BY d.id,f_s.facility_code having total >1

	) temp
	on temp.facility_code = f.facility_code
	where  f.district = d1.id
	and c.id=d1.county
	and temp.total>0
	$and_data
	group by temp.id,f.facility_code
	order by temp.drug_name asc,temp.total asc, temp.expiry_date desc
	");
$date=date( "d M y");
array_push($row_data, array("The below commodities will expire in the next $interval months from $date $title  "));
// echo "<pre>";print_r($facility_stock_data);exit;
foreach ($facility_stock_data as $facility_stock_data_item) :
	array_push($row_data, array($facility_stock_data_item["drug_name"],
		$facility_stock_data_item["expiry_date"],
		$facility_stock_data_item["unit_size"],
		$facility_stock_data_item["packs"],
		$facility_stock_data_item["units"],
		$facility_stock_data_item["unit_cost"],
		$facility_stock_data_item["total_ksh"],
		"KEMSA",
		$facility_stock_data_item["manufacture"],
		$facility_stock_data_item["facility_name"],
		$facility_stock_data_item["facility_code"],
		$facility_stock_data_item["subcounty"],
		$facility_stock_data_item["county"]

		));
endforeach;
$excel_data['row_data'] = $row_data;

$this->hcmp_functions->create_excel($excel_data);
endif;

}

public function stock_level_mos($county_id = null, $district_id = null, $facility_code = null, $commodity_id = null, $graph_type = null) {
	$district_id = ($district_id == "NULL") ? null : $district_id;
	$graph_type = ($graph_type == "NULL") ? null : $graph_type;
	$facility_code = ($facility_code == "NULL") ? null : $facility_code;
	$county_id = ($county_id == "NULL") ? null : $county_id;
	$commodity_id = ($commodity_id == "ALL" || $commodity_id == "NULL") ? null : $commodity_id;
		// echo $commodity_id;die;
	$and_data = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
	$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
	$and_data .= ($county_id > 0) ? " AND counties.id='$county_id'" : null;
	$and_data = isset($and_data) ? $and_data : null;
	if ($graph_type=='excel') {
		$and_data .= isset($commodity_id) ? "AND commodities.id =$commodity_id" : "AND d.tracer_item =1";			
	}else{
		$and_data .= isset($commodity_id) ? "AND commodities.id =$commodity_id" : "AND commodities.tracer_item =1";

	}

	$group_by = ($district_id > 0 && isset($county_id) && !isset($facility_code)) ? " ,d.id" : null;
	$group_by .= ($facility_code > 0 && isset($district_id)) ? "  ,f.facility_code" : null;
	$group_by .= ($county_id > 0 && !isset($district_id)) ? " ,c_.id" : null;
	$group_by = isset($group_by) ? $group_by : " ,c_.id";

	$title = '';

	if (isset($county_id)) :
		$county_name = counties::get_county_name($county_id);
	$name = $county_name['county'];
	$title = "$name County";
	elseif (isset($district_id)) :
		$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
	$district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
	$title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
	elseif (isset($facility_code)) :
		$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
	$title = $facility_code_['facility_name'];
	else :
		$title = "National";
	endif;

	if ($graph_type != "excel") :
		$commodity_array ;

	$getdates = Doctrine_Manager::getInstance()->getCurrentConnection()
	->fetchAll("SELECT MIN(created_at) as EarliestDate,MAX(created_at) as LatestDate
		FROM facility_issues");

		//echo '<pre>'; print_r($getdates);echo '<pre>'; exit;
	$early=$getdates[0]['EarliestDate'];
	$late=$getdates[0]['LatestDate'];

	$now = time(); 
	$my_date = strtotime($early);
		$datediff = ($now - $my_date)/(60*60*24);//in days
		$datediff= round($datediff,1);

		if (isset($county_id)) {

			$getdates = Doctrine_Manager::getInstance()->getCurrentConnection()
			->fetchAll("SELECT MIN(created_at) as EarliestDate,MAX(created_at) as LatestDate
				FROM facility_issues 
				inner join facilities on facility_issues.facility_code=facilities.facility_code
				inner join districts on facilities.district=districts.id
				inner join counties on districts.county=counties.id
				inner join commodities on facility_issues.commodity_id=commodities.id where counties.id=$county_id");

		//echo '<pre>'; print_r($getdates);echo '<pre>'; exit;
			$early=$getdates[0]['EarliestDate'];
			$late=$getdates[0]['LatestDate'];

			$now = time(); 
			$my_date = strtotime($early);
		$datediff = ($now - $my_date)/(60*60*24);//in days
		$datediff= round($datediff,1);

		$get_amc = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT commodities.id,commodities.commodity_name,avg(facility_issues.qty_issued) as total_units_consumed,
			(sum(facility_issues.qty_issued)*30/$datediff)/commodities.total_commodity_units as amc_packs,commodities.total_commodity_units FROM hcmp_rtk.facility_issues 
			inner join facilities on facility_issues.facility_code=facilities.facility_code
			inner join districts on facilities.district=districts.id
			inner join counties on districts.county=counties.id inner join commodities on facility_issues.commodity_id=commodities.id where s11_No IN('internal issue','(-ve Adj) Stock Deduction')
			$and_data group by commodities.id");


			//return $get_amc ;	


		$get_totals = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT commodities.id,commodities.commodity_name,sum(facility_stocks.current_balance) 
			as total_bal_units, sum(facility_stocks.current_balance)/commodities.total_commodity_units as cur_bal_packs,commodities.total_commodity_units FROM hcmp_rtk.facility_stocks 
			inner join facilities on facility_stocks.facility_code=facilities.facility_code
			inner join districts on facilities.district=districts.id
			inner join counties on districts.county=counties.id inner join commodities on facility_stocks.commodity_id=commodities.id 
			where commodities.status=1 $and_data group by commodities.id");

	}else {

		$get_amc = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT commodities.id,commodities.commodity_name,avg(facility_issues.qty_issued) as total_units_consumed,
			(sum(facility_issues.qty_issued)*30/$datediff)/commodities.total_commodity_units as amc_packs,commodities.total_commodity_units FROM hcmp_rtk.facility_issues 
			inner join commodities on facility_issues.commodity_id=commodities.id where s11_No IN('internal issue','(-ve Adj) Stock Deduction')
			$and_data group by commodities.id");


			//return $get_amc ;	

		$get_totals = Doctrine_Manager::getInstance()->getCurrentConnection()
		->fetchAll("SELECT commodities.id,commodities.commodity_name,sum(facility_stocks.current_balance) 
			as total_bal_units, sum(facility_stocks.current_balance)/commodities.total_commodity_units as cur_bal_packs,commodities.total_commodity_units FROM hcmp_rtk.facility_stocks inner join commodities on facility_stocks.commodity_id=commodities.id 
			where commodities.status=1 $and_data group by commodities.id");


	}


			//return $get_totals ;	
	$combine=array ();

	for ($i=0; $i < sizeof($get_totals) ; $i++) { 
				//array_push($combine,$get_totals[$i ],$get_amc[$i ]);
		$combine[]=array_merge($get_totals[$i ],$get_amc[$i ]);
	}
			//echo '<pre>'; print_r($combine);echo '<pre>'; exit;

	$category_data = array();
	$series_data = $series_data_ = array();
	$temp_array = $temp_array_ = array();
	$graph_data = array();
	$graph_type = '';

	foreach ($combine as $data) :
		$series_data = array_merge($series_data, array($data["commodity_name"] => (int)$data['cur_bal_packs']/(int)$data['amc_packs']));
	$category_data = array_merge($category_data, array($data["commodity_name"]));
	endforeach;

			// echo "<pre>";print_r($series_data);echo "</pre>";exit;
	$graph_type = 'bar';

	$graph_data = array_merge($graph_data, array("graph_id" => 'dem_graph_mos'));
	$graph_data = array_merge($graph_data, array("graph_title" => "$title Stock Level in Months of Stock (MOS)"));
	$graph_data = array_merge($graph_data, array("graph_type" => $graph_type));
	$graph_data = array_merge($graph_data, array("color" => "['#4572A7','#FFF263', '#6AF9C4']"));
	$graph_data = array_merge($graph_data, array("graph_yaxis_title" => "MOS"));
	$graph_data = array_merge($graph_data, array("graph_categories" => $category_data));
	$graph_data = array_merge($graph_data, array("series_data" => array('total' => $series_data)));
	$data = array();

	$data['high_graph'] = $this -> hcmp_functions -> create_high_chart_graph($graph_data);
			//
	$data['graph_id'] = 'dem_graph_mos';
	return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);
		//
	else :
		$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "Stock Level in Months of Stock $title", 'file_name' => $title . ' MOS');
	$row_data = array();
	$column_data = array("County", "Sub-County", "Facility Name", "Facility Code", "Item Name", "MOS");
	$excel_data['column_data'] = $column_data;
			 //echo '' ; exit;

	$commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT d.id,ct.county,sc.district,f.facility_code,
		f.facility_name,sum(fs.current_balance) as bal
		,sum(fs.current_balance)/d.total_commodity_units as packs,d.total_commodity_units,fs.batch_no,fs.expiry_date,d.commodity_name
		FROM hcmp_rtk.facility_stocks fs
		INNER JOIN facilities f ON  fs.facility_code=f.facility_code
		INNER JOIN commodities d ON  fs.commodity_id=d.id
		INNER JOIN districts sc ON  f.district=sc.id
		INNER JOIN counties ct ON  sc.county=ct.id
		$and_data AND fs.status=1 group by fs.batch_no order by ct.id asc
		
		");


		/*echo'<table><tr>
					<th>County</th>
					<th>Sub-County</th>
					<th>Facility code</th>
					<th>Facility name</th>
					<th>Commodity name</th>
					<th>Batch affected</th>
					<th>Expiry date</th>
					<th>Bal(units)</th>
					<th>Bal(packs)</th>
					<th>AMC(packs)</th>
					<th>MOS(packs)</th>
				</tr>';*/
				$r_data = array();
				$counter = 0;
				foreach ($commodity_array as $key) {
					$commodity=$key['id'];
					$f_code=$key['facility_code'];
					$batch_n=$key['batch_no'];
					$amc = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
						SELECT sum(qty_issued) as amc FROM hcmp_rtk.facility_issues where commodity_id=$commodity and facility_code=$f_code AND batch_no='$batch_n' AND
						date_issued	> DATE_SUB(CURDATE(), INTERVAL 31 DAY)
						");
					foreach ($amc as $val ) {
						$amc= $val['amc'];
						if ($amc<0) {
							$amc=$amc*-1;
						}
						$amc_packs=round(($amc/$key['total_commodity_units']));
						if ($amc_packs<0) {
							$amc_packs=$amc_packs*-1;
						}
						if ($key['bal']<0 ) {
							$bal=$key['bal']*-1;
						}else{
							$bal=$key['bal'];
						}
						if ($key['packs']<0 ) {
							$packs=$key['packs']*-1;
						}else{
							$packs=$key['packs'];
						}


					}


			/*echo'<tr>';
			echo '<td>'.$key['county'].'</td>';
			echo '<td>'.$key['district'].'</td>';
			echo '<td>'.$key['facility_code'].'</td>';
			echo '<td>'.$key['facility_name'].'</td>';
			echo '<td>'.$key['commodity_name'].'</td>';
			echo '<td>'.$key['batch_no'].'</td>';
			echo '<td>'.$key['expiry_date'].'</td>';
			echo '<td>'.$bal.'</td>';
			echo '<td>'.round($packs,2).'</td>';
			echo '<td>'.$amc_packs.'</td>';
			echo '<td>'.round($packs/$amc_packs,2).'</td>';
			echo'</tr>';*/

			$r_data[$counter]["county"] = $key['county'];
			$r_data[$counter]["district"] = $key['district'];
			$r_data[$counter]["facility_code"] = $key['facility_code'];
			$r_data[$counter]["facility_name"] = $key['facility_name'];
			$r_data[$counter]["commodity_name"] = $key['commodity_name'];
			$r_data[$counter]["batch_no"] = $key['batch_no'];
			$r_data[$counter]["expiry_date"] = $key['expiry_date'];
			$r_data[$counter]["balance"] = $bal;
			$r_data[$counter]["bal_packs"] = round($packs,2);
			$r_data[$counter]["amc_packs"] = $amc_packs;
			$r_data[$counter]["amc_units"] = round($packs/$amc_packs,2);

			$counter = $counter + 1;
		}
		// echo '</table>';
		// echo "<pre> MY ARRAY";print_r($r_data);echo "</pre>";exit;
		// exit;
		/*
			$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> 
			fetchAll("select 
					   c.county,d1.district as subcounty, f.facility_name,f.facility_code, d.commodity_name as drug_name,
					case 
					  when (ifnull( round(avg(IFNULL(f_s.current_balance, 0) / IFNULL(f_m_s.total_units, 0)),
					            1) ,0)) >0 then (ifnull( round(avg(IFNULL(f_s.current_balance, 0) / IFNULL(f_m_s.total_units, 0)),
					            1) ,0)) else 0 end as total,f_s.current_balance
					from
					    facilities f,
					    districts d1,
					    counties c,
					    facility_stocks f_s,
					    commodities d
					        left join
					    facility_monthly_stock f_m_s ON f_m_s.`commodity_id` = d.id
					where
					    f_s.facility_code = f.facility_code
					        and f.district = d1.id
					        and d1.county = c.id
					        and f_s.commodity_id = d.id
					        and f_m_s.facility_code = f.facility_code
					        $and_data
					group by d.id,f.facility_code
					order by c.county asc,d1.district asc
			        ");
			*/

			/*foreach ($facility_stock_data as $facility_stock_data_item) :
				array_push($row_data, array($facility_stock_data_item["county"], $facility_stock_data_item["subcounty"], $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["drug_name"], $facility_stock_data_item["total"]));
				endforeach;*/
				array_push($row_data,$r_data);
				$excel_data['row_data'] = $r_data;

			// echo "<pre>";print_r($row_data);echo "</pre>";exit;

				$this -> hcmp_functions -> create_excel($excel_data);
				endif;

			}

	//for getting the stock level in units for the national dashboard
			public function stock_level_units($county_id = null, $district_id = null, $facility_code = null, $commodity_id = null, $graph_type = null) {

				$district_id = ($district_id == "NULL") ? null : $district_id;
				$graph_type = ($graph_type == "NULL") ? null : $graph_type;
				$facility_code = ($facility_code == "NULL") ? null : $facility_code;
				$county_id = ($county_id == "NULL") ? null : $county_id;
				$commodity_id = ($commodity_id == "ALL" || $commodity_id == "NULL") ? null : $commodity_id;

				$and_data = ($district_id > 0) ? " AND d1.id = '$district_id'" : null;
				$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
				$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
				$and_data .= isset($commodity_id) ? "AND d.id =$commodity_id" : "AND d.tracer_item =1";
				$and_data = isset($and_data) ? $and_data : null;

				$group_by = ($district_id > 0 && isset($county_id) && !isset($facility_code)) ? " ,d.id" : null;
				$group_by .= ($facility_code > 0 && isset($district_id)) ? "  ,f.facility_code" : null;
				$group_by .= ($county_id > 0 && !isset($district_id)) ? " ,c.id" : null;
				$group_by = isset($group_by) ? $group_by : " ,c.id";

				$title = '';


				if (isset($county_id)) :
					$county_name = counties::get_county_name($county_id);
				$name = $county_name['county'];
				$title = "$name County";
				elseif (isset($district_id) && ($district_id > 0)) :
					$district_data = districts::get_district_name($district_id);
			//echo $district_data;exit;
				$district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
				$title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
				elseif (isset($facility_code)) :
					$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
				$title = $facility_code_['facility_name'];
				else :
					$title = "National";
				endif;

				if ($graph_type != "excel") :
					$commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> 
				fetchAll("select 
					d.commodity_name as drug_name,
					f_s.current_balance as total
					from
					facilities f,
					districts d1,
					counties c,
					facility_stocks f_s,
					commodities d
					left join
					facility_monthly_stock f_m_s ON f_m_s.`commodity_id` = d.id
					where
					f_s.facility_code = f.facility_code
					and f.district = d1.id
					and d1.county = c.id
					and f_s.commodity_id = d.id
					and f_m_s.facility_code = f.facility_code
					$and_data
					group by d.id $group_by
					");

				$category_data = array();
				$series_data = $series_data_ = array();
				$temp_array = $temp_array_ = array();
				$graph_data = array();
				$graph_type = '';

				foreach ($commodity_array as $data) :
					$series_data = array_merge($series_data, array($data["drug_name"] => (int)$data['total']));
				$category_data = array_merge($category_data, array($data["drug_name"]));
				endforeach;

				$graph_type = 'bar';

				$graph_data = array_merge($graph_data, array("graph_id" => 'dem_graph_mos'));
				$graph_data = array_merge($graph_data, array("graph_title" => "$title Stock Level"));
				$graph_data = array_merge($graph_data, array("graph_type" => $graph_type));
				$graph_data = array_merge($graph_data, array("color" => "['#4b0082','#FFF263', '#6AF9C4']"));
				$graph_data = array_merge($graph_data, array("graph_yaxis_title" => "units"));
				$graph_data = array_merge($graph_data, array("graph_categories" => $category_data));
				$graph_data = array_merge($graph_data, array("series_data" => array('total' => $series_data)));
				$data = array();

				$data['high_graph'] = $this -> hcmp_functions -> create_high_chart_graph($graph_data);
			//
				$data['graph_id'] = 'dem_graph_mos';
				return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);
		//
				else :
					$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "Stock Level in units $title", 'file_name' => $title);
				$row_data = array();
				$column_data = array("County", "Sub-County", "Facility Name", "Facility Code", "Item Name", "Units");
				$excel_data['column_data'] = $column_data;

				$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> 
				fetchAll("select 
					c.county,d1.district as subcounty, 
					f.facility_name,
					f.facility_code, 
					d.commodity_name as drug_name,
					f_s.current_balance
					from
					facilities f,
					districts d1,
					counties c,
					facility_stocks f_s,
					commodities d

					where
					f_s.facility_code = f.facility_code
					and f.district = d1.id
					and d1.county = c.id
					and f_s.commodity_id = d.id
					$and_data
					group by d.id,f.facility_code
					order by c.county asc,d1.district asc
					");
			//echo "<pre>";print_r($facility_stock_data);exit;
				foreach ($facility_stock_data as $facility_stock_data_item) :
					array_push($row_data, array($facility_stock_data_item["county"], $facility_stock_data_item["subcounty"], $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["drug_name"], $facility_stock_data_item["current_balance"]));
				endforeach;
				$excel_data['row_data'] = $row_data;

				$this -> hcmp_functions -> create_excel($excel_data);
				endif;

			}

			public function consumption($county_id = null, $district_id = null, $facility_code = null, $commodity_id = null, $graph_type = null, $from = null, $to = null) {

				$title = '';
				$district_id = ($district_id == "NULL") ? null : $district_id;
				$graph_type = ($graph_type == "NULL") ? null : $graph_type;
				$facility_code = ($facility_code == "NULL") ? null : $facility_code;
				$county_id = ($county_id == "NULL") ? null : $county_id;
				$commodity_id = ($commodity_id == "NULL") ? null : $commodity_id;
				$commodity_array = explode(',', $commodity_id);
				$count_commodities = count($commodity_array);
				$year = ($year == "NULL" || !isset($year)) ? date('Y') : $year;
				$to = ($to == "NULL" || !isset($to)) ? date('Y-m-d') : date('Y-m-d', strtotime(urldecode($to)));

				$and_data = ($district_id > 0) ? " AND d1.id = '$district_id'" : null;
				$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
				$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
				$and_data = isset($and_data) ? $and_data : null;
				if($count_commodities>1){

				}else{
					$and_data .= isset($commodity_id) ? "AND d.id =$commodity_id" : "AND d.tracer_item =1";
				}

		/*$group_by =($district_id>0 && isset($county_id) && !isset($facility_code)) ?" ,d.id" : null;
		 $group_by .=($facility_code>0 && isset($district_id)) ?"  ,f.facility_code" : null;
		 $group_by .=($county_id>0 && !isset($district_id)) ?" ,c_.id" : null;
		 $group_by =isset( $group_by) ?  $group_by: " ,c_.id";*/

		 $time = "Between " . date('j M y', strtotime($from)) . " and " . date('j M y', strtotime($to));

		 if (isset($county_id)) :

		 	$county_name = counties::get_county_name($county_id);
		 $name = $county_name['county'];
		 $title = "$name County";
		//print_r($name);exit;
		 elseif (isset($district_id)) :
		 	$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
		 $district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
		 $title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
		 elseif (isset($facility_code)) :
		 	$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
		 $title = $facility_code_['facility_name'];
		 else :
		 	$title = "National";
		 endif;
		 if ($graph_type != "excel") :
			// echo    .$to; exit;

		 	$commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select d.commodity_name as drug_name,  
		 		round(avg(IFNULL(ABS(f_i.`qty_issued`),0) / IFNULL(d.total_commodity_units,0)),1) as total
		 		from facilities f,  districts d1, counties c, commodities d left join facility_issues f_i on f_i.`commodity_id`=d.id 
		 		where f_i.facility_code = f.facility_code 
		 		and f.district=d1.id 
		 		and d1.county=c.id 
		 		and f_i.`qty_issued`>0
		 		and YEAR(f_i.created_at)=$year 
		 		$and_data
		 		group by d.id
		 		");

		 $category_data = array();
		 $series_data = $series_data_ = array();
		 $temp_array = $temp_array_ = array();
		 $graph_data = array();
		 $graph_type = '';

		 foreach ($commodity_array as $data) :
		 	$series_data = array_merge($series_data, array($data["drug_name"] => (int)$data['total']));
		 $category_data = array_merge($category_data, array($data["drug_name"]));
		 endforeach;

		 $graph_type = 'bar';
		 $graph_data = array_merge($graph_data, array("graph_id" => 'dem_graph_consuption'));
		 $graph_data = array_merge($graph_data, array("graph_title" => "$title Consumption (Packs) for $year"));
		 $graph_data = array_merge($graph_data, array("graph_type" => $graph_type));
		 $graph_data = array_merge($graph_data, array("color" => "['#AA4643', '#89A54E', '#80699B']"));
		 $graph_data = array_merge($graph_data, array("graph_yaxis_title" => "Packs"));
		 $graph_data = array_merge($graph_data, array("graph_categories" => $category_data));
		 $graph_data = array_merge($graph_data, array("series_data" => array('total' => $series_data)));
		 $data = array();

		 $data['high_graph'] = $this -> hcmp_functions -> create_high_chart_graph($graph_data);
		 $data['graph_id'] = 'dem_graph_consuption';
		 return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);
		 else :
		 	$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "$title Consumption (Packs) $time", 'file_name' => $title . ' Consumption');
		 $row_data = array();			
		 $column_data = array("County", "Sub-County", "Facility Name", "Facility Code", "Item Name", "Consumption (Packs)");
		 for ($i=1; $i < $count_commodities; $i++) { 
		 	$item_name = "Item Name";
		 	$consumption = "Consumption (Packs)";
		 	array_push($column_data, $item_name);
		 	array_push($column_data, $consumption);
		 }
			// echo "<pre>";
			// print_r($column_data);die;
		 $excel_data['column_data'] = $column_data;
			// echo ; exit;

		 if($count_commodities>1){
		 	$sql = "SELECT c.county,d1.district AS subcounty,f.facility_name,f.facility_code FROM
		 	facilities f,districts d1,counties c WHERE f.district = d1.id AND d1.county = c.id $and_data					    
		 	ORDER BY c.county ASC , d1.district ASC";
				// echo "$sql";die;
		 	$facility_data = $this->db->query($sql)->result_array();

		 	foreach ($facility_data as $key => $value) {
		 		$county = $value['county'];
		 		$subcounty = $value['subcounty'];
		 		$facility_name = $value['facility_name'];
		 		$facility_code = $value['facility_code'];
		 		$final_array[$facility_code] = array($county,$subcounty,$facility_name,$facility_code);					
		 		for ($i=0; $i <$count_commodities ; $i++) { 
		 			$commodity_id =  $commodity_array[$i];
		 			$commodity_details = Commodities::get_commodity_name($commodity_id);
		 			$drug_name = $commodity_details[0]['commodity_name'];
		 			array_push($final_array[$facility_code],$drug_name);

		 			$sql_commodity_details = "SELECT ROUND(AVG(IFNULL(ABS(f_i.`qty_issued`), 0) / IFNULL(d.total_commodity_units, 0)),1) AS total
		 			FROM commodities d LEFT JOIN  facility_issues f_i ON f_i.`commodity_id` = d.id
		 			WHERE f_i.facility_code = '$facility_code'
		 			AND f_i.`qty_issued` > 0
		 			and f_i.created_at between '$from' and '$to'
		 			AND d.id = '$commodity_id'
		 			GROUP BY d.id , f_i.facility_code";
		 			$consuption_details = $this->db->query($sql_commodity_details)->result_array();
		 			if(count($consuption_details)==0){
		 				$total = 'No Data Available';
		 				array_push($final_array[$facility_code],$total);

		 			}else{
		 				foreach ($consuption_details as $keys => $values) {												
		 					$total = $values['total'];	
		 					$total = ($total=='') ? 'No Data Available' : $total ;																	
		 					array_push($final_array[$facility_code],$total);
		 				}

		 			}

		 		}


		 	}
		 	$row_data = $final_array;
		 }else{
		 	$commodity_id = $commodity_array[0];
		 	$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select 
		 		c.county,d1.district as subcounty, f.facility_name,f.facility_code, d.commodity_name as drug_name,
		 		round(avg(IFNULL(ABS(f_i.`qty_issued`), 0) / IFNULL(d.total_commodity_units, 0)),
		 			1) as total
		 	from
		 	facilities f,
		 	districts d1,
		 	counties c,
		 	commodities d
		 	left join facility_issues f_i on f_i.`commodity_id`=d.id 
		 	where f_i.facility_code = f.facility_code 
		 	and f.district=d1.id 
		 	and d1.county=c.id 
		 	and f_i.`qty_issued`>0
		 	and f_i.created_at between '$from' and '$to'
		 	$and_data
		 	group by d.id , f.facility_code
		 	order by c.county asc , d1.district asc
		 	");

		 	foreach ($facility_stock_data as $facility_stock_data_item) :
		 		array_push($row_data, array($facility_stock_data_item["county"], $facility_stock_data_item["subcounty"], $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["drug_name"], $facility_stock_data_item["total"]));
		 	endforeach;
		 }

		 $excel_data['row_data'] = $row_data;

		 $this -> hcmp_functions -> create_excel($excel_data);
		 endif;

		}

		public function old_order($year = null, $county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
			$district_id = ($district_id == "NULL") ? null : $district_id;
			$graph_type = ($graph_type == "NULL") ? null : $graph_type;
			$facility_code = ($facility_code == "NULL") ? null : $facility_code;
			$county_id = ($county_id == "NULL") ? null : $county_id;
			$year = ($year == "NULL") ? date('Y') : $year;

			$array_months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
			// echo "<pre>";
			// print_r($array_months);die;
			$and_data = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
			$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
			$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
			$and_data .= ($year > 0) ? " and year(o.`order_date`) =$year" : null;
			$and_data = isset($year) ? $and_data : null;

		//echo  ; exit;
			$commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
				sum(o.`order_total`) as total,DATE_FORMAT( o.`order_date`,  '%b' ) AS cal_month
				FROM
				facilities f, districts d, counties c,`facility_orders` o
				WHERE
				o.facility_code=f.facility_code
				and f.district=d.id and d.county=c.id
				$and_data
				group by month(o.`order_date`)
				");

			$commodity_array_2 = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
				sum(o.`order_total`) as total,DATE_FORMAT( o.`order_date`,  '%b' ) AS cal_month
				FROM
				facilities f, districts d, counties c,`facility_orders` o
				WHERE
				o.facility_code=f.facility_code
				and f.district=d.id and d.county=c.id and o.status = 4
				$and_data
				group by month(o.`order_date`)
				");

		// var_dump($commodity_array_2);
		// exit;

			$category_data = array();
			$series_data = $series_data_ = array();
			$temp_array = $temp_array_ = array();
			$graph_data = array();

			$title = '';

			if ($graph_type != "excel") :
				if (isset($county_id)) :
					$county_name = counties::get_county_name($county_id);
				$name = $county_name['county'];
				$title = "$name county";
				elseif (isset($district_id)) :
					$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
				$district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
				$title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
				elseif (isset($facility_code)) :
					$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
				$title = $facility_code_['facility_name'];
				else :
					$title = "National";
				endif;

				foreach ($commodity_array as $data) :
					$series_data = array_merge($series_data, array($data["cal_month"] => (int)$data['total']));
				$category_data = array_merge($category_data, array($data["cal_month"]));
				endforeach;

				$series_data2 = $series_data_2 = $category_data_2 = array();
				foreach ($commodity_array_2 as $data_2) :
					$series_data_2 = array_merge($series_data_2, array($data_2["cal_month"] => (int)$data_2['total']));
				$category_data_2 = array_merge($category_data_2, array($data_2["cal_month"]));
				endforeach;

			//$graph_details = array('' => , );;
			// array_merge($series_data,$series_data_2);
			// echo "<pre>";print_r($series_data_2);echo "</pre>";exit;

				$graph_type = 'column';

				$graph_data = array_merge($graph_data, array("graph_id" => 'dem_graph_order'));
				$graph_data = array_merge($graph_data, array("graph_title" => "$year $title Order Cost"));
				$graph_data = array_merge($graph_data, array("graph_type" => $graph_type));
				$graph_data = array_merge($graph_data, array("color" => "['#92A8CD', '#A47D7C']"));
				$graph_data = array_merge($graph_data, array("graph_yaxis_title" => "Cost in KSH"));
				$graph_data = array_merge($graph_data, array("graph_categories" => $category_data));
				$graph_data = array_merge($graph_data, array("series_data" => array('Cost of Orders Made' => $series_data, 'Cost of Orders delivered' => $series_data_2)));
				$data = array();

			//seth
				$data['high_graph'] = $this -> hcmp_functions -> create_high_chart_graph($graph_data);
			// echo $data['high_graph'];exit;

				$data['graph_id'] = 'dem_graph_order';
				return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);
				else :
					$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "$year $title Order Cost", 'file_name' => "$year $title Order Cost (KSH)");
				$row_data = array();
				$column_data = array("Date of Order Placement", "Date of Order Approval", "Total Order Cost (Ksh)", "Date of Delivery", "Lead Time (Order Placement to Delivery)", "Supplier", "Facility Name", "Facility Code", "Sub-County", "County");
				$excel_data['column_data'] = $column_data;
			//echo  ; exit;
				$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT c.county,d.district as sub_county, f.facility_name, f.facility_code, 
					DATE_FORMAT(`order_date`,'%d %b %y') as order_date, 
					DATE_FORMAT(`approval_date`,'%d %b %y')  as approval_date,
					DATE_FORMAT(`deliver_date`,'%d %b %y')  as delivery_date, 
					DATEDIFF(`approval_date`,`order_date`) as tat_order_approval,
					DATEDIFF(`deliver_date`,`approval_date`) as tat_approval_deliver,
					DATEDIFF(`deliver_date`,`order_date`) as tat_order_delivery
					, sum(o.`order_total`) as total 
					from facility_orders o, facilities f, districts d, counties c 
					where f.facility_code=o.facility_code and f.district=d.id 
					and c.id=d.county $and_data
					group by o.id order by c.county asc ,d.district asc , 
					f.facility_name asc 
					");
				array_push($row_data, array("The orders below were placed $year $title"));
				foreach ($facility_stock_data as $facility_stock_data_item) :
					array_push($row_data, array($facility_stock_data_item["order_date"], $facility_stock_data_item["approval_date"], $facility_stock_data_item["total"], $facility_stock_data_item["delivery_date"], $facility_stock_data_item["tat_order_delivery"], "KEMSA", $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["sub_county"], $facility_stock_data_item["county"]));
				endforeach;
				$excel_data['row_data'] = $row_data;

				$this -> hcmp_functions -> create_excel($excel_data);
				endif;

			}

			public function order($year = null, $county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
			$district_id = ($district_id == "NULL") ? null : $district_id;
			$graph_type = ($graph_type == "NULL") ? null : $graph_type;
			$facility_code = ($facility_code == "NULL") ? null : $facility_code;
			$county_id = ($county_id == "NULL") ? null : $county_id;
			$year = ($year == "NULL") ? date('Y') : $year;			
			$array_months_texts = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
			$array_months = array(1,2,3,4,5,6,7,8,9,10,11,12);

			$and_data = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
			$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
			$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
			$and_data .= ($year > 0) ? " and year(o.`order_date`) =$year" : null;
			
			$new_commodity_array = array();
			foreach ($array_months as $key => $value) {
				$month_name = $array_months_texts[$key];								
				$month_and = ($year > 0) ? " and MONTH(o.`order_date`) ='$value'" : null;				
				$and_data = isset($year) ? $and_data : null;	

				$commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
				sum(o.`order_total`) as total,DATE_FORMAT( o.`order_date`,  '%b' ) AS cal_month
				FROM
				facilities f, districts d, counties c,`facility_orders` o
				WHERE
				o.facility_code=f.facility_code
				and f.district=d.id and d.county=c.id
				$and_data $month_and
				group by month(o.`order_date`)");	

				$commodity_array_2 = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
				sum(o.`order_total`) as total,DATE_FORMAT( o.`order_date`,  '%b' ) AS cal_month
				FROM
				facilities f, districts d, counties c,`facility_orders` o
				WHERE
				o.facility_code=f.facility_code
				and f.district=d.id and d.county=c.id and o.status = 4
				$and_data $month_and
				group by month(o.`order_date`)
				");

				$count_array1 = count($commodity_array);			
				if($count_array1>0){
					foreach ($commodity_array as $key => $value) {
						$new_commodity_array[] = array('cal_month'=>$month_name,'total'=>$value['total']);
					}
				}else{
					$new_commodity_array[] = array('cal_month'=>$month_name,'total'=>0);
				}

				$count_array2 = count($commodity_array_2);			
				if($count_array2>0){
					foreach ($commodity_array_2 as $key => $value) {
						$new_commodity_array_2[] = array('cal_month'=>$month_name,'total'=>$value['total']);
					}
				}else{
					$new_commodity_array_2[] = array('cal_month'=>$month_name,'total'=>0);
				}
				
			}
			// $commodity_array = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			// 	sum(o.`order_total`) as total,DATE_FORMAT( o.`order_date`,  '%b' ) AS cal_month
			// 	FROM
			// 	facilities f, districts d, counties c,`facility_orders` o
			// 	WHERE
			// 	o.facility_code=f.facility_code
			// 	and f.district=d.id and d.county=c.id
			// 	$and_data
			// 	group by month(o.`order_date`)
			// 	");

			// $commodity_array_2 = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			// 	sum(o.`order_total`) as total,DATE_FORMAT( o.`order_date`,  '%b' ) AS cal_month
			// 	FROM
			// 	facilities f, districts d, counties c,`facility_orders` o
			// 	WHERE
			// 	o.facility_code=f.facility_code
			// 	and f.district=d.id and d.county=c.id and o.status = 4
			// 	$and_data
			// 	group by month(o.`order_date`)
			// 	");

			$category_data = array();
			$series_data = $series_data_ = array();
			$temp_array = $temp_array_ = array();
			$graph_data = array();

			$title = '';

			if ($graph_type != "excel") :
				if (isset($county_id)) :
					$county_name = counties::get_county_name($county_id);
				$name = $county_name['county'];
				$title = "$name county";
				elseif (isset($district_id)) :
					$district_data = (isset($district_id) && ($district_id > 0)) ? districts::get_district_name($district_id) -> toArray() : null;
				$district_name_ = (isset($district_data)) ? " :" . $district_data[0]['district'] . " Subcounty" : null;
				$title = isset($facility_code) && isset($district_id) ? "$district_name_ : $facility_name" : (isset($district_id) && !isset($facility_code) ? "$district_name_" : "$name County");
				elseif (isset($facility_code)) :
					$facility_code_ = isset($facility_code) ? facilities::get_facility_name_($facility_code) : null;
				$title = $facility_code_['facility_name'];
				else :
					$title = "National";
				endif;

				
				foreach ($new_commodity_array as $keyz=>$data) :
					$cal_month = $data["cal_month"];
					$total = $data["total"];					
					$series_data = array_merge($series_data, array($cal_month => (int)$total));
				$category_data = array_merge($category_data, array($data["cal_month"]));
				endforeach;
				// die;
				// foreach ($commodity_array as $data) :
				// 	$series_data = array_merge($series_data, array($data["cal_month"] => (int)$data['total']));
				// $category_data = array_merge($category_data, array($data["cal_month"]));
				// endforeach;

				$series_data2 = $series_data_2 = $category_data_2 = array();

				foreach ($new_commodity_array_2 as $keyz=>$data) :
					$cal_month = $data["cal_month"];
					$total = $data["total"];					
					$series_data_2 = array_merge($series_data_2, array($cal_month => (int)$total));
				$category_data_2 = array_merge($category_data_2, array($data["cal_month"]));
				endforeach;
				// foreach ($commodity_array_2 as $data_2) :
				// 	// echo "<pre>";
				// 	// print_r($data_2);die;
				// 	$series_data_2 = array_merge($series_data_2, array($data_2["cal_month"] => (int)$data_2['total']));
				// $category_data_2 = array_merge($category_data_2, array($data_2["cal_month"]));
				// endforeach;
// 
			//$graph_details = array('' => , );;
			// array_merge($series_data,$series_data_2);
			// echo "<pre>";print_r($series_data_2);echo "</pre>";exit;

				$graph_type = 'column';

				$graph_data = array_merge($graph_data, array("graph_id" => 'dem_graph_order'));
				$graph_data = array_merge($graph_data, array("graph_title" => "$year $title Order Cost"));
				$graph_data = array_merge($graph_data, array("graph_type" => $graph_type));
				$graph_data = array_merge($graph_data, array("color" => "['#92A8CD', '#A47D7C']"));
				$graph_data = array_merge($graph_data, array("graph_yaxis_title" => "Cost in KSH"));
				$graph_data = array_merge($graph_data, array("graph_categories" => $category_data));
				$graph_data = array_merge($graph_data, array("series_data" => array('Cost of Orders Made' => $series_data, 'Cost of Orders delivered' => $series_data_2)));
				$data = array();

			//seth
				$data['high_graph'] = $this -> hcmp_functions -> create_high_chart_graph($graph_data);
			// echo $data['high_graph'];exit;

				$data['graph_id'] = 'dem_graph_order';
				return $this -> load -> view("shared_files/report_templates/high_charts_template_v_national", $data);
				else :
					$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "$year $title Order Cost", 'file_name' => "$year $title Order Cost (KSH)");
				$row_data = array();
				$column_data = array("Date of Order Placement", "Date of Order Approval", "Total Order Cost (Ksh)", "Date of Delivery", "Lead Time (Order Placement to Delivery)", "Supplier", "Facility Name", "Facility Code", "Sub-County", "County");
				$excel_data['column_data'] = $column_data;
			//echo  ; exit;
				$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT c.county,d.district as sub_county, f.facility_name, f.facility_code, 
					DATE_FORMAT(`order_date`,'%d %b %y') as order_date, 
					DATE_FORMAT(`approval_date`,'%d %b %y')  as approval_date,
					DATE_FORMAT(`deliver_date`,'%d %b %y')  as delivery_date, 
					DATEDIFF(`approval_date`,`order_date`) as tat_order_approval,
					DATEDIFF(`deliver_date`,`approval_date`) as tat_approval_deliver,
					DATEDIFF(`deliver_date`,`order_date`) as tat_order_delivery
					, sum(o.`order_total`) as total 
					from facility_orders o, facilities f, districts d, counties c 
					where f.facility_code=o.facility_code and f.district=d.id 
					and c.id=d.county $and_data
					group by o.id order by c.county asc ,d.district asc , 
					f.facility_name asc 
					");
				array_push($row_data, array("The orders below were placed $year $title"));
				foreach ($facility_stock_data as $facility_stock_data_item) :
					array_push($row_data, array($facility_stock_data_item["order_date"], $facility_stock_data_item["approval_date"], $facility_stock_data_item["total"], $facility_stock_data_item["delivery_date"], $facility_stock_data_item["tat_order_delivery"], "KEMSA", $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["sub_county"], $facility_stock_data_item["county"]));
				endforeach;
				$excel_data['row_data'] = $row_data;

				$this -> hcmp_functions -> create_excel($excel_data);
				endif;

			}
			public function get_lead_infor($year = null, $county_id = null, $district_id = null, $facility_code = null, $graph_type = null) {
				$district_id = ($district_id == "NULL") ? null : $district_id;
				$graph_type = ($graph_type == "NULL") ? null : $graph_type;
				$facility_code = ($facility_code == "NULL") ? null : $facility_code;
				$county_id = ($county_id == "NULL") ? null : $county_id;
				$year = ($year == "NULL") ? date('Y') : $year;
				$and = ($district_id > 0) ? " AND d.id = '$district_id'" : null;
				$and .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
				$and .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
		// $and =isset( $and) ?  $and:null;
				$and .= ($year > 0) ? " and year(o.`order_date`) =$year" : null;

				if ($graph_type != "excel") :
					$using_hcmp = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT 
						avg( ifnull(DATEDIFF(`approval_date`, `order_date`),0)) as tat_order_approval,
						avg( ifnull(DATEDIFF(`deliver_date`,`approval_date`),0)) as tat_approval_delivery,
						avg( ifnull(DATEDIFF( `deliver_date`,`order_date`),0)) as tat_order_delivery
						from
						facility_orders o,
						facilities f,
						districts d,
						counties c
						where
						f.facility_code = o.facility_code
						and f.district = d.id
						and c.id = d.county
						$and
						");

				$one = $this -> get_time($using_hcmp[0]['tat_order_approval']);
				$two = $this -> get_time($using_hcmp[0]['tat_approval_delivery']);
				$three = $this -> get_time($using_hcmp[0]['tat_order_delivery']);
				$table = "
				<table class='table table-bordered'>
					<tr><td>Order Placement - Order Approval</td><td>$one</td></tr>
					<tr><td>Order Approval - Order Delivery</td><td>$two</td></tr>
					<tr><td>Order Placement - Order Delivery</td><td>$three</td></tr>
				</table> 
				";
				echo $table;
				else :

					$excel_data = array('doc_creator' => "HCMP", 'doc_title' => "Order Lead Time", 'file_name' => "Order Lead Time");
				$row_data = array();
				$column_data = array("Date of Order Placement", "Date of Order Approval", "Total Order Cost (Ksh)", "Date of Delivery", "Lead Time (Order Placement to Delivery)", "Supplier", "Facility Name", "Facility Code", "Sub-County", "County");
				$excel_data['column_data'] = $column_data;
			//echo  ; exit;
				$facility_stock_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT c.county,d.district as sub_county, f.facility_name, f.facility_code, 
					DATE_FORMAT(`order_date`,'%d %b %y') as order_date, 
					DATE_FORMAT(`approval_date`,'%d %b %y')  as approval_date,
					DATE_FORMAT(`deliver_date`,'%d %b %y')  as delivery_date, 
					DATEDIFF(`approval_date`,`order_date`) as tat_order_approval,
					DATEDIFF(`deliver_date`,`approval_date`) as tat_approval_deliver,
					DATEDIFF(`deliver_date`,`order_date`) as tat_order_delivery
					, sum(o.`order_total`) as total 
					from facility_orders o, facilities f, districts d, counties c 
					where f.facility_code=o.facility_code and f.district=d.id 
					and c.id=d.county $and
					group by o.id order by c.county asc ,d.district asc , 
					f.facility_name asc 
					");
				array_push($row_data, array("The orders below were placed $year"));
				foreach ($facility_stock_data as $facility_stock_data_item) :
					array_push($row_data, array($facility_stock_data_item["order_date"], $facility_stock_data_item["approval_date"], $facility_stock_data_item["total"], $facility_stock_data_item["delivery_date"], $facility_stock_data_item["tat_order_delivery"], "KEMSA", $facility_stock_data_item["facility_name"], $facility_stock_data_item["facility_code"], $facility_stock_data_item["sub_county"], $facility_stock_data_item["county"]));
				endforeach;
				$excel_data['row_data'] = $row_data;

				$this -> hcmp_functions -> create_excel($excel_data);
				endif;
			}

			public function get_time($days) {
				switch (true) {
					case $days==1 :
					$time = "$days day";
					break;
					case $days>1 && $days<=7 :
					$days = ceil($days);
					$time = "$days days";
					break;

					case $days>7 && $days<=30 :
					$new = ceil($days / 7);
					if ($new === 1) {
						$time = "$new week";
					} else {
						$extra = $days%=7;
						if ($extra <= 1) {
							$time = "$new weeks  $extra day";
						} else {
							$time = "$new weeks  $extra days";
						}

					}

					break;

					case $days>30 && $days<=365 :
					$new = ceil($days / 30);
					if ($new == 1) {
						$time = "$new month";
					} else {
						$time = "$new months";
					}

					break;

					default :
					$time = "N/A";
					break;
				}
				return $time;
			}

			public function demo_accounts() {
				$facility_user_data = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select distinct
					user.id,
					user.username,
					user.usertype_id,
					ifnull(log.`action`, 0) as login
					from
					user
					left join
					log ON log.user_id = user.id and log.start_time_of_event =(select max(start_time_of_event) from log where log.user_id=user.id)
					where
					user.username like '%@hcmp.com'
					group by user.id
					order by user.id asc
					");

				$graph_data = $series_data = array();
				$total_active_users = $total_inactive_users = 0;
				$status = '';
				$total = count($facility_user_data);
				foreach ($facility_user_data as $facility_user_data) :
					if ($facility_user_data['login'] == "Logged In") {
						$total_active_users++;
						$status = '<button type="button" class="btn btn-xs btn btn-danger">In Use</button>';
					} else {
						$total_inactive_users++;
						$status = '<button type="button" class="btn btn-xs btn-success">Available</button>';
					}
					array_push($series_data, array($facility_user_data['username'], 123456, $status));
					endforeach;
					array_push($series_data, array('', 'Total Available Accounts:', $total_inactive_users));
					array_push($series_data, array('', 'Total Accounts In Use :', $total_active_users));
					array_push($series_data, array('', 'Total Demo Accounts:', $total));

					$category_data = array( array("User Name", " Password ", "Status"));

					$graph_data = array_merge($graph_data, array("table_id" => 'dem_graph_1'));
					$graph_data = array_merge($graph_data, array("table_header" => $category_data));
					$graph_data = array_merge($graph_data, array("table_body" => $series_data));
					$data['content_view'] = $this -> hcmp_functions -> create_data_table($graph_data);
					$data['title'] = "Demo User Accounts";
					$data['banner_text'] = "Demo User Accounts";
					$this -> load -> view('shared_files/template/plain_template_v', $data);

				}

				public function reports() {

		//$data['county'] = Counties::getAll();
		//Added function to display oonly the counties that are currently using HCMP
					$counties = Counties::get_counties_all_using_HCMP();
					$data['county'] = $counties;

					$data['commodities'] = Commodities::get_all();		
					$data['sub_county'] = Districts::getAll();
					$this -> load -> view('national/reports_home', $data);

				}

				public function facilities_json() {

					echo json_encode(facilities::getAll_json());

				}

				public function commodities_json() {

					echo json_encode(Commodities::getAll_json());

				}

				public function consumption_report() {

					$county = $_POST['county'];
					$sub_county = $_POST['sub_county'];
					$facility_id = $_POST['facility_id'];
					$email_address = $_POST[''];
					$username = $_POST['username'];
					$facility_id = $_POST['facility_id'];
				}

			}
			?>
