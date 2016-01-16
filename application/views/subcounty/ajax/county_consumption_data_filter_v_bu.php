<?php
$year = date("Y");
$county_id = $this -> session -> userdata('county_id');
$district_id_active =  $this -> session -> userdata('district_id');
  $identifier = $this -> session -> userdata('user_indicator');
		
        switch ($identifier):
			case 'district':
			$link=	base_url('reports/order_listing/subcounty/true');
			$link2=	base_url('reports/order_listing/subcounty');
			break;
			case 'county':
			$link=	base_url('reports/order_listing/county/true');
			$link2=	base_url('reports/order_listing/county');
			break;
			 endswitch;
			$temp = array_filter($county_dashboard_notifications, function($value){
			    return $value > 0;
			});	
			$count_for_class=count($temp);
			$class="";
			if ($count_for_class==1) {
				
				 $class="col-md-12 stat_item";
				
			} elseif($count_for_class==2) {
				
				$class="col-md-6 stat_item";
				
			}elseif($count_for_class==3) {
				
				 $class="col-md-4 stat_item ";
				
			}elseif($count_for_class==4) {
				
				 $class="col-md-3 stat_item ";
				
			}else {
				$class="col-md-2 stat_item";
			}	
?>
<style>
	.stat_item {
		height: 52px;
		padding: 2px 5px;
		margin:0 1px 1px 1px;
		color: #fff;
		text-align: center;
		font-size: 1em;
		
	}	
	.stat_item:hover{
	-webkit-transform: scale(1.005);
	-moz-transform: scale(1.005);
	margin-left: 2px;
	-webkit-transition-duration: 50ms;
	-webkit-transition-function: ease-out;
	-moz-transition-duration: 50ms;
	-moz-transition-function: ease-out;
	box-shadow: 0 1px 3px 0.5px #000;
}
	.bold{
		font-weight:800;
	}
 	.input-small{
 		width: 100px !important;
 	}
  .filter-row{
    margin: none;
  }
 </style>
 <div class="row-fluid" style="margin-bottom: 5px">
			
         <?php if($county_dashboard_notifications['actual_expiries']>0): ?>
         	
         	<a href="<?php echo base_url('reports/county_expiries/')?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['actual_expiries'];?></span>
                  	<br/>
                  	 <span id="">Expired Commodities</span>
                            
                   </div></a>
      
         <?php endif; // Actual Expiries?>
         <?php if($county_dashboard_notifications['items_stocked_out_in_facility']>0): ?>
         	
         	<a href="<?php echo base_url('reports/stock_out/')?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['items_stocked_out_in_facility'];?></span>
                  	<br/>
                  	 <span id="">Facilities have stock outs</span>
                            
                   </div></a>
         	
        <?php endif; // items_stocked_out_in_facility?>
          <?php if($county_dashboard_notifications['potential_expiries']>0): ?>
          	
          	<a href="<?php echo base_url('reports/county_expiries/')?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['potential_expiries'];?></span>
                  	<br/>
                  	 <span id="">Commodity (ies) Expiring in 6 months</span>
                            
                   </div></a>
          	
      	
		  <?php endif; // Potential Expiries?>
        <?php if(array_key_exists('pending', $county_dashboard_notifications['facility_order_count']) 
        && @$county_dashboard_notifications['facility_order_count']['pending']>0): ?>
        
        <a href="<?php echo $link2 ?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['facility_order_count']['pending'];?></span>
                  	<br/>
                  	 <span id="">Order(s) Pending</span>
                            
                   </div></a>
      	
        <?php endif; //pending?>
        <?php if(array_key_exists('approved', $county_dashboard_notifications['facility_order_count'])
		 && @$county_dashboard_notifications['facility_order_count']['approved']>0): ?>
		 
		  <a href="<?php echo $link ?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['facility_order_count']['approved'];?></span>
                  	<br/>
                  	 <span id="">Order(s) pending dispatch from KEMSA</span>
                            
                   </div></a>
		 
         <?php endif; //approved?>
        <?php if(array_key_exists('rejected', $county_dashboard_notifications['facility_order_count']) 
         && @$county_dashboard_notifications['facility_order_count']['rejected']>0): ?>
         
         <a href="<?php echo $link ?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['facility_order_count']['rejected'];?></span>
                  	<br/>
                  	 <span id="">Order(s) rejected</span>
                            
                   </div></a>
         
        
        <?php endif; //rejected?>
    <?php if($county_dashboard_notifications['facility_donations']>0): ?>
 		 <a href="<?php echo base_url('reports/county_donation/')?>">
 					<div class="color_e stat_item <?php echo $class; ?>">
						<span class="bold"><?php 
				echo $county_dashboard_notifications['facility_donations'];?></span>
                  	 <br/>
                  	 <span id="">Items have been donated</span>
                            
                   </div></a>
		  <?php endif; // Donated Items?>
           
</div>
<div class="alert alert-info" style="width: 100%">
  <b>Below is the consumption Level in the County </b> :Select filter Options
</div>
<ul class='nav nav-tabs'>
	  <li class="active"><a href="#tracer" data-toggle="tab">Tracer Items</a></li>
      <li class=""><a href="#cat" data-toggle="tab">Categories</a></li>
      <li class=""><a href="#county" data-toggle="tab">County View</a></li>
      <li class=""><a href="#subcounty" data-toggle="tab">Sub County View</a></li>
</ul>
    <div id="myTabContent" class="tab-content">
	<!--for the tracer items tab-->
      <div  id="tracer" class="tab-pane fade active in">

      	<br />
      	<div class="filter">
      		<form class="form-inline" role="form">
      			<select id="tracer_commodity_filter" class="form-control col-md-3">
      				<option value="NULL">Select Commodity</option>
					<?php
					foreach($tracer_items as $data):
					        $commodity_name=$data->commodity_name;   
					        $commodity_id=$data->id;
					        echo "<option value='$commodity_id'>$commodity_name</option>";
					endforeach;
					?>
				</select>
				<select id="tracer_district_filter" class="form-control col-md-2">
					<option selected="selected" value="NULL">Select Sub-county</option>
					<?php
					foreach($district_data as $district_):
					        $district_id=$district_->id;
					        $district_name=$district_->district;    
					        echo "<option value='$district_id'>$district_name</option>";
					endforeach;
					?>
				</select> 
				<!--Date Pickers for from and to-->
				<input type="text" name="tracer_from"  id="tracer_from" placeholder="FROM" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />
				<input type="text" name="tracer_to"  id="tracer_to" placeholder="TO" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" /> 
				<select id="tracer_plot_value_filter" class="form-control col-md-2">
					<option selected="selected" value="NULL">Select Plot value</option>
					<option value="packs">Packs</option>
					<option value="units">Units</option>
					<option value="ksh">KSH</option>
				</select>
				<div class="col-md-1">
					<button class="btn btn-sm btn-success tracer-filter"><span class="glyphicon glyphicon-filter"></span>Filter</button> 
				</div>
				<div class="col-md-1">
					<button class="btn btn-sm btn-success tracer-download"><span class="glyphicon glyphicon-save"></span>Download</button> 
				</div>
				<div class="col-md-1">
					<button class="btn btn-sm btn-primary tracer-filter-table"><span class="glyphicon glyphicon-th-list"></span>Results as Table</button> 
				</div>
          </form>
      </div>
  </div>
       <div  id="cat" class="tab-pane fade">
       	<br>
       	<div class="filter row">
<form class="form-inline" role="form">
       	<select id="category_filter" class="form-control col-md-3">
<option value="NULL">Select Commodity Category</option>
<?php
foreach($categories as $data):
		$commodity_name=$data->sub_category_name;	
		$commodity_id=$data->id;
		echo "<option value='$commodity_id'>$commodity_name</option>";
endforeach;
?>
</select>
	<select id="category_district_filter" class="form-control col-md-2">
<option selected="selected" value="NULL">Select Sub-county</option>
<?php
foreach($district_data as $district_):
		$district_id=$district_->id;
		$district_name=$district_->district;	
		echo "<option value='$district_id'>$district_name</option>";
endforeach;
?>
</select> 
<select id="category_facility_filter" class="form-control col-md-3">
<option value="NULL">Select facility</option>
</select>
<input type="text" name="from"  id="category_from" 
placeholder="FROM" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />
<input type="text" name="to"  id="category_to" placeholder="TO" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />		
<select id="category_plot_value_filter" class="form-control col-md-2">
<option selected="selected" value="NULL">Select Plot value</option>
<option value="packs">Packs</option>
<option value="units">Units</option>
<option value="ksh">KSH</option>
</select>
<div class="col-md-1">
<button class="btn btn-sm btn-success category-filter"><span class="glyphicon glyphicon-filter"></span>Filter</button> 
</div>
<div class="col-md-1">
<button class="btn btn-sm btn-success category-download"><span class="glyphicon glyphicon-save"></span>Download</button> 
</div>	
<div class="col-md-1">
<button class="btn btn-sm btn-primary category-filter-table"><span class="glyphicon glyphicon-filter"></span>Results as Table</button> 
</div>
   </form>
      </div>
   
      </div>
      <div  id="county" class="tab-pane fade in">
      	<br>
<div class="filter">
<form class="form-inline" role="form">
<select id="county_commodity_filter" class="form-control col-md-3">
<option value="NULL">Select Commodity</option>
<?php
foreach($c_data as $data):
		$commodity_name=$data['commodity_name'];	
		$commodity_id=$data['id'];
		echo "<option value='$commodity_id'>$commodity_name</option>";
endforeach;
?>
</select>		
<select id="county_plot_value_filter" class="form-control col-md-2">
<option selected="selected" value="NULL">Select Plot value</option>
<option value="packs">Packs</option>
<option value="units">Units</option>
<option value="ksh">KSH</option>
</select>
<input type="text" name="from"  id="county_from" 
placeholder="FROM" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />
<input type="text" name="to"  id="county_to" placeholder="TO" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />		

<div class="col-md-1">
<button class="btn btn-sm btn-success county-filter"><span class="glyphicon glyphicon-filter"></span>Filter</button> 
</div>
<div class="col-md-1">
<button class="btn btn-sm btn-success county-download"><span class="glyphicon glyphicon-save"></span>Download</button> 
</div>
<div class="col-md-1">
<button class="btn btn-sm btn-primary county-filter-table"><span class="glyphicon glyphicon-th-list"></span>Results as Table</button> 
</div>

</form>
</div>
</div>
      <div  id="subcounty" class="tab-pane fade in">
      	<br>
<div class="filter row">
<form class="form-inline" role="form">
<select id="subcounty_commodity_filter" class="form-control col-md-2">
<option value="NULL">Select Commodity</option>
<?php
foreach($c_data as $data):
		$commodity_name=$data['commodity_name'];	
		$commodity_id=$data['id'];
		echo "<option value='$commodity_id'>$commodity_name</option>";
endforeach;
?>
</select>	
	<select id="subcounty_district_filter" class="form-control col-md-2">
<option selected="selected" value="NULL">Select Sub-county</option>
<?php
foreach($district_data as $district_):
		$district_id=$district_->id;
		$district_name=$district_->district;	
		echo "<option value='$district_id'>$district_name</option>";
endforeach;
?>
</select> 
<select id="subcounty_facility_filter" class="form-control col-md-2">
<option value="NULL">Select facility</option>
</select>	
<input type="text" name="from"  id="from" placeholder="FROM" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />
<input type="text" name="to"  id="to" placeholder="TO" class="form-control input-small col-md-1 clone_datepicker_normal_limit_today" />		
<select id="subcounty_plot_value_filter" class="form-control col-md-2">
<option selected="selected" value="NULL">Select Plot value</option>
<option value="packs">Packs</option>
<option value="units">Units</option>
<option value="ksh">KSH</option>
</select>
<div class="col-md-1">
<button class="btn btn-sm btn-success subcounty-filter"><span class="glyphicon glyphicon-filter"></span>Filter</button> 
</div>
<div class="col-md-1">
<button class="btn btn-sm btn-success subcounty-download"><span class="glyphicon glyphicon-save"></span>Download</button> 
</div>
<div class="col-md-1">
<button class="btn btn-sm btn-primary subcounty-filter-table"><span class="glyphicon glyphicon-th-list"></span>Results as Table</button> 
</div>
</form>
</div>
</div>
</div>
<div class="graph_content" id="graph_content_">	
</div>
<script>
	 $(function () { 
<?php echo $consumption_default; ?>
});

	$(document).ready(function() {
    <?php echo $default_consumption_graph; ?>
    
		json_obj = { "url" : "assets/img/calendar.gif'",};
var baseUrl=json_obj.url;
    //	-- Datepicker	limit today		
	$(".clone_datepicker_normal_limit_today").datepicker({
    maxDate: new Date(),				
	dateFormat: 'd M yy', 
	changeMonth: true,
	changeYear: true,
	buttonImage: baseUrl,       });	
	    
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          $('.graph_content').html('');
          })
		$("#subcounty_facility_filter,#category_facility_filter").hide();
		
		$("#subcounty_district_filter").change(function() {
		var option_value=$(this).val();
		
		if(option_value=='NULL'){
		$("#subcounty_facility_filter").hide('slow');	
		}
		else{
var drop_down='';
 var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json_data/"+option_value;
  $.getJSON( hcmp_facility_api ,function( json ) {
     $("#subcounty_facility_filter").html('<option value="NULL" selected="selected">--select facility--</option>');
      $.each(json, function( key, val ) {
      	drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>";	
      });
      $("#subcounty_facility_filter").append(drop_down);
    });
		$("#subcounty_facility_filter").show('slow');		
		}
		});	
		$("#category_district_filter").change(function() {
		var option_value=$(this).val();
		if(option_value=='NULL'){
		$("#category_facility_filter").hide('slow');	
		}
		else{
var drop_down='';
 var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json_data/"+option_value;
  $.getJSON( hcmp_facility_api ,function( json ) {
     $("#facility_filter").html('<option value="NULL" selected="selected">--select facility--</option>');
      $.each(json, function( key, val ) {
      	drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>";	
      });
      $("#category_facility_filter").append(drop_down);
    });
		$("#category_facility_filter").show('slow');		
		}
		});	
		//tracer filter
		 $(".tracer-filter").button().click(function(e) {
        e.preventDefault(); 
        
        var from =$("#tracer_from").val();
        var to =$("#tracer_to").val();
        
        if(from==''){from="NULL";}
        if(to==''){to="NULL";}
        var url_ = "reports/consumption_data_dashboard/"+
       $("#tracer_commodity_filter").val()+"/"+
       $("#tracer_district_filter").val()+"/NULL/"+$("#tracer_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/NULL/1"; 
        ajax_request_replace_div_content(url_,'.graph_content'); 
        
          });   
		 // tracer filter table
		 $(".tracer-filter-table").button().click(function(e) {
        e.preventDefault(); 
        
        var from =$("#tracer_from").val();
        var to =$("#tracer_to").val();
        
        if(from==''){from="NULL";}
        if(to==''){to="NULL";}

        var url_ = "reports/consumption_stats_graph/"+
       $("#tracer_commodity_filter").val()+"/NULL/"+$("#tracer_district_filter").val()+"/NULL/"+$("#tracer_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/"+"table_data"+"/1"; 
        ajax_request_replace_div_content(url_,'.graph_content');    
          }); 

              
       $(".tracer-download").button().click(function(e) {
        e.preventDefault(); 
        var from =$("#county_from").val();
        var to =$("#county_to").val();
        
        if(from==''){from="NULL";   }
        if(to==''){to="NULL";}

  		var url_ = "reports/consumption_stats_graph/"+
       $("#tracer_commodity_filter").val()+"/NULL/NULL/NULL/"+$("#tracer_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/csv_data"; 
         window.open(url+url_ ,'_blank');   
          }); 
		//
	$(".county-filter").button().click(function(e) {
		e.preventDefault();	
		var from =$("#county_from").val();
		var to =$("#county_to").val();
		
		if(from==''){from="NULL";	}
        if(to==''){to="NULL";}

        var url_ = "reports/consumption_stats_graph/"+
       $("#county_commodity_filter").val()+"/NULL/NULL/NULL/"+$("#county_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/graph";	
		ajax_request_replace_div_content(url_,'.graph_content');	
          });	

	$(".county-filter-table").button().click(function(e) {
		e.preventDefault();	
		
		var from =$("#county_from").val();
		var to =$("#county_to").val();
		
		if(from==''){from="NULL";	}
        if(to==''){to="NULL";}

        var url_ = "reports/consumption_stats_graph/"+
       $("#county_commodity_filter").val()+"/NULL/NULL/NULL/"+$("#county_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/table_data";	
		ajax_request_replace_div_content(url_,'.graph_content');	
          });

              
       $(".county-download").button().click(function(e) {
		e.preventDefault();	
		var from =$("#county_from").val();
		var to =$("#county_to").val();
		
		if(from==''){from="NULL";	}
        if(to==''){to="NULL";}

  var url_ = "reports/consumption_stats_graph/"+
       $("#county_commodity_filter").val()+"/NULL/NULL/NULL/"+$("#county_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/csv_data";	
		 window.open(url+url_ ,'_blank');	
          }); 
       //subcounty filter
        $(".subcounty-filter").button().click(function(e) {
		e.preventDefault();	
        var url_ = "reports/get_county_stock_level_new/"+
$("#subcounty_commodity_filter").val()+"/NULL/"+$("#subcounty_district_filter").val()+"/"+ $("#subcounty_facility_filter").val()+"/"+$("#subcounty_plot_value_filter").val()+ "/NULL/NULL";	
		ajax_request_replace_div_content(url_,'.graph_content');	
          });
        //subcounty as table filter
        $(".subcounty-filter-table").button().click(function(e) {
		e.preventDefault();	
        var url_ = "reports/get_county_stock_level_new/"+
$("#subcounty_commodity_filter").val()+"/NULL/"+$("#subcounty_district_filter").val()+"/"+ $("#subcounty_facility_filter").val()+"/"+$("#subcounty_plot_value_filter").val()+ "/table_data/NULL";	
		ajax_request_replace_div_content(url_,'.graph_content');	
          });


        $(".subcounty-download").button().click(function(e) {
		e.preventDefault();	
        var url_ = "reports/get_county_stock_level_new/"+
$("#subcounty_commodity_filter").val()+"/NULL/"+$("#subcounty_district_filter").val()+"/"+ $("#subcounty_facility_filter").val()+"/"+$("#subcounty_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/csv_data";	;	
		 window.open(url+url_ ,'_blank');	
          });
          
         $(".category-filter").button().click(function(e) {
		e.preventDefault();	
        var url_ = "reports/get_county_stock_level_new/"+
"NULL/"+$("#category_filter").val()+"/"+$("#category_district_filter").val()+"/"+ $("#category_facility_filter").val()+"/"+$("#category_plot_value_filter").val()+ "/"+"NULL"+ "/"+"NULL";	
		ajax_request_replace_div_content(url_,'.graph_content');	
          });
          
          $(".category-filter-table").button().click(function(e) {
		e.preventDefault();	
        var url_ = "reports/get_county_stock_level_new/"+
"NULL/"+$("#category_filter").val()+"/"+$("#category_district_filter").val()+"/"+ $("#category_facility_filter").val()+"/"+$("#category_plot_value_filter").val()+ "/"+"table_data"+ "/"+"NULL";	
		ajax_request_replace_div_content(url_,'.graph_content');	
          });

        $(".category-download").button().click(function(e) {
		e.preventDefault();	
        var url_ = "reports/get_county_stock_level_new/"+
"NULL/"+$("#category_filter").val()+"/"+$("#category_district_filter").val()+"/"+ $("#category_facility_filter").val()+"/"+$("#category_plot_value_filter").val()+ "/"+encodeURI(from)+ "/"+encodeURI(to)+"/csv_data";	
		 window.open(url+url_ ,'_blank');	
          });	  


		});
</script>