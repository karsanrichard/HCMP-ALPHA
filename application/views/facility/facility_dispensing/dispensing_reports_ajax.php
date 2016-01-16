<?php 	
//Pick values from the seesion 	
$facility_code=$this -> session -> userdata('facility_id');
$district_id=$this -> session -> userdata('district_id');
$county_id =$this -> session -> userdata('county_id'); 

?>
<style>
 	.input-small{
 		width: 100px !important;
 	}
 	.input-small_{
 		width: 230px !important;
 	}
 </style>
<h1 class="page-header" style="margin: 0;font-size: 1.6em;">Consumption</h1>
<div class="alert alert-info" style="width: 100%">
  <b>Below is the consumption Level in the County </b> :Select filter Options
</div>
<div class="filter row">
<form class="form-inline" role="form">
<select id="commodity_filter" class="form-control col-md-2">
	<option value="NULL">Select Commodity</option>
	<?php
	foreach($c_data as $data):
			$commodity_name = $data['commodity_name'];	
			$commodity_id = $data['commodity_id'];
			echo "<option value='$commodity_id'>$commodity_name</option>";
	endforeach;
	?>
</select>
<input type="text" name="from"  id="from" placeholder="FROM" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />
<input type="text" name="to"  id="to" placeholder="TO" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />		
<!--<select name="year" id="year_filter" style="width: 7.8em;">
		<option value="0">Select Year</option>
		<option value="2014">2014</option>
		<option value="2013">2013</option>
</select>-->
<select id="plot_value_filter" class="form-control col-md-3">
	<option value="NULL">Select Plot value</option>
	<option value="packs">Packs</option>
	<option value="units">Units</option>
	<option value="ksh">KSH</option>
	<option value="service_point">Per Service Point</option>
</select> 
<div class="col-md-1">
<button class="btn btn-sm btn-success" id="filter" name="filter"><span class="glyphicon glyphicon-filter">Filter</button> 
</div>
</form>	
</div>

<div class='graph-section' id='graph-section'></div>

<script>
	$(document).ready(function() 
	{
		json_obj = { "url" : "assets/img/calendar.gif'",};
		var baseUrl=json_obj.url;
		<?php echo @$graph_data; ?>
	
			$("#filter").button().click(function(e) 
			{
				e.preventDefault();
				var from =$("#from").val();
		        var to =$("#to").val();
		        
		        if(from==''){from="NULL";}
		        if(to==''){to="NULL";}
	        
		        //($commodity_id = null, $option = null,$from = null,$to = null,$report_type = null)
		        //($commodity_id = null,$category_id = null, $district_id = null, $facility_code=null, $option = null,$from=null,$to=null,$report_type=null)
		        var url = "reports/consumption_stats_graph/"+
							$("#commodity_filter").val()+
							"/NULL/"+"<?php echo $district_id;?>"+
							"/"+"<?php echo $facility_code;?>"+"/"+
							$("#plot_value_filter").val()+
							"/"+encodeURI(from)+
							"/"+encodeURI(to)+
							"/"+'table_data';
 				ajax_request_replace_div_content(url,'.graph-section');
		
          });
          
	  //	-- Datepicker	limit today		
	$(".clone_datepicker_normal_limit_today").datepicker({
    maxDate: new Date(),				
	dateFormat: 'd M yy', 
	changeMonth: true,
	changeYear: true,
	buttonImage: baseUrl,       });	

		
  });
</script>