<?php
/**
 * @author Kariuki
 */
class Facility_stocks extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('id', 'int');
		$this -> hasColumn('facility_code', 'int');
		$this -> hasColumn('commodity_id', 'int');
		$this -> hasColumn('batch_no', 'varchar', 50);
		$this -> hasColumn('manufacture', 'varchar', 100);
		$this -> hasColumn('initial_quantity', 'int');
		$this -> hasColumn('current_balance', 'int');
		$this -> hasColumn('source_of_commodity', 'int');
		$this -> hasColumn('other_source_id', 'int');
		$this -> hasColumn('expiry_date', 'date');
		$this -> hasColumn('date_added', 'date');
		$this -> hasColumn('date_modified', 'date');
		$this -> hasColumn('status', 'int');
	}
	public function setUp() {
		$this -> setTableName('facility_stocks');
		$this -> hasMany('commodities as commodity_detail', array('local' => 'commodity_id', 'foreign' => 'id'));
		$this -> hasMany('Commodities as Code', array('local' => 'commodity_id', 'foreign' => 'id'));
	}
	public static function get_all_active($facility_code) {
		$query = Doctrine_Query::create() -> select("*") -> from("facility_stocks") -> where("facility_code=$facility_code and status=1");
		$commodities = $query -> execute();
		return $commodities;
	}//save the data on to the table
	public static function get_all_facility($facility_code,$commodity_id) {
		
		$commodities = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll(" SELECT sum(current_balance) as current_bal FROM facility_stocks where facility_code=$facility_code 
			and commodity_id=$commodity_id and status=1 ");
		return $commodities;
	}

	public function get_current_stock_for_reversal($facility_code,$commodity_id,$batch_no){
		$sql = "SELECT * FROM facility_stocks where facility_code = '$facility_code' and commodity_id = '$commodity_id' and batch_no='$batch_no'";
        return $this->db->query($sql)->result_array();	
	}

	public function get_facility_batches($facility_code){
		$current_date = date('Y-m-d',strtotime('NOW'));
		$sql = "SELECT * FROM facility_stocks where facility_code='$facility_code' and current_balance >0 and expiry_date > '$current_date'";
        return $this->db->query($sql)->result_array();		
	}
	
	public function get_facilty_stock_id($id){
		$sql = "select current_balance from facility_stocks where id = '$id' LIMIT 0,1";
        return $this->db->query($sql)->result_array();
	}

	public static function get_all() {
		$query = Doctrine_Query::create() -> select("*") -> from("facility_stocks");
		$commodities = $query -> execute();
		return $commodities;
	}//save the data on to the table
	public static function update_facility_stock($data_array) {
		$o = new facility_stocks();
		$o -> fromArray($data_array);
		$o -> save();
		return TRUE;
	}// get the total balance of a specific item within a balance
	public static function get_facility_commodity_total($facility_code, $commodity_id = null, $date_added = null) {
		$date_checker = isset($date_added) ? " and date_added like '%$date_added%'" : null;
		$commodity_id = isset($commodity_id) ? "and commodity_id=$commodity_id" : null;
		$query = Doctrine_Query::create() -> select("commodity_id,ifnull(sum(current_balance),0) as commodity_balance") -> from("facility_stocks") -> where("facility_code='$facility_code' $commodity_id  $date_checker and status='1'") -> groupBy("commodity_id");
		$stocks = $query -> execute();
		return $stocks;
	}// get all facility stock commodity id, options check if the user wants batch data or commodity grouped data and return the total
	public static function get_distinct_stocks_for_this_county_store($county_id){
		$store_stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT DISTINCT
			    dst.commodity_id AS commodity_id,
			    fs.id AS facility_stock_id,
			    dst.expiry_date,
			    c.commodity_name,
			    c.commodity_code,
			    c.unit_size,
			    dst.total_balance AS store_commodity_balance,
			    ROUND(((dst.total_balance) / c.total_commodity_units),
			            1) AS pack_balance,
			    c.total_commodity_units,
			    fs.manufacture,
			    c_s.source_name,
			    fs.batch_no,
			    c_s.id AS source_id
			FROM
			    facility_stocks fs,
			    commodity_source c_s,
			    county_drug_store_issues ds,
			    commodities c,
			    county_drug_store_totals dst
			WHERE
			    ds.county_id = '1'
			        AND dst.county_id = '1'
			        AND dst.expiry_date >= NOW()
			        AND dst.commodity_id = fs.commodity_id
			        AND c.id = dst.commodity_id
			        AND dst.total_balance > 0
			GROUP BY dst.commodity_id
			ORDER BY fs.expiry_date ASC
			
		");
		return $store_stocks;
	}
	public static function get_distinct_stocks_for_this_district_store($district_id) {
		$store_stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT DISTINCT
			    dst.commodity_id AS commodity_id,
			    fs.id AS facility_stock_id,
			    dst.expiry_date,
			    c.commodity_name,
			    c.commodity_code,
			    c.unit_size,
			    dst.total_balance AS store_commodity_balance,
			    ROUND(((dst.total_balance) / c.total_commodity_units),
			            1) AS pack_balance,
			    c.total_commodity_units,
			    fs.manufacture,
			    c_s.source_name,
			    fs.batch_no,
			    c_s.id AS source_id
			FROM
			    facility_stocks fs,
			    commodity_source c_s,
			    drug_store_issues ds,
			    commodities c,
			    drug_store_totals dst
			WHERE
			    ds.district_id = '$district_id'
			        AND dst.district_id = '$district_id'
			        AND dst.expiry_date >= NOW()
			        AND dst.commodity_id = fs.commodity_id
			        AND c.id = dst.commodity_id
			        AND dst.total_balance > 0
			GROUP BY dst.commodity_id
			ORDER BY fs.expiry_date ASC
			");
		return $store_stocks;
	}
	public static function get_district_store_commodities($district_id) {
		$store_comm = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT DISTINCT commodity_id as commodity_id from drug_store_issues");
		return $store_comm;
	}
	public static function get_district_store_total_commodities($district_id) {
		$store_comm = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT * from drug_store_totals");
		return $store_comm;
	}
	public static function get_distinct_stocks_for_this_facility($facility_code, $checker = null, $exception = null) {
		//random comments

		// $addition = isset($checker) ? ($checker === 'batch_data') ? 'and fs.current_balance>0 group by fs.id,c.id order by c.commodity_name asc,fs.expiry_date asc,fs.batch_no desc' : 'and fs.current_balance>0 group by fs.commodity_id order by c.commodity_name asc,fs.expiry_date asc,fs.batch_no desc' : null;
		$addition = isset($checker) ? ($checker === 'batch_data') ? 'and fs.current_balance>0 group by fs.id,c.id order by c.commodity_name asc,fs.batch_no asc,fs.expiry_date asc' : 'and fs.current_balance>0 group by fs.commodity_id order by c.commodity_name asc,fs.batch_no desc' : null;

		// $addition = isset($checker) ? ($checker === 'batch_data') ? 'and fs.current_balance>0 group by fs.id,c.id order by c.commodity_name asc,fs.batch_no asc,fs.expiry_date asc' : 'and fs.current_balance>0 group by fs.commodity_id order by c.commodity_name asc,fs.batch_no desc' : null;
		$check_expiry_date = isset($exception) ? null : " and fs.expiry_date >= NOW()";
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT DISTINCT c.id as commodity_id, fs.id as facility_stock_id,fs.expiry_date,c.commodity_name,c.commodity_code,
			c.unit_size,fs.current_balance as commodity_balance, round((fs.current_balance / c.total_commodity_units) ,1) as pack_balance,
			c.total_commodity_units,fs.manufacture,
			c_s.source_name, fs.batch_no, c_s.id as source_id from facility_stocks fs, commodities c, commodity_source c_s
			where fs.facility_code ='$facility_code' $check_expiry_date 
			and c.id=fs.commodity_id and fs.status='1' AND c_s.id = fs.source_of_commodity $addition 
			");
		
		return $stocks;
	}
	public static function get_county_stock_amc($county_id){
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			c.id AS commodity_id,
			cs.id AS drug_store_stock_id,
			c.commodity_name,
			c.commodity_code,
			c.unit_size,
			fs.expiry_date,
			ROUND((cs.total_balance), 1) AS commodity_balance,
			ROUND(((cs.total_balance) / c.total_commodity_units),
				1) AS pack_balance,
			c.total_commodity_units,
			c_s.source_name,
			c_s.id AS source_id,
			CASE temp.selected_option
			WHEN 'Pack_Size' THEN ROUND(temp.consumption_level, 1)
			WHEN
			'Unit_Size'
			THEN
			ROUND(temp.total_units / temp.consumption_level,
				1)
			ELSE 0
			END AS amc
			FROM
			commodity_source c_s,
			county_drug_store_totals cs,
			facility_stocks fs,
			commodities c
			LEFT JOIN
			facility_monthly_stock temp ON temp.commodity_id = c.id
			WHERE
			cs.county_id = '$county_id'
			AND fs.expiry_date >= NOW()
			AND fs.commodity_id= cs.commodity_id
			AND c.id = cs.commodity_id
			GROUP BY c.id
			");
		return $stocks;
	}
	public static function get_district_stock_amc($district_id) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("			
			SELECT 
			c.id AS commodity_id,
			ds.id AS drug_store_stock_id,
			c.commodity_name,
			c.commodity_code,
			c.unit_size,
			fs.expiry_date,
			ROUND((ds.total_balance), 1) AS commodity_balance,
			ROUND(((ds.total_balance) / c.total_commodity_units),
				1) AS pack_balance,
			c.total_commodity_units,
			c_s.source_name,
			c_s.id AS source_id,
			CASE temp.selected_option
			WHEN 'Pack_Size' THEN ROUND(temp.consumption_level, 1)
			WHEN
			'Unit_Size'
			THEN
			ROUND(temp.total_units / temp.consumption_level,
				1)
			ELSE 0
			END AS amc
			FROM
			commodity_source c_s,
			drug_store_totals ds,
			facility_stocks fs,
			commodities c
			LEFT JOIN
			facility_monthly_stock temp ON temp.commodity_id = c.id
			WHERE
			ds.district_id = '$district_id'
			AND fs.expiry_date >= NOW()
			AND fs.commodity_id= ds.commodity_id
			AND c.id = ds.commodity_id
			GROUP BY c.id
		");
		return $stocks;
	}
	//for the ZINC ORS analysis in Analysis controller
	public function get_stock_levels($commodity_id) {
		$year = date("Y");
		$stock_level = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			cts.county,
			d.district AS subcounty,
			fs.batch_no,
			fs.manufacture,
			fs.current_balance AS total_units,
			ROUND(((fs.current_balance) / c.total_commodity_units),
				0) AS total_packs,
		fs.facility_code,
		fs.expiry_date,
		c.total_commodity_units,
		(fs.current_balance * c.unit_cost) AS total_cost,
		c.commodity_name,
		f.facility_name
		FROM
		facility_stocks fs,
		commodities c,
		counties cts,
		districts d,
		facilities f
		WHERE
		fs.commodity_id = c.id
		AND fs.facility_code = f.facility_code
		AND f.district = d.id
		AND d.county = cts.id
		AND fs.commodity_id = $commodity_id
		AND YEAR(expiry_date) >= $year
		AND fs.current_balance > 0");
		return $stock_level;
	}
	//for getting the stock outs for email
	public static function get_stock_outs_for_email($facility_code) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			d.district,
			f_s.`facility_code`,
			f.facility_name,
			c.`id` AS commodity_id,
			c.unit_size,
			cts.county,
			cs.source_name,
			f_s.manufacture,
			c.`commodity_code`,
			c.`commodity_name`,
			max(date_modified) AS last_day,
			c.unit_cost,
			sum(current_balance) as current_balance,
			(sum(current_balance) / c.total_commodity_units) as current_balance_packs
			FROM
			facilities f,
			commodities c,
			counties cts,
			districts d,
			facility_stocks f_s,
			commodity_source cs
			WHERE
			f.facility_code = f_s.facility_code
			AND cs.id = c.commodity_source_id
			and f.facility_code = $facility_code
			AND f_s.commodity_id = c.id
			AND f.district = d.id
			and d.county = cts.id
			AND f_s.status = 1
			GROUP BY c.id
			having current_balance <= 10
			order by c.commodity_name asc");
		return $stocks;
	}
	public static function get_facility_stock_amc($facility_code) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			c.id AS commodity_id,
			fs.id AS facility_stock_id,
			fs.expiry_date,
			c.commodity_name,
			c.commodity_code,
			c.unit_size,
			ROUND(SUM(fs.current_balance), 1) AS commodity_balance,
			ROUND((SUM(fs.current_balance) / c.total_commodity_units),
				1) AS pack_balance,
		c.total_commodity_units,
		fs.manufacture,
		c_s.source_name,
		fs.batch_no,
		c_s.id AS source_id,
		CASE temp.selected_option
		WHEN 'Pack_Size' THEN ROUND(temp.consumption_level, 1)
		WHEN
		'Unit_Size'
		THEN
		ROUND(temp.total_units / temp.consumption_level,
			1)
		ELSE 0
		END AS amc
		FROM
		commodity_source c_s,
		facility_stocks fs,
		commodities c
		LEFT JOIN
		facility_monthly_stock temp ON temp.commodity_id = c.id and temp.facility_code=$facility_code
		WHERE
		fs.facility_code = '$facility_code'
		AND fs.expiry_date >= NOW()
		AND c.id = fs.commodity_id
		AND fs.status = '1'
		AND c_s.id = fs.source_of_commodity
		GROUP BY c.id
		");
		return $stocks;
	}
	public static function get_facility_expired_stuff($facility_code) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			c.id as commodity_id,
			fs.id as facility_stock_id,
			fs.expiry_date,
			c.commodity_name,
			c.commodity_code,
			c.unit_size,
			c.unit_cost,
			c.total_commodity_units,
			fs.manufacture,
			fs.current_balance,
			c_s.source_name,
			fs.batch_no,
			c_s.id as source_id
			from
			facility_stocks fs,
			commodities c,
			commodity_source c_s
			where
			fs.facility_code = '$facility_code'
			and fs.expiry_date <= NOW()
			and c.id = fs.commodity_id
			and fs.status = '1' 
			");
		return $stocks;
	}
	public static function county_drug_store_act_expiries($county_id){
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			    cds.id,
			    cds.commodity_id AS commodity_id,
			    c.commodity_name,
			    c.unit_size,
			    c.unit_cost,
			    cds.county_id,
			    cds.district_id,
			    cds.s11_No,
			    cds.batch_no,
			    cds.expiry_date,
			    cds.balance_as_of,
			    cds.adjustmentpve,
			    cds.adjustmentnve,
			    cds.qty_issued AS current_balance,
			    cds.date_issued,
			    cds.issued_to,
			    cds.created_at,
			    cds.issued_by,
			    cds.status
			FROM
			    county_drug_store_issues cds,
			    commodities c
			WHERE
			    cds.expiry_date <= NOW()
			        AND cds.county_id = '$county_id'
			        AND cds.qty_issued > 0
			        AND c.id = cds.commodity_id
			");
		return $stocks;
	}
	public static function county_drug_store_pte_expiries($county_id){
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			    cds.id,
			    cds.commodity_id AS commodity_id,
			    c.commodity_name,
			    c.unit_size,
			    c.unit_cost,
			    cds.county_id,
			    cds.district_id,
			    cds.s11_No,
			    cds.batch_no,
			    cds.expiry_date,
			    cds.balance_as_of,
			    cds.adjustmentpve,
			    cds.adjustmentnve,
			    cds.qty_issued AS current_balance,
			    cds.date_issued,
			    cds.issued_to,
			    cds.created_at,
			    cds.issued_by,
			    cds.status
			FROM
			    county_drug_store_issues cds,
			    commodities c
			WHERE
			    cds.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
			        AND cds.county_id = '1'
			        AND qty_issued > 0
			        AND c.id = cds.commodity_id
			");
		return $stocks;
	}
	public static function drug_store_commodity_expiries($district_id) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT ds.id,ds.commodity_id as commodity_id,c.commodity_name,c.unit_size,c.unit_cost,ds.facility_code,ds.district_id
			,ds.s11_No,ds.batch_no,
			ds.expiry_date,ds.balance_as_of,ds.adjustmentpve,
			ds.adjustmentnve,ds.qty_issued AS current_balance,ds.date_issued,
			ds.issued_to,ds.created_at,ds.issued_by,
			ds.status
			from drug_store_issues ds,commodities c
			where ds.expiry_date  <= NOW() AND ds.district_id = '$district_id' 
			and ds.qty_issued>0 and c.id = ds.commodity_id
			");
		return $stocks;
	}
	public static function drug_store_potential_expiries($district_id) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
			    ds.id,
			    ds.commodity_id AS commodity_id,
			    c.commodity_name,
			    c.unit_size,
			    c.unit_cost,
			    ds.facility_code,
			    ds.district_id,
			    ds.s11_No,
			    ds.batch_no,
			    ds.expiry_date,
			    ds.balance_as_of,
			    ds.adjustmentpve,
			    ds.adjustmentnve,
			    ds.qty_issued AS current_balance,
			    ds.date_issued,
			    ds.issued_to,
			    ds.created_at,
			    ds.issued_by,
			    ds.status
			FROM
			    drug_store_issues ds,
			    commodities c
			WHERE
			    ds.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
			        AND ds.district_id = '$district_id'
			        AND qty_issued > 0
			        AND c.id = ds.commodity_id
			");
		return $stocks;
	}
	public static function get_items_that_have_stock_out_in_facility($facility_code = null, $district_id = null, $county_id = null) {
		$where_clause = ((isset($facility_code)) && $facility_code != '') ? "f.facility_code=$facility_code " : ((isset($district_id) && $district_id != '') ? "d.id=$district_id " : "d.county=$county_id ");
		$group_by = isset($facility_code) ? " order by c.commodity_name asc" : (isset($district_id) ? " order by f.facility_name asc" : " order by d.district asc");
		
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			d.district,
			f_s.`facility_code`,
			f.facility_name,
			c.`id` AS commodity_id,
			c.unit_size,
			cs.source_name,
			f_s.manufacture,
			c.`commodity_code`,
			c.`commodity_name`,
			max(date_modified) AS last_day,
			sum(current_balance) as current_balance
			FROM
			facilities f,
			commodities c,
			districts d,
			facility_stocks f_s,
			commodity_source cs
			WHERE
			f.facility_code = f_s.facility_code
			AND cs.id = c.commodity_source_id
			and $where_clause
			AND f_s.commodity_id = c.id
			AND f.district = d.id
			AND f_s.status = 1
			GROUP BY c.id
			having current_balance = 0
			$group_by");
		return $stocks;
	}
	public static function get_stocked_out_commodities_for_report($facility_code = null, $district_id = null, $county_id = null) {
		$where_clause = isset($facility_code) ? "f.facility_code=$facility_code " : (isset($district_id) ? "d.id=$district_id " : "d.county=$county_id ");
		$group_by = isset($facility_code) ? " order by c.commodity_name asc" : (isset($district_id) ? " order by f.facility_name asc" : " order by d.district asc");
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			d.district,
			f_s.`facility_code`,
			f.facility_name,
			c.`id` AS commodity_id,
			cnts.county,
			c.unit_size,
			cs.source_name,
			f_s.manufacture,
			c.`commodity_code`,
			c.`commodity_name`,
			sum(f_s.current_balance/c.total_commodity_units) as current_balance_packs,
			sum(f_s.current_balance) as current_balance_units,
			ROUND(temp.total_units / temp.consumption_level,1)AS amc
			FROM
			facilities f,
			districts d,
			counties cnts,
			counties c_s,
			facility_stocks f_s,
			commodity_source cs,
			commodities c
			LEFT JOIN
			facility_monthly_stock temp ON temp.commodity_id = c.id
			and temp.facility_code = 11840
			WHERE
			f.facility_code = f_s.facility_code
			AND cs.id = c.commodity_source_id
			and $where_clause
			AND f_s.commodity_id = c.id
			AND f.district = d.id
			AND f_s.status = 1
			GROUP BY c.id
			having amc <=3
			$group_by");
		return $stocks;
	}
	public static function potential_expiries($facility_code) {
		$query = Doctrine_Query::create() -> select("*") -> from("Facility_stocks") -> where("expiry_date 
			BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH) AND facility_code='$facility_code' AND YEAR(expiry_date) = YEAR(NOW()) AND current_balance>0 AND status IN (1,2)");
		$stocks = $query -> execute();
		return $stocks;
	}
	public static function potential_expiries_seth($facility_code) {
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll("
			SELECT * FROM facility_stocks WHERE expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH) AND facility_code='$facility_code' AND current_balance>0 AND status IN (1,2)
			");
		return $query;
	}
	public static function potential_expiries_email($facility_code = null) {
		$query = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select 
			c.county,
			d1.district as subcounty,
			temp.commodity_name,
			f.facility_code,
			f.facility_name,
			temp.manufacture,
			sum(temp.total) as total_ksh,
			temp.unit_cost,
			temp.expiry_date,
			temp.unit_size,
			temp.units,
			temp.source_name,
			date(temp.date_added) as date_added,
			temp.packs
			from
			districts d1,
			counties c,
			facilities f
			left join
			(select 
				ROUND(SUM(f_s.current_balance / d.total_commodity_units) * d.unit_cost, 1) AS total,
				ROUND(SUM(f_s.current_balance / d.total_commodity_units), 1) as packs,
				SUM(f_s.current_balance) as units,
				f_s.facility_code,
				d.id,
				d.commodity_name,
				f_s.manufacture,
				cs.source_name,
				f_s.date_added,
				f_s.expiry_date,
				d.unit_size,
				d.unit_cost
				from
				facility_stocks f_s, commodities d, commodity_source cs
				where
				f_s.expiry_date between DATE_ADD(CURDATE(), INTERVAL 1 day) and DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
				AND cs.id = d.commodity_source_id
				and d.id = f_s.commodity_id
				and year(f_s.expiry_date) != 1970
				AND f_s.status = (1 or 2)
				GROUP BY d.id , f_s.facility_code
				having total > 1) temp ON temp.facility_code = f.facility_code
		where
		f.district = d1.id and c.id = d1.county
		AND temp.total > 0
		AND f.facility_code = '$facility_code'
		group by temp.id , f.facility_code
		order by temp.commodity_name asc , temp.total asc , temp.expiry_date desc");
		return $query;
	}
	//Used for the SMS notificatin
	//Gets the total number of potential expiries in the facility
	public static function get_potential_expiries_sms() {
		$expiries = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT fs.facility_code, f.facility_name, cs.source_name, COUNT(DISTINCT(fs.commodity_id)) as total
			FROM facility_stocks fs, facilities f, commodity_source cs, commodities c
			WHERE expiry_date  BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH) 
			AND fs.facility_code = f.facility_code
			AND cs.id = fs.source_of_commodity
			and current_balance>0
			GROUP BY facility_code");
		return $expiries;
	}
	public static function get_potential_expiries_weekly_email($district_id) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT 
				    *
			FROM
			facility_stocks fs,
			facilities f
			where
			expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
			AND fs.facility_code = f.facility_code
			AND f.district = '$district_id'
			and current_balance > 0 ");
		return $stocks;
	}
	public static function get_stock_outs_sms($facility_code) {
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$stock_outs = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select 
			fs.date_added, 
			fs.commodity_id,
			fs.facility_code,
			f.facility_name,
			c.commodity_name,
			COUNT(DISTINCT (fs.commodity_id)) as total
			FROM
			facility_stocks fs,
			facilities f,
			commodity_source cs,
			commodities c
			where
			fs.current_balance = 0 
			and fs.status = 1
			and fs.initial_quantity > 0
			AND fs.facility_code = f.facility_code
			$and_data
			group by facility_code
			order by f.facility_name");
		return $stock_outs;
	}
	public static function specify_period_potential_expiry($facility_code, $interval) {
			// $query = Doctrine_Query::create() -> select("*") -> from("Facility_stocks") -> where("expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL $interval MONTH) 
			//  AND facility_code='$facility_code' AND YEAR(expiry_date) = YEAR(NOW()) AND current_balance>0");
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() ->fetchAll("
			SELECT 
			fs.facility_code,
			fs.current_balance,
			fs.initial_quantity,
			fs.date_modified,
			fs.batch_no,
			fs.expiry_date,
			fs.manufacture,
			c.id,
			c.commodity_code,
			c.commodity_name,
			c.unit_size,
			c.unit_cost,
			c.total_commodity_units,
			ROUND((fs.current_balance / c.total_commodity_units) * c.unit_cost, 1) AS total
			FROM
			facility_stocks fs,commodities c
			WHERE
			expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL $interval MONTH)
			AND fs.commodity_id = c.id 
			AND fs.facility_code = $facility_code
			AND fs.current_balance > 0
			AND YEAR(fs.expiry_date) = YEAR(NOW())
			AND fs.status IN (1 , 2)");
			// $stocks = $query -> execute();
		return $stocks;
	}
	public static function specify_period_potential_expiry_store($district_id, $interval) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			SELECT ds.id, ds.facility_code, ds.district_id, ds.commodity_id, ds.s11_No, ds.batch_no, ds.expiry_date, ds.balance_as_of , ds.adjustmentpve, ds.adjustmentnve, ds.qty_issued, ds.date_issued, ds.issued_to, ds.created_at, ds.issued_by, ds.status 
			FROM drug_store_issues ds,drug_store_totals dst where expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL $interval MONTH)
			AND ds.district_id= $district_id AND ds.district_id = dst.district_id AND dst.total_balance > 0
			");
		return $stocks;
	}
	public static function specify_period_potential_expiry_store_titus($district_id, $interval) {
			// echo "SELECT  d.commodity_name,d.commodity_code, dsi.district_id, dsi.commodity_id,
	  //   				dsi.batch_no, SUM(dsi.balance_as_of) AS current_bal, dsi.expiry_date, DATEDIFF(dsi.expiry_date,CURDATE()) as days_to_expire, d.unit_size,
	  //   				d.unit_cost,sum(dsi.balance_as_of * d.unit_cost) as total_cost FROM drug_store_issues dsi JOIN  commodities d ON d.id = dsi.commodity_id
			// 			WHERE dsi.district_id = '$district_id' AND dsi.balance_as_of > 0 AND dsi.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL $interval MONTH) 
			// 			GROUP BY district_id , commodity_idND ds.district_id= $district_id AND ds.district_id = dst.district_id AND dst.total_balance > 0";die;
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT  d.commodity_name,d.commodity_code, dsi.district_id, dsi.commodity_id,
			dsi.batch_no, SUM(dsi.balance_as_of) AS current_bal, dsi.expiry_date, DATEDIFF(dsi.expiry_date,CURDATE()) as days_to_expire, d.unit_size,
			d.unit_cost,sum(dsi.balance_as_of * d.unit_cost) as total_cost FROM drug_store_issues dsi JOIN  commodities d ON d.id = dsi.commodity_id
			WHERE dsi.district_id = '$district_id' AND dsi.balance_as_of > 0 AND dsi.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL $interval MONTH) 
			GROUP BY district_id , commodity_id");
		return $stocks;
	}
	public static function All_expiries($facility_code, $checker = null) {
		$year = date(Y);
		$and = isset($checker) ? " and (f_s.status =1 or f_s.status =2)" : " and f_s.status =1";
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select    
			f_s.facility_code,
			f_s.commodity_id,
			f_s.batch_no,
			f_s.manufacture,
			f_s.status,
			f_s.expiry_date,
			c.commodity_name,
			c.unit_size,
			c.total_commodity_units,
			c.unit_cost,
			f_s.current_balance,
			c.commodity_code from  facility_stocks f_s 
			LEFT JOIN  commodities c ON c.id=f_s.commodity_id where facility_code=$facility_code 
			and f_s.current_balance>0 and expiry_date <= NOW() $and AND year(expiry_date)=$year");
		return $stocks;
	}
	public static function All_expiries_email($facility_code, $checker = null) {
		$year = date("Y");
			//$and=isset($checker)? " and (f_s.status =1 or f_s.status =2)" : " and f_s.status =1";
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select 
			d1.district as subcounty,
			cts.county,
			f_s.facility_code,
			f_s.commodity_id,
			f_s.batch_no,
			ROUND((f_s.current_balance / c.total_commodity_units),1) as packs,
			ROUND((f_s.current_balance / c.total_commodity_units) * c.unit_cost,1) AS total,
			f_s.manufacture,
			f_s.status,
			f_s.expiry_date,
			c.commodity_name,
			c.unit_size,
			c.total_commodity_units,
			c.unit_cost,
			f.facility_name,
			f_s.current_balance as units,
			date(f_s.date_added) as date_added,
			c.commodity_code,
			cs.source_name
			from
			districts d1,
			counties cts,
			facilities f,
			commodity_source cs,
			facility_stocks f_s
			LEFT JOIN
			commodities c ON c.id = f_s.commodity_id
			where
			f_s.facility_code = $facility_code
			AND cs.id = c.commodity_source_id
			AND f.facility_code = f_s.facility_code
			AND f.district = d1.id 
			AND d1.county = cts.id
			and f_s.current_balance > 0
			and expiry_date <= NOW()
			and (f_s.status = 1 or f_s.status = 2)
			AND year(expiry_date) = $year");
		return $stocks;
	}
	public static function expiries_report($facility_code) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select 
			f_s.facility_code,
			f_s.commodity_id,
			f_s.batch_no,
			f_s.manufacture,
			f_s.status,
			c.commodity_name,
			c.commodity_code,
			DATE_FORMAT(f_s.expiry_date, '%d %b %y') as expiry_date,
			DATE_FORMAT(f_s.expiry_date, '%M %Y') as expiry_month
			from
			facility_stocks f_s
			LEFT JOIN
			commodities c ON c.id = f_s.commodity_id
			where
			facility_code = $facility_code
			and f_s.current_balance > 0
			and year(expiry_date) between year(NOW()) and year(DATE_ADD(CURDATE(),
				INTERVAL 2 year)) 
		order by f_s.expiry_date asc");
		return $stocks;
	}
	/////getting cost of exipries county
	public static function get_county_cost_of_exipries_new($facility_code = null, $district_id = null, $county_id, $year = null, $month = null, $option = null, $data_for = null) {
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as name";
		break;
		case 'units' :
		$computation = " ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		case 'packs' :
		$computation = " ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total";
		break;
		default :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as name";
		break;
		endswitch;
		$selection_for_a_month = isset($facility_code) && isset($district_id) ? " d.commodity_name as name," : (isset($district_id) && !isset($facility_code) ? " f.facility_name as name," : " di.district as name,");
		$select_option = ($data_for == 'all') ? "date_format( fs.expiry_date, '%b' ) as cal_month," : $selection_for_a_month;
		$and_data = ($district_id > 0) ? " AND di.id = '$district_id'" : null;
		$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
			//$and_data .=($month>0) ? " AND date_format( fs.expiry_date, '%m')=$month"  : null;
		$and_data .= ($year > 0) ? " AND DATE_FORMAT( fs.expiry_date,'%Y') =$year" : null;
		$group_by_a_month = isset($facility_code) && isset($district_id) ? " GROUP BY fs.commodity_id having total>0" : (isset($district_id) && !isset($facility_code) ? " GROUP BY f.facility_code having total>0" : " GROUP BY d.id having total>0");
		$group_by = ($data_for == 'all') ? "GROUP BY month(expiry_date) asc" : $group_by_a_month;
			//exit;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT $select_option  date_format(expiry_date,'%M') AS month, $computation  
			FROM facility_stocks fs, facilities f, commodities d, counties c, districts di
			WHERE fs.facility_code = f.facility_code
			AND fs.`expiry_date` <= NOW( )
			AND f.district =di.id
			AND di.county=c.id
			AND d.id = fs.commodity_id
			$and_data
			$group_by
			");
		return $inserttransaction;
	}
	//for the potential expiries
	public static function get_county_cost_of_potential_expiries_new($facility_code = null, $district_id = null, $county_id, $year = null, $month = null, $option = null, $data_for = null) {
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total_potential,d.commodity_name as name";
		break;
		case 'units' :
		$computation = " ifnull(CEIL(SUM(fs.current_balance)),0) AS total_potential";
		break;
		case 'packs' :
		$computation = " ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total_potential";
		break;
		default :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total_potential,d.commodity_name as name";
		break;
		endswitch;
		$selection_for_a_month = isset($facility_code) && isset($district_id) ? " d.commodity_name as name," : (isset($district_id) && !isset($facility_code) ? " f.facility_name as name," : " di.district as name,");
		$select_option = ($data_for == 'all') ? "date_format( fs.expiry_date, '%b' ) as cal_month," : $selection_for_a_month;
		$and_data = ($district_id > 0) ? " AND di.id = '$district_id'" : null;
		$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
			//$and_data .=($month>0) ? " AND date_format( fs.expiry_date, '%m')=$month"  : null;
		$and_data .= ($year > 0) ? " AND DATE_FORMAT( fs.expiry_date,'%Y') =$year" : null;
		$group_by_a_month = isset($facility_code) && isset($district_id) ? " GROUP BY fs.commodity_id having total>0" : (isset($district_id) && !isset($facility_code) ? " GROUP BY f.facility_code having total>0" : " GROUP BY d.id having total>0");
		$group_by = ($data_for == 'all') ? "GROUP BY month(expiry_date) asc" : $group_by_a_month;
			//exit;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT $select_option  date_format(expiry_date,'%M') AS month_potential, $computation  
			FROM facility_stocks fs, facilities f, commodities d, counties c, districts di
			WHERE fs.facility_code = f.facility_code
			AND fs.`expiry_date` >= NOW( )
			AND f.district =di.id
			AND di.county=c.id
			AND d.id = fs.commodity_id
			$and_data
			$group_by
			");
		return $inserttransaction;
	}
	//for the potential expiries
	public static function get_facility_cost_of_exipries_new($facility_code = null, $district_id = null, $county_id, $year = null, $month = null, $option = null, $data_for = null) {
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total";
		break;
		case 'units' :
		$computation = "ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		case 'packs' :
		$computation = "ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total";
		break;
		default :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total";
		break;
		endswitch;
		$selection_for_a_month = isset($facility_code) && isset($district_id) ? " d.commodity_name as name," : (isset($district_id) && !isset($facility_code) ? " f.facility_name as name," : " di.district as name,");
		$select_option = ($data_for == 'all') ? "date_format( fs.expiry_date, '%b' ) as cal_month," : $selection_for_a_month;
		$and_data = ($district_id > 0) ? " AND di.id = '$district_id'" : null;
		$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
		$and_data .= ($month > 0) ? " AND date_format( fs.expiry_date, '%m')=$month" : null;
		$and_data .= ($year > 0) ? " AND DATE_FORMAT( fs.expiry_date,'%Y') =$year" : null;
		$group_by_a_month = isset($facility_code) && isset($district_id) ? " GROUP BY fs.commodity_id having total>0" : (isset($district_id) && !isset($facility_code) ? " GROUP BY f.facility_code having total>0" : " GROUP BY d.id having total>0");
		$group_by = ($data_for == 'all') ? "GROUP BY month(expiry_date) asc" : $group_by_a_month;
		return "SELECT $select_option $computation
		FROM facility_stocks fs, facilities f, commodities d, counties c, districts di
		WHERE fs.facility_code = f.facility_code
		AND fs.`expiry_date` <= NOW( )
		AND f.district =di.id
		AND di.county=c.id
		AND d.id = fs.commodity_id
		$and_data
		$group_by";
			//exit;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT $select_option $computation
			FROM facility_stocks fs, facilities f, commodities d, counties c, districts di
			WHERE fs.facility_code = f.facility_code
			AND fs.`expiry_date` <= NOW( )
			AND f.district =di.id
			AND di.county=c.id
			AND d.id = fs.commodity_id
			$and_data
			$group_by
			");
		return $inserttransaction;
	}
	//for getting the stock levels for county and subcounty levels
	public static function get_stock_levels_county($facility_code = null, $sub_county_id = null, $county_id = null, $commodity_id = null, $option = null) {
			//the computation for the stock levels
			//ie whether units, packs or ksh
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ cms.total_commodity_units)))*cms.unit_cost ,0) AS total";
		break;
		case 'units' :
		$computation = "ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		case 'packs' :
		$computation = "ifnull(SUM(ROUND(fs.current_balance/cms.total_commodity_units)),0) AS total";
		break;
		default :
		$computation = "ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		endswitch;
			//set the parameters for the query blank at first
		$and_data = "";
		$and_data .= (isset($county_id) && ($county_id > 0)) ? "AND c.id = $county_id" : null;
		$and_data .= (isset($sub_county_id) && ($sub_county_id > 0)) ? "AND d.id = $sub_county_id" : null;
		$and_data .= (isset($commodity_id) && ($commodity_id > 0)) ? "AND cms.id = $commodity_id" : null;
			//for the group by statement
		$group_by = "GROUP BY cms.id";
		$group_by .= (isset($facility_code) && ($facility_code > 0)) ? ", $facility_code" : null;
		$group_by .= (isset($sub_county_id) && ($sub_county_id > 0)) ? ", $sub_county_id" : null;
		$group_by .= (isset($county_id) && ($county_id > 0)) ? "AND c.id = $county_id" : null;
		$stock_levels = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			d.district,
			cms.commodity_name,
			f.facility_name,
			f.facility_code,
			d.district AS name,
			$computation
			FROM
			facility_stocks fs,
			facilities f,
			commodities cms,
			districts d
			WHERE
			fs.facility_code = f.facility_code
			AND f.district = d.id
			AND cms.id = fs.commodity_id
			AND fs.expiry_date > NOW()
			AND fs.status = 1
			$and_data
			$group_by
			HAVING total > 0
			ORDER BY di.district ASC , f.facility_name ASC
			");
		return $stock_levels;
	}
	//stock levels for tracer items for county and sub county level interface
	public static function get_county_stock_level_tracer($county_id, $district_id = null,$facility_code = null, $option = null) {
			//computation required by the user
		switch ($option) :
		case 'ksh' :
		$computation = "((fs.current_balance/ c.total_commodity_units)*c.unit_cost) AS total";
		break;
		case 'units' :
		$computation = "fs.current_balance AS total";
		break;
		case 'packs' :
		$computation = "(fs.current_balance/c.total_commodity_units) AS total";
		break;
		default :
		$computation = "fs.current_balance AS total";
		break;
		endswitch;
			//extra columns for excl in case selected
		$columnar_data .= (isset($district_id)&&($district_id>0))? ", d.district ": null;
		$columnar_data .= (isset($facility_code)&&($facility_code>0))? ", f.facility_name ": null;
			//data filter level
		$and_data .= ($county_id > 0) ? " AND d.county='$county_id'" : null;
		$and_data .= (isset($district_id)&&($district_id>0))?" AND d.id = '$district_id'":null;
		$and_data .= (isset($facility_code)&&($facility_code>0))?" AND f.facility_code = '$facility_code'":null;
			//group by statements
		$group_by = (isset($district_id)&&($district_id>0))?", d.id":null;
		$group_by = (isset($facility_code)&&($facility_code>0))?", f.id":null;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() 
		->fetchAll("SELECT 
			c.commodity_name,
			c.id as commodity_id,
			$computation,
			fs.batch_no,
			fs.manufacture,
			fs.expiry_date,
			cts.county
			$columnar_data
			FROM
			facility_stocks fs,
			commodities c,
			districts d,
			facilities f,
			counties cts
			WHERE
			fs.commodity_id = c.id
			AND c.tracer_item = 1
			AND f.facility_code = fs.facility_code
			AND f.district = d.id
			AND d.county = cts.id
			$and_data
			GROUP BY c.id $group_by
			");
		return $inserttransaction;
	}
	public static function get_county_drug_stock_level_new($facility_code = null, $district_id = null, $county_id, $category_id = NULL, $commodity_id = NULL, $option = null, $graph_type = null, $division_id = NULL) {
		$selection_for_a_month = (isset($facility_code) && isset($district_id)) || (($category_id > 0)) ? " d.commodity_name as name," : (($district_id > 0) && !isset($facility_code) ? " f.facility_name as name," : ($graph_type == 'table_data') && ($commodity_id > 0) ? " di.district , f.facility_name, f.facility_code, " : " di.district as name,");
		$selection_for_a_month = (!isset($commodity_id) && !isset($category_id)) ? " di.district ,d.commodity_name, f.facility_name, f.facility_code,di.district as name," : $selection_for_a_month;
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total";
		break;
		case 'units' :
		$computation = "ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		case 'packs' :
		$computation = "ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total";
		break;
		default :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total";
		break;
		endswitch;
		$and_data .= (isset($category_id) && ($category_id > 0)) ? "AND d.commodity_sub_category_id = '$category_id'" : null;
		$and_data .= (isset($commodity_id) && ($commodity_id > 0)) ? "AND fs.commodity_id = d.id AND d.id = '$commodity_id'" : null;
		$and_data .= (isset($division_id) && ($division_id > 0)) ? "AND d.commodity_division = '$division_id' " : null;
		$and_data .= (isset($district_id) && ($district_id > 0)) ? "AND di.id = '$district_id'" : null;
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND di.county='$county_id'" : null;
		$and_data .= (!isset($commodity_id) && !isset($category_id)) ? 'and d.id=fs.commodity_id' : null;
		$group_by_a_month = ((isset($facility_code) && isset($district_id)) || (isset($category_id) && ($category_id > 0))) ? " GROUP BY fs.commodity_id having total>0" : (isset($district_id) && !isset($facility_code) ? " GROUP BY f.facility_code having total>0" : ($graph_type == 'table_data') && ($commodity_id > 0) ? " GROUP BY d.id, f.facility_code having total>0 order by di.district asc, f.facility_name asc" : " GROUP BY d.id having total > 0");
		$group_by_a_month = (!isset($commodity_id) && !isset($category_id)) ? " GROUP BY d.id, f.facility_code having total>0 order by di.district asc, f.facility_name asc " : $group_by_a_month;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT  $selection_for_a_month $computation
			FROM facility_stocks fs, facilities f, commodities d,  districts di
			WHERE fs.facility_code = f.facility_code
			AND f.district =di.id
			and fs.expiry_date>NOW()
			AND fs.status=1
			$and_data
			$group_by_a_month
			");
		return $inserttransaction;
	}
	//For the County Comparison
	public static function get_county_comparison_data($facility_code = null, $district_id = null, $county_id, $category_id = NULL, $commodity_id = NULL, $option = null, $graph_type = null, $division_id = NULL) {
		$selection_for_a_month = (isset($facility_code) && isset($district_id)) ? " d.commodity_name as name," : (($district_id > 0) && !isset($facility_code) ? " f.facility_name as name," : ($graph_type == 'table_data') && ($commodity_id > 0) ? " di.district , f.facility_name, f.facility_code, " : " di.district as name,");
		$selection_for_a_month = (!isset($commodity_id)) ? " di.district ,d.commodity_name, f.facility_name, f.facility_code,di.district as name," : $selection_for_a_month;
		switch ($option) :
		case 'units' :
		$computation = "ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		case 'packs' :
		$computation = "ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total";
		break;
		default :
		$computation = "ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		endswitch;
			//$and_data .=(isset($category_id)&& ($category_id>0)) ?"AND d.commodity_sub_category_id = '$category_id'" : null;
		$and_data .= (isset($commodity_id) && ($commodity_id > 0)) ? "AND fs.commodity_id = d.id AND d.id = '$commodity_id'" : null;
			//$and_data .=(isset($division_id)&& ($division_id>0)) ?"AND d.commodity_division = '$division_id' " :null;
		$and_data .= (isset($district_id) && ($district_id > 0)) ? "AND di.id = '$district_id'" : null;
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND di.county='$county_id'" : null;
		$and_data .= (!isset($commodity_id)) ? 'and d.id=fs.commodity_id' : null;
		$group_by_a_month = ((isset($facility_code) && isset($district_id))) ? " GROUP BY fs.commodity_id having total>0" : (isset($district_id) && !isset($facility_code) ? " GROUP BY f.facility_code having total>0" : ($graph_type == 'table_data') && ($commodity_id > 0) ? " GROUP BY d.id, f.facility_code having total>0 order by di.district asc, f.facility_name asc" : " GROUP BY d.id having total > 0");
		$group_by_a_month = (!isset($commodity_id)) ? " GROUP BY di.id having total>0 order by di.district asc " : $group_by_a_month;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT  $selection_for_a_month $computation,
			ifnull(round(avg(IFNULL(fs.current_balance, 0) / IFNULL(f_m_s.total_units, 0)),1),0) as total_mos
			FROM facility_stocks fs, facilities f, commodities d,  districts di,
			facility_monthly_stock f_m_s
			WHERE fs.facility_code = f.facility_code
			AND f_m_s.`commodity_id` = d.id
			AND f.district =di.id
			and fs.expiry_date>NOW()
			AND fs.status=1
			$and_data
			GROUP BY di.id having total>0 order by di.district asc
			");
		return $inserttransaction;
	}
	public static function get_county_consumption_level_new($facility_code, $district_id, $county_id, $category_id, $commodity_id, $option, $from, $to, $graph_type = null, $tracer = null) {
		// echo "$to";die;
		$selection_for_a_month = ((!isset($facility_code) || $facility_code == "ALL") && ($district_id) > 0) || $category_id > 0 ? " f.facility_name as name," : (($commodity_id == "ALL") && isset($facility_code) ? " d.commodity_name as name," : ((isset($county_id) && $district_id == "ALL") ? " di.district as name," : ($graph_type == 'table_data' && $commodity_id > 0) ? " di.district , f.facility_name, f.facility_code, " : 1));
		if ($selection_for_a_month == 1) {
			$seconds_diff = $to - $from;
			$date_diff = floor($seconds_diff / 3600 / 24);
			$selection_for_a_month = $date_diff <= 30 ? "DATE_FORMAT(fs.date_issued,'%d %b %y') as name," : "DATE_FORMAT(fs.date_issued,'%b %y') as name ,";
		}
		$to = date('Y-m-d', $to);
		$from = date('Y-m-d', $from);
		switch ($option) :

			case 'ksh' :
				$computation = "ifnull((SUM(ROUND(fs.qty_issued/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as commodity";
				// $computation = "ifnull((SUM(ROUND(ABS(fs.qty_issued)/ d.total_commodity_units)))*d.unit_cost ,0)-ifnull((SUM(ROUND(ABS(fs.adjustmentnve)/ d.total_commodity_units)))*d.unit_cost ,0)+ifnull((SUM(ROUND(ABS(fs.adjustmentpve)/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as commodity";
				break;
			case 'units' :
				$computation = "ifnull(CEIL(SUM(fs.qty_issued)),0) AS total,d.commodity_name as commodity";
				// $computation = "ifnull(CEIL(SUM(ABS(fs.qty_issued))),0)-ifnull(CEIL(SUM(ABS(fs.adjustmentnve))),0)+ifnull(CEIL(SUM(ABS(fs.adjustmentpve))),0) AS total,d.commodity_name as commodity";
				break;
			case 'packs' :
				$computation = "ifnull(SUM(ROUND(fs.qty_issued/d.total_commodity_units)),0) AS total,d.commodity_name as commodity";
				// $computation = "ifnull(SUM(ROUND(ABS(fs.qty_issued)/d.total_commodity_units)),0)-ifnull(SUM(ROUND(ABS(fs.adjustmentnve)/d.total_commodity_units)),0)+ifnull(SUM(ROUND(ABS(fs.adjustmentpve)/d.total_commodity_units)),0) AS total,d.commodity_name as commodity";
				break;
			case 'mos' :
				$r = facility_stocks_temp::get_months_of_stock($district_id, $county_id, $facility_code);
				return $r;
				exit ;
				break;
			default :
				// $computation = "ifnull(SUM(ROUND(ABS(fs.qty_issued)/d.total_commodity_units)),0)-ifnull(SUM(ROUND(ABS(fs.adjustmentnve)/d.total_commodity_units)),0)+ifnull(SUM(ROUND(ABS(fs.adjustmentpve)/d.total_commodity_units)),0) AS total,d.commodity_name as commodity";
			
				// $computation = "ifnull((SUM(ROUND(ABS(fs.qty_issued)/ d.total_commodity_units)))*d.unit_cost ,0)-ifnull((SUM(ROUND(ABS(fs.adjustmentnve)/ d.total_commodity_units)))*d.unit_cost ,0)+ifnull((SUM(ROUND(ABS(fs.adjustmentpve)/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as commodity";
				$computation = "ifnull((SUM(ROUND(fs.qty_issued/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as commodity";
				break;

		// case 'ksh' :
		// $computation = "ifnull((SUM(ROUND(fs.qty_issued/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as commodity";
		// break;
		// case 'units' :
		// $computation = "ifnull(CEIL(SUM(fs.qty_issued)),0) AS total,d.commodity_name as commodity";
		// break;
		// case 'packs' :
		// $computation = "ifnull(SUM(ROUND(fs.qty_issued/d.total_commodity_units)),0) AS total,d.commodity_name as commodity";
		// break;
		// case 'mos' :
		// $r = facility_stocks_temp::get_months_of_stock($district_id, $county_id, $facility_code);
		// return $r;
		// exit ;
		// break;
		// default :
		// $computation = "ifnull((SUM(ROUND(fs.qty_issued/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as commodity";
		// break;

		endswitch;
		$and_data .= (isset($category_id) && ($category_id > 0)) ? "AND d.commodity_sub_category_id = '$category_id'" : null;
		$and_data = isset($from) && isset($to) ? "AND fs.date_issued between '$from' and '$to'" : null;
		$and_data .= (isset($commodity_id) && ($commodity_id > 0)) ? "AND d.id = '$commodity_id'" : null;
		$and_data .= (isset($district_id) && ($district_id > 0)) ? "AND di.id = '$district_id'" : null;
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND di.county='$county_id'" : null;
		$and = (isset($tracer) && ($tracer > 0)) ? " AND d.tracer_item = 1" : null;
		$group_by_a_month = (isset($facility_code) && isset($district_id)) || isset($category_id) ? " GROUP BY fs.commodity_id having total>0" : (($district_id > 0 && !isset($facility_code)) ? " GROUP BY f.facility_code having total>0" : " GROUP BY d.id having total>0");
		$group_by_a_month = (($facility_code == "ALL") || !isset($facility_code)) && $district_id > 0 ? " GROUP BY f.facility_code having total>0" : ($commodity_id == "ALL") && isset($facility_code) ? " GROUP BY fs.commodity_id having total>0" : (isset($county_id) && $district_id == "ALL") ? " GROUP BY d.id having total>0" : (($graph_type == 'table_data') && ($commodity_id > 0) ? " GROUP BY d.id, f.facility_code having total>0 order by di.district asc, f.facility_name asc" : 1);
		if ($group_by_a_month == 1) {
			$group_by_a_month = $date_diff <= 30 ? "GROUP BY DATE_FORMAT(fs.date_issued,'%d %b %y'),fs.commodity_id" : " GROUP BY DATE_FORMAT(fs.date_issued,'%b %y'),fs.commodity_id";
		} elseif ($tracer = 1) {
			$group_by_a_month = $date_diff <= 30 ? "GROUP BY commodity" : $group_by_a_month;
		}
		$group_by_a_month = (isset($tracer) && ($group_by_a_month)) ? " GROUP BY commodity" : $group_by_a_month;
		// echo "SELECT  $selection_for_a_month $computation
	 //    FROM facility_issues fs, facilities f, commodities d, districts di
	 //    WHERE fs.facility_code = f.facility_code
	 //    AND f.district = di.id
	 //    AND fs.qty_issued >=0
	 //    $and 
	 //    AND d.id = fs.commodity_id
	 //    $and_data
	 //    $group_by_a_month";die;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT  $selection_for_a_month $computation
	    FROM facility_issues fs, facilities f, commodities d, districts di
	    WHERE fs.facility_code = f.facility_code
	    AND f.district = di.id
	    AND fs.qty_issued >=0
	    $and 
	    AND d.id = fs.commodity_id
	    $and_data
	    $group_by_a_month having total>0
	     ");
			return $inserttransaction;
	}
	public static function get_tracer_items_report_new($facility_code, $district_id, $county_id, $category_id, $commodity_id, $option, $from, $to, $graph_type = null, $tracer = null ,$option = NULL) {
		$selection_for_a_month = ((!isset($facility_code) || $facility_code == "ALL") && ($district_id) > 0) || $category_id > 0 ? " f.facility_name as name," : (($commodity_id == "ALL") && isset($facility_code) ? " d.commodity_name as name," : ((isset($county_id) && $district_id == "ALL") ? " di.district as name," : ($graph_type == 'table_data' && $commodity_id > 0) ? " di.district , f.facility_name, f.facility_code, " : 1));
			// echo $option;exit;
		if ($selection_for_a_month == 1) {
			$seconds_diff = $to - $from;
			$date_diff = floor($seconds_diff / 3600 / 24);
			$selection_for_a_month = $date_diff <= 30 ? "DATE_FORMAT(fs.date_issued,'%d %b %y') as date," : "DATE_FORMAT(fs.date_issued,'%b %y') as date ,";
		}
		$to = date('Y-m-d', $to);
		$from = date('Y-m-d', $from);
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.qty_issued/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.id as commodity_id,d.commodity_code,d.unit_size,d.total_commodity_units,d.commodity_name as commodity";
		break;
		case 'units' :
		$computation = "ifnull(CEIL(SUM(fs.qty_issued)),0) AS total,d.id as commodity_id,d.commodity_code,d.unit_size,d.total_commodity_units,d.commodity_name as commodity";
		break;
		case 'packs' :
		$computation = "ifnull(SUM(ROUND(fs.qty_issued/d.total_commodity_units)),0) AS total,d.id as commodity_id,d.commodity_code,d.unit_size,d.total_commodity_units,d.commodity_name as commodity";
		break;
		case 'mos' :
		$r = facility_stocks_temp::get_months_of_stock($district_id, $county_id, $facility_code);
		return $r;
		exit ;
		break;
		default :
		$computation = "ifnull((SUM(ROUND(fs.qty_issued/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.id as commodity_id,d.commodity_code,d.unit_size,d.total_commodity_units,d.commodity_name as commodity";
		break;
		endswitch;
		$and_data .= (isset($category_id) && ($category_id > 0)) ? "AND d.commodity_sub_category_id = '$category_id'" : null;
		$and_data = isset($from) && isset($to) ? "AND fs.date_issued between '$from' and '$to'" : null;
		$and_data .= (isset($commodity_id) && ($commodity_id > 0)) ? "AND d.id = '$commodity_id'" : null;
		$and_data .= (isset($district_id) && ($district_id > 0)) ? "AND di.id = '$district_id'" : null;
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND di.county='$county_id'" : null;
		$and = (isset($tracer) && ($tracer > 0)) ? " AND d.tracer_item = 1" : null;
		$group_by_a_month = (isset($facility_code) && isset($district_id)) || isset($category_id) ? " GROUP BY fs.commodity_id having total>0" : (($district_id > 0 && !isset($facility_code)) ? " GROUP BY f.facility_code having total>0" : " GROUP BY d.id having total>0");
		$group_by_a_month = (($facility_code == "ALL") || !isset($facility_code)) && $district_id > 0 ? " GROUP BY f.facility_code having total>0" : ($commodity_id == "ALL") && isset($facility_code) ? " GROUP BY fs.commodity_id having total>0" : (isset($county_id) && $district_id == "ALL") ? " GROUP BY d.id having total>0" : (($graph_type == 'table_data') && ($commodity_id > 0) ? " GROUP BY d.id, f.facility_code having total>0 order by di.district asc, f.facility_name asc" : 1);
		if ($group_by_a_month == 1) {
			$group_by_a_month = $date_diff <= 30 ? "GROUP BY DATE_FORMAT(fs.date_issued,'%d %b %y')" : " GROUP BY DATE_FORMAT(fs.date_issued,'%b %y')";
		} elseif ($tracer = 1) {
			$group_by_a_month = $date_diff <= 30 ? "GROUP BY commodity" : $group_by_a_month;
		}
		$group_by_a_month = (isset($tracer) && ($group_by_a_month)) ? " GROUP BY commodity" : $group_by_a_month;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT  $selection_for_a_month $computation
			FROM facility_issues fs, facilities f, commodities d, districts di
			WHERE fs.facility_code = f.facility_code
			AND f.district = di.id
			AND fs.qty_issued >0
			$and 
			AND d.id = fs.commodity_id
			$and_data
			$group_by_a_month
			");
			// echo "SELECT $computation";exit;
		return $inserttransaction;
	}
	public static function get_sub_county_cost_of_exipries($facility_code = null, $district_id = null, $county_id, $year = null, $month = null, $option = null, $data_for = null) {
		switch ($option) :
		case 'ksh' :
		$computation = "ifnull((SUM(ROUND(fs.current_balance/ d.total_commodity_units)))*d.unit_cost ,0) AS total,d.commodity_name as name";
		break;
		case 'units' :
		$computation = " ifnull(CEIL(SUM(fs.current_balance)),0) AS total";
		break;
		case 'packs' :
		$computation = " ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total";
		break;
		default :
		$computation = "ifnull(SUM(ROUND(fs.current_balance/d.total_commodity_units)),0) AS total";
		break;
		endswitch;
		$selection_for_a_month = isset($facility_code) && isset($district_id) ? " d.commodity_name as name," : (isset($district_id) && !isset($facility_code) ? " f.facility_name as name," : " di.district as name,");
		$select_option = ($data_for == 'all') ? "date_format( fs.expiry_date, '%b' ) as cal_month," : $selection_for_a_month;
		$and_data = ($district_id > 0) ? " AND di.id = '$district_id'" : null;
		$and_data .= ($facility_code > 0) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND c.id='$county_id'" : null;
			//$and_data .=($month>0) ? " AND date_format( fs.expiry_date, '%m')=$month"  : null;
		$and_data .= ($year > 0) ? " AND DATE_FORMAT( fs.expiry_date,'%Y') =$year" : null;
		$group_by_a_month = isset($facility_code) && isset($district_id) ? " GROUP BY fs.commodity_id having total>0" : (isset($district_id) && !isset($facility_code) ? " GROUP BY f.facility_code having total>0" : " GROUP BY d.id having total>0");
		$group_by = ($data_for == 'all') ? "GROUP BY month(expiry_date) asc" : $group_by_a_month;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT d.commodity_name,  f.facility_name, di.district, $select_option  date_format(expiry_date,'%M') AS month, $computation  
			FROM facility_stocks fs, facilities f, commodities d, counties c, districts di
			WHERE fs.facility_code = f.facility_code
			AND fs.`expiry_date` <= NOW( )
			AND f.district =di.id
			AND di.county=c.id
			AND d.id = fs.commodity_id
			$and_data
			$group_by
			");
		return $inserttransaction;
	}
	public static function get_county_expiries($county_id, $year, $district_id = null, $facility_code = null) {
		$and_data = (isset($district_id) && ($district_id > 0)) ? "AND d1.id = '$district_id'" : null;
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND d1.county =$county_id" : null;
		$query = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select  d1.id as district_id, d1.district, f.facility_code, f.facility_name, sum(temp.total) as total
			from districts d1, facilities f left join
			(
				select  ROUND( (
					SUM( f_s.current_balance ) / d.total_commodity_units ) * d.unit_cost, 1
		) AS total, f_s.facility_code from facility_stocks f_s, commodities d
		where f_s.expiry_date < NOW( ) 
		and d.id=f_s.commodity_id
		and year(f_s.expiry_date)=$year
		AND f_s.status =(1 or 2)
		GROUP BY f_s.commodity_id,f_s.facility_code having total >1
		) temp
		on temp.facility_code = f.facility_code
		where  f.district = d1.id
		$and_data
		group by f.facility_code");
		return $query;
	}
	public static function get_potential_expiry_summary($county_id, $interval, $district_id = null, $facility_code = null) {
		$and_data = (isset($district_id) && ($district_id > 0)) ? "AND d1.id = '$district_id'" : null;
		$and_data .= (isset($facility_code) && ($facility_code > 0)) ? " AND f.facility_code = '$facility_code'" : null;
		$and_data .= ($county_id > 0) ? " AND d1.county =$county_id" : null;
		$query = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("
			select  d1.id as district_id, d1.district, f.facility_code, f.facility_name, sum(temp.total) as total
			from districts d1, facilities f left join
			(
				select  ROUND( (
					SUM( f_s.current_balance ) / d.total_commodity_units ) * d.unit_cost, 1
		) AS total, f_s.facility_code from facility_stocks f_s, commodities d
		where d.id=f_s.commodity_id
		AND f_s.expiry_date between DATE_ADD(CURDATE(), INTERVAL 1 day) and  DATE_ADD(CURDATE(), INTERVAL $interval MONTH)
		AND f_s.status =(1 or 2)
		and year(f_s.expiry_date)=year(NOW())
		GROUP BY f_s.commodity_id,f_s.facility_code having total >1
		) temp
		on temp.facility_code = f.facility_code
		where  f.district = d1.id
		$and_data
		and temp.total>0
		group by f.facility_code");
			/////
		return $query;
	}
	public static function get_facility_drug_consumption_level($facilities_filter, $commodity_filter, $year_filter, $plot_value_filter) {
		switch ($plot_value_filter) :
		case 'ksh' :
		$computation = "CEIL((fs.qty_issued)*cms.unit_cost ) AS total_consumption";
		break;
		case 'units' :
		$computation = "fs.qty_issued AS total_consumption";
		break;
		case 'packs' :
		$computation = "CEIL(fs.qty_issued/cms.total_commodity_units) AS total_consumption";
		break;
		default :
		$computation = "fs.qty_issued AS total_consumption";
		break;
		endswitch;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT MONTHNAME( fs.date_issued ) as month, cms.commodity_name as Name,$computation 
			FROM facility_issues fs, commodities cms, facilities f, districts di, counties c
			WHERE fs.facility_code = f.facility_code
			AND f.facility_code = $facilities_filter
			AND fs.qty_issued > 0
			AND f.district = di.id
			AND fs.status =  '1'
			AND fs.commodity_id = $commodity_filter
			AND YEAR( fs.date_issued ) =$year_filter
			AND cms.id = fs.commodity_id
			GROUP BY MONTH( fs.date_issued ) asc");
		return $inserttransaction;
	}
	public static function get_facility_consumption_level_new($facilities_filter, $commodity_filter, $year_filter, $plot_value_filter) {
		switch ($plot_value_filter) :
		case 'ksh' :
		$computation = "CEIL((fs.qty_issued)*cms.unit_cost ) AS total_consumption";
		break;
		case 'units' :
		$computation = "fs.qty_issued AS total_consumption";
		break;
		case 'packs' :
		$computation = "CEIL(fs.qty_issued/cms.total_commodity_units) AS total_consumption";
		break;
		case 'service_point' :
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT fs.qty_issued AS total_consumption, fs.issued_to as service_name
			FROM facility_issues fs, commodities cms, facilities f, districts di, counties c
			WHERE fs.facility_code = f.facility_code
			AND f.facility_code = $facilities_filter
			AND fs.qty_issued > 0
			AND f.district = di.id
			AND fs.status =  '1'
			AND YEAR( fs.date_issued ) =$year_filter
			AND cms.id = fs.commodity_id
			GROUP BY service_name asc");
		return $inserttransaction;
		break;
		default :
		$computation = "fs.qty_issued AS total_consumption";
		break;
		endswitch;
		($commodity_filter == 0) ? $and_data = null : $and_data = "AND fs.commodity_id = $commodity_filter";
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT MONTHNAME( fs.date_issued ) as month, $computation 
			FROM facility_issues fs, commodities cms, facilities f, districts di, counties c
			WHERE fs.facility_code = f.facility_code
			AND f.facility_code = $facilities_filter
			AND fs.qty_issued > 0
			AND f.district = di.id
			AND fs.status =  '1'
			$and_data
			AND YEAR( fs.date_issued ) =$year_filter
			AND cms.id = fs.commodity_id
			GROUP BY MONTH( fs.date_issued ) asc");
		return $inserttransaction;
	}
	public static function get_filtered_commodity_consumption_level($facilities_filter, $commodity_filter, $year_filter, $plot_value_filter) {
		switch ($plot_value_filter) :
		case 'ksh' :
		$computation = "CEIL((fs.qty_issued)*cms.unit_cost ) AS total_consumption";
		break;
		case 'units' :
		$computation = "fs.qty_issued AS total_consumption";
		break;
		case 'packs' :
		$computation = "CEIL(fs.qty_issued/cms.total_commodity_units) AS total_consumption";
		break;
		default :
		$computation = "fs.qty_issued AS total_consumption";
		break;
		endswitch;
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT MONTHNAME( fs.date_issued ) as month, cms.commodity_name as Name,$computation 
			FROM facility_issues fs, commodities cms, facilities f, districts di, counties c
			WHERE fs.facility_code = f.facility_code
			AND f.facility_code = $facilities_filter
			AND fs.qty_issued > 0
			AND f.district = di.id
			AND fs.status =  '1'
			AND YEAR( fs.date_issued ) =$year_filter
			AND cms.id = fs.commodity_id
			GROUP BY MONTH( fs.date_issued ) asc");
		return $inserttransaction;
	}
	public static function get_commodity_consumption_level($facilities_code) {
		$year = date("Y");
		$inserttransaction = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT MONTHNAME( fs.date_issued )as month, cms.commodity_name as commodity, fs.qty_issued AS total_consumption
			FROM facility_issues fs, commodities cms, facilities f, districts di, counties c
			WHERE fs.facility_code = f.facility_code
			AND fs.qty_issued > 0
			AND f.district = di.id
			AND fs.status =  '1'
			AND fs.facility_code = $facilities_code
			AND YEAR( fs.date_issued ) = $year
			AND cms.id = fs.commodity_id
			GROUP BY MONTH( fs.date_issued ) asc");
		return $inserttransaction;
	}
	public static function get_expiries($facility_code, $year = NULL) {
		$year = (isset($year)) ? $year : date("Y");
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select fs.current_balance AS total_expiries, MONTHNAME(fs.expiry_date) as month from  facility_stocks fs 
			LEFT JOIN  commodities c 
			ON c.id=fs.commodity_id 
			where facility_code=$facility_code 
			AND DATE_FORMAT( fs.expiry_date,'%Y') = $year
			and expiry_date <= NOW()
			GROUP BY  MONTH(  `expiry_date` ) ");
		return $stocks;
	}
	public static function get_filtered_expiries($facility_code, $year, $month, $option) {
		switch ($option) :
		case 'KSH' :
		$computation = "(CEIL(fs.current_balance)*c.unit_cost ) AS total_expiries";
		break;
		case 'Units' :
		$computation = "fs.current_balance AS total_expiries";
		break;
		case 'Packs' :
		$computation = "(CEIL(fs.current_balance/c.total_commodity_units)) AS total_expiries";
		break;
		default :
		$computation = "fs.current_balance AS total_expiries";
		break;
		endswitch;
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select $computation, c.commodity_name as commodity from  facility_stocks fs 
			LEFT JOIN  commodities c 
			ON c.id=fs.commodity_id 
			where facility_code=$facility_code 
			and fs.status =2 
			and expiry_date <= NOW()
			AND DATE_FORMAT( fs.expiry_date,'%Y') = $year  
			AND DATE_FORMAT( fs.expiry_date,'%m') = $month  
			GROUP BY commodity ");
		return $stocks;
	}
	public static function import_stock_from_v1($facility_code) {
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select 
	    *
			from
			kemsa2.facility_stock
			left join
			hcmp_rtk.drug_commodity_map ON drug_commodity_map.old_id = facility_stock.kemsa_code
			where facility_stock.facility_code = $facility_code
			");
		return $stocks;
	}
	public static function import_amc_from_v1($facility_code = null, $commoity_id = null) {
		$and = isset($commoity_id) ? " and historical_stock.drug_id=$commoity_id" : null;
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select 
	    *
			from
			kemsa2.historical_stock
			left join
			hcmp_rtk.drug_commodity_map ON drug_commodity_map.old_id = historical_stock.drug_id
			where historical_stock.facility_code = $facility_code $and");
		return $stocks;
	}
	public static function import_issues_from_v1($facility_code = null, $commoity_id = null) {
		$and = isset($commoity_id) ? " and historical_stock.drug_id=$commoity_id" : null;
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("select 
				    *
			from
			kemsa2.facility_issues
			left join
			hcmp_rtk.drug_commodity_map ON drug_commodity_map.old_id = facility_issues.kemsa_code
			where facility_issues.facility_code = $facility_code
			and facility_issues.receipts=0");
		return $stocks;
	}
	public static function getpotentialexpcount($county_id, $district_id) {
		$and_data = " AND facilities.district=$district_id";
		if ($district_id == '') {
			$and_data = "AND districts.county=$county_id";
		}
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT * FROM `facility_stocks`
			INNER JOIN facilities
			ON facility_stocks.facility_code=facilities.facility_code
			INNER JOIN districts ON districts.id=facilities.district
			WHERE  expiry_date between DATE_ADD(CURDATE(), INTERVAL 1 day) and DATE_ADD(CURDATE(), INTERVAL 6 MONTH) AND current_balance>0 AND status IN (1,2)
			$and_data ");
		return $stocks;
	}
	public static function getexpcount($county_id, $district_id) {
		$year = date(Y);
		$and_data = " AND facilities.district=$district_id";
		if ($district_id == '') {
			$and_data = "AND districts.county=$county_id";
		}
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT facilities.facility_code,facilities.facility_name,districts.district 
			FROM `facility_stocks` 
			INNER JOIN commodities ON facility_stocks.commodity_id=commodities.id 
			INNER JOIN facilities ON facility_stocks.facility_code=facilities.facility_code 
			INNER JOIN districts ON districts.id=facilities.district WHERE expiry_date < NOW( )
			AND current_balance>0 AND facility_stocks.status IN (1,2)and year(expiry_date)=$year $and_data
			");
		return $stocks;
	}
	public static function getexplist($county_id, $year) {
		$and_data = "AND districts.county=$county_id";
		$stocks = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT facilities.facility_code,facilities.facility_name,districts.district,SUM((facility_stocks.current_balance/commodities.total_commodity_units)*commodities.unit_cost) as total 
			FROM `facility_stocks` 
			INNER JOIN commodities ON facility_stocks.commodity_id=commodities.id 
			INNER JOIN facilities ON facility_stocks.facility_code=facilities.facility_code 
			INNER JOIN districts ON districts.id=facilities.district WHERE expiry_date < NOW( )
			AND current_balance>0 AND facility_stocks.status IN (1,2)and year(expiry_date)=$year $and_data
			group by facilities.facility_code ");
		return $stocks;
	}
	//used to get the stock level for a specific commodity in the entire country
	public static function get_commodity_stock_level($commodity_id) {
		$stock_level = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			c.county,
			d1.district AS subcounty,
			f.facility_name,
			f.facility_code,
			d.commodity_name AS commodity_name,
			d.unit_size AS unit_size,
			round(f_s.current_balance,0) AS balance_units,
			round((f_s.current_balance / d.total_commodity_units),0) AS balance_packs,
			d.unit_cost,
			f_s.manufacture,
			f_s.batch_no,
			f_s.expiry_date,
			cs.source_name AS supplier,
			temp.total_units as amc_units,
			CASE temp.selected_option
			WHEN 'Pack_Size' THEN ROUND(temp.consumption_level, 0)
			WHEN 'Unit_Size' THEN ROUND(temp.total_units / temp.consumption_level, 0)
			ELSE 0
			END AS amc,
			(ROUND(f_s.current_balance/temp.total_units,0)) as mos
			FROM
			facilities f,
			districts d1,
			counties c,
			facility_stocks f_s,
			commodities d,
			facility_monthly_stock temp,
			commodity_source cs
			WHERE
			f_s.facility_code = f.facility_code
			AND f.district = d1.id
			AND temp.facility_code = f.facility_code
			AND d1.county = c.id
			AND f_s.commodity_id = d.id
			AND d.commodity_source_id = cs.id
			AND temp.commodity_id = f_s.commodity_id
			AND f_s.commodity_id = $commodity_id
			AND f_s.expiry_date >= NOW()
			AND f_s.current_balance >0
			GROUP BY f_s.batch_no,d.id , f.facility_code
			ORDER BY c.county ASC , d1.district ASC");
		return $stock_level;
	}
	//query to get the number of facilities stocked out on a particular commodity for the year
	public static function facilities_stocked_specific_commodity($commodity_id) {
		$year = date("Y");
		$no_of_facilities = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			distinct(facility_code)
			FROM
			facility_stocks
			WHERE
			current_balance = 0
			AND commodity_id = $commodity_id
			AND YEAR(date_added) = $year");
		return count($no_of_facilities);
	}
	//query to get the number of facilities reporting on a particular commodity for the year
	public static function facilities_reporting_on_a_specific_commodity($commodity_id) {
		$year = date("Y");
		$no_of_facilities = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT 
			distinct(facility_code)
			FROM
			facility_stocks
			WHERE
			commodity_id = $commodity_id
			AND year(expiry_date)>=$year
			AND YEAR(date_added) = $year");
		return count($no_of_facilities);
	}
	//query to get the number of batches expiring for a particular commodity for the year
	public static function batches_expiring_specific_commodities($commodity_id) {
		$year = date("Y");
		$no_of_facilities = Doctrine_Manager::getInstance() -> getCurrentConnection() -> fetchAll("SELECT DISTINCT
			(batch_no)
			FROM
			facility_stocks
			WHERE
			current_balance > 0
			AND commodity_id = $commodity_id
			AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 MONTH)");
		return count($no_of_facilities);
	}

	public static function get_service_point_stocks($facility_code = NULL,$service_point = NULL,$potential_expiries = NULL,$expiries = NULL){
		$posterior = isset($facility_code)? "AND sp.facility_code = $facility_code " :NULL;
		$posterior .= isset($service_point) ? "AND sp.service_point_id = $service_point " : NULL;
		$posterior .= isset($potential_expiries) ? "AND sp.expiry_date BETWEEN CURDATE()AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH) " : NULL;
		$posterior .= isset($expiries) ? "AND sp.expiry_date < NOW()" : NULL;
		
		$query = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAll("
			SELECT 
			    sp.id AS service_point_stock_id,
			    sp.facility_code,
			    sp.service_point_id,
			    sp.commodity_id,
				c.commodity_name,
				c.total_commodity_units,
			    sp.current_balance,
			    sp.batch_no,
			    sp.expiry_date,
			    s.id AS service_point_id,
			    s.service_point_name
			FROM
			    service_point_stocks sp,
			    service_points s,
			    commodities c
			WHERE
			    s.id = sp.service_point_id AND c.id = sp.commodity_id $posterior
    	");

		return $query;
	}

	public static function service_point_expiries(){

	}
}
