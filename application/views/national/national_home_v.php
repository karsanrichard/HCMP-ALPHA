<!DOCTYPE html>
<html  lang="en">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>National</title>
		<link rel="icon" href="<?php echo base_url().'assets/img/coat_of_arms.png'?>" type="image/x-icon" />
		<link rel="stylesheet" href="<?php echo base_url().'assets/css/override.css'?>" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="<?php echo base_url().'assets/boot-strap3/css/bootstrap.min.css'?>" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="<?php echo base_url().'assets/boot-strap3/css/bootstrap-responsive.css'?>" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="<?php echo base_url().'assets/bower_components/semantic/packaged/css/semantic.css'?>" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="<?php echo base_url().'assets/datatable/TableTools.css'?>" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="<?php echo base_url().'assets/datatable/dataTables.bootstrap.css'?>" media="screen" title="no title" charset="utf-">
		<link rel="stylesheet" href="<?php echo base_url().'assets/scripts/pace.js'?>" media="screen" title="no title" charset="utf-8">

		<script src="<?php echo base_url().'assets/FusionCharts/FusionCharts.js'?>" type="text/javascript"></script>

	</head>

	<body id="page-top">
	<style>
	.active_{
		color: #555!important;
		background-color: #e7e7e7!important;
	}
	</style>
		<nav id="mainNav" class="navbar navbar-inverse navbar-fixed-top container-fluid">
			
			<div class="navbar-header" id="st-trigger-effects">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
         <a href="<?php echo base_url().'kenya';?>">
         	
         	<img class="navbar-brand" style="padding:0;" src="<?php echo base_url();?>assets/img/coat_of_arms_dash.png" class="img-responsive " alt="Responsive image" id="" >
         </a>
            
          

        </div>
        
        <div class="navbar-brand" >
  
            <a  href="<?php echo base_url().'kenya';?>" >Health Commodities Management Platform.</a>
            
        </div>
        
        <div class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav navbar-right">
            <li class="active_"><a href="<?php echo base_url().'national/reports';?>">Reports</a></li>
            <li class="dropdown" style="background: #144d6e; color: white;">
     		<a href="#" class="dropdown-toggle" style="color:white" data-toggle="dropdown" role="button" aria-expanded="false">Log In <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
              	<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo base_url().'home';?>"><span class="glyphicon glyphicon-user"></span>Essential Commodities</a></li>
                <li role="presentation" class="divider"></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="http://41.89.6.223/HCMP/user"><span class="glyphicon glyphicon-user"></span>RTK</a></li>
                <li role="presentation" class="divider"></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="http://41.89.6.209/sms_system"><span class="glyphicon glyphicon-user"></span>SMS System</a></li>
                
              </ul>
            </li>
                    
          </ul>
          
                                        
        </div><!--/.nav-collapse -->

			<!-- /.container-fluid -->

		</nav>

		<div class="container-fluid">

			<div class=" row-fluid" style="margin-bottom: 12px;">
				
				

				<div class=" tile col-xs-12 col-md-3 col-lg-3" id="coverage-map">
					
					<div class="row-fluid">
						<input type="hidden" value="NULL" id="placeholder" />
					<div class=" col-lg-12"  style="max-height: 480px;">
					<div class="tile-header">
						National Coverage
					</div>
					<div class="row-fluid" style="height: 60px;">
						<div class="col-md-1" >
						</div>
						<div class="col-md-8"><strong>Click County to View Data</strong> </div>
					</div>
					<div id="map" style="height: 400px;"></div>
					<script>
					var map= new FusionMaps ("assets/FusionMaps/FCMap_KenyaCounty.swf","KenyaMap","100%","100%","0","0");
					map.setJSONData(<?php echo $maps; ?>
						);
					
						map.render("map");

					</script>

					</div>
					</div>
					<div class="row-fluid" style="height: 60px;">
						<div class="col-md-1" >
							<div id="map-key"></div>
						</div>
						<div class="col-md-8"><strong>Using HCMP</strong> </div>
					</div>
					
				

				</div>

				<div class="col-xs-12 col-md-9 col-lg-9">
					<div class="ui statistics ">
					<div class="ui grid tile ">
						<div class="tile-header">
							National Statistics
						</div>
						<div class="four wide column"></div>
						<div class="four wide column">
							<div class="statistic excel_" id="hcwtrained">
								<div class="value" id="hcw_trained">
									
								</div>
								<div class="label" >
									Total HCW Trained
								</div>
							</div>
						</div>
						<div class="four wide column">
							<div class="statistic excel_" id="rolledout">
								<div class="text value" id="facilities_rolled_out" >
									
								</div>
								<div class="label">
									Total Facilities Rolled Out
								</div>
							</div>
						</div>
						<!-- <div class="four wide column">
							<div class="statistic">
								<div class="value">
									<i class=""></i> #
								</div>
								<div class="label">
									Avg Stock out days
								</div>
							</div>
						</div>
						<div class="three wide column">
							<div class="statistic">
								<div class="value">
									#
								</div>
								<div class="label">
									Avg order fill rate
								</div>

							</div>

						</div> -->
						<div class="one wide column" style="padding:0.5%">
							<div class="corner-label right" data-toggle="modal" data-target="#facilitystatsModal">

								
							</div>
						</div>

					</div>

					<div class="ui grid">
						<div class="tile" style="height: 430px">
							<div class="" style="height: 50px; padding: 8px;margin: 0;">

								

							</div>
							<div id="actual"></div>
						</div>
					</div>

				</div>

					

				</div>

			</div>

			<div class="row-fluid">

				<div class="tile col-xs-12 col-md-6 col-lg-6">
					<div class="tile-header">Cost of Orders</div>
					
					<div id="orders" class="tile_size"></div> <!--- orders -->
					
				</div>
				
				<div class="tile col-xs-12 col-md-6 col-lg-6">
					<div class="tile-header">Consumption</div>
					
					<div id="consumption" class="tile_size"></div> <!-- consumption -->
					
				</div>

			</div>
			
			<div class="row-fluid" style="max-height: 430px;margin-top: 4%;">

				<div class="">
					
					<div class="tile-header">
							Stock Level in Months of Stock (MOS)
						</div>
					
					<div id="mos" class="tile_size"></div> <!--- MOS -->
					
				</div>
<!-- 				
				<div class="tile col-xs-12 col-md-6 col-lg-6">
					<div class="tile-header" >Order Lead Time</div>
					
					<div id="" class="tile_size">
						<div id="lead_infor" style="padding-top: 20px;">
         


      				 </div>
					</div>
					
				</div>
 -->
			</div>

		</div>

		<footer class="footer" style="margin-top: 2%;">

			<div class="container footer-text">

				<div class="row">
					<p class="text-muted" style="text-align: center;"> Government of Kenya &copy <?php echo date('Y');?>. All Rights Reserved</p>
				</div>

			</div>

		</footer>

		<!-- jQuery -->

		<script src="<?php echo base_url().'assets/scripts/jquery.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/pace.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/select2.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/highcharts/highcharts.js'?>"></script>
		<script src="<?php echo base_url().'assets/highcharts/exporting.js'?>"></script>
		<script src="<?php echo base_url().'assets/boot-strap3/js/bootstrap.min.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/jquery-ui.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/validator.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/jquery.validate.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/waypoints.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/waypoints-sticky.min.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/typehead/typeahead.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'assets/scripts/typehead/handlebars.js'?>" type="text/javascript"></script>

		<script>

			$( document ).ready(function() {
paceOptions = {
ajax: false, // disabled
document: true, //
eventLag: true,
restartOnPushState: false,
elements:{
selectors:['body']
} //

};

function load(time){
var x = new XMLHttpRequest()
x.open('GET', document.URL , true);
x.send();
};
setTimeout(function(){
Pace.ignore(function(){
load(3100);
});
},4500);

Pace.on('hide', function(){
//   console.log('done');
});

var url="<?php echo base_url(); ?>
	";

	});
	
	
	
	//auto run
         var url ='<?php echo base_url()?>';
        // $('#potential_').on('shown.bs.tab', function (e) {
        // $('#potential').html('');
       // });
         $('#actual_').on('shown.bs.tab', function (e) {
         $('#actual').html('');
         });

      $('.county-name').html("National "+" &nbsp;");
      ajax_request_replace_div_content('national/expiry/NULL/NULL/NULL/NULL/NULL',"#actual"); 
      //ajax_request_replace_div_content('national/potential/NULL/NULL/NULL/NULL/NULL',"#potential"); 
      ajax_request_replace_div_content('national/facility_over_view/',"#facilities_rolled_out");
      ajax_request_replace_div_content('national/hcw/',"#hcw_trained");
      ajax_request_replace_div_content('national/stock_level_mos/NULL/NULL/NULL/NULL/NULL',"#mos");
      ajax_request_replace_div_content('national/consumption/NULL/NULL/NULL/NULL',"#consumption");
      ajax_request_replace_div_content('national/get_facility_infor/NULL/NULL/NULL/NULL',"#facilities");      
      ajax_request_replace_div_content('national/order/NULL/NULL/NULL/NULL/NULL',"#orders");
      ajax_request_replace_div_content('national/get_lead_infor/NULL/NULL/NULL/NULL/NULL',"#lead_infor");
      
        $(".ecounty-filter").button().click(function(e) {
        e.preventDefault(); 
        var year = $("#eyear").val();
        var county = $("#ecounty_filter").val();
       // var district=$(this).closest("tr").find("#ecounty_filter").val();
       // var facility=$(this).closest("tr").find("#ecounty_filter").val();
           ajax_request_replace_div_content('national/expiry/'+year+'/'+county+'/NULL/NULL/NULL',"#actual");
        });
        
        $(".asubcounty-filter").button().click(function(e) {
        e.preventDefault(); 
        var year=$("#asubcountyyear").val();
        var county_id=$('#county_id').val();
        var district=$("#asubcounty_filter").val();
        var facility=$("#asubcounty_facility_filter").val();
        ajax_request_replace_div_content('national/expiry/'+year+'/'+county_id+'/'+district+'/'+facility+'/NULL',"#actual");
        });
        /////potential
        $(".pcounty-filter").button().click(function(e) {
        e.preventDefault(); 
        var county=$("#pcounty_filter").val();
        ajax_request_replace_div_content('national/potential/'+county+'/NULL/NULL/NULL',"#potential");
        });
        
        $(".psubcounty-filter").button().click(function(e) {
        e.preventDefault(); 
        var county_id=$('#county_id').val();
        var district=$("#psubcounty_filter").val();
        var facility=$("#psubcounty_facility_filter").val();
        ajax_request_replace_div_content('national/potential/'+county_id+'/'+district+'/'+facility+'/NULL',"#potential");
        });
     
         $(".subcounty").click(function(){
            /*
             * when clicked, this object should populate facility names to facility dropdown list.
             * Initially it sets a default value to the facility drop down list then ajax is used 
             * is to retrieve the district names using the 'dropdown()' method used above.
             */
            json_obj = {"url":"<?php echo site_url("orders/getFacilities");?>",}
            var baseUrl = json_obj.url;
            var id = $(this).attr("value");
            $('.subcounty').val(id);
            dropdown(baseUrl,"district="+id,".facility");
 
          
        });

      
    function run(data){
        var county_data=data.split('^');
        $('#placeholder').val(county_data[0]);
        $('.county-name').html(county_data[1]+"&nbsp;County &nbsp;");
        ajax_request_replace_div_content('national/facility_over_view/'+county_data[0],"#facilities_rolled_out");
        ajax_request_replace_div_content('national/hcw/'+county_data[0],"#hcw_trained");
        $('.county').val(county_data[0]);
        $('#county_id').val(county_data[0]);
        json_obj={"url":"<?php echo site_url("orders/getDistrict");?>",}
        var baseUrl=json_obj.url;
        dropdown(baseUrl,"county="+county_data[0],".subcounty");
        ajax_request_replace_div_content('national/expiry/NULL/'+county_data[0]+'/NULL/NULL/NULL',"#actual");
        //ajax_request_replace_div_content('national/potential/'+county_data[0]+'/NULL/NULL/NULL/NULL',"#potential"); 
        ajax_request_replace_div_content('national/stock_level_mos/'+county_data[0]+'/NULL/NULL/NULL/ALL',"#mos");
        ajax_request_replace_div_content('national/consumption/'+county_data[0]+'/NULL/NULL/NULL',"#consumption");
        ajax_request_replace_div_content('national/get_facility_infor/'+county_data[0]+'/NULL/NULL/NULL',"#facilities");
        ajax_request_replace_div_content('national/order/NULL/'+county_data[0]+'/NULL/NULL/NULL',"#orders");
        ajax_request_replace_div_content('national/get_lead_infor/NULL/'+county_data[0]+'/NULL/NULL/NULL',"#lead_infor");
    }
            function dropdown(baseUrl,post,identifier){
            /*
             * ajax is used here to retrieve values from the server side and set them in dropdown list.
             * the 'baseUrl' is the target ajax url, 'post' contains the a POST varible with data and
             * 'identifier' is the id of the dropdown list to be populated by values from the server side
             */
            $.ajax({
              type: "POST",
              url: baseUrl,
              data: post,
              success: function(msg){
                    var values=msg.split("_")
                    var dropdown="<option value='NULL'>All</option>";
                    for (var i=0; i < values.length-1; i++) {
                        var id_value=values[i].split("*")
                        dropdown+="<option value="+id_value[0]+">";
                        dropdown+=id_value[1];
                        dropdown+="</option>";
                    };
                    $(identifier).html(dropdown);
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                   if(textStatus == 'timeout') {}
               }
            }).done(function( msg ) {
            });
        }
       function ajax_request_replace_div_content(function_url,div){
        var function_url =url+function_url;
        var loading_icon=url+"assets/img/Preloader_3.gif";
        $.ajax({
        type: "POST",
        url: function_url,
        beforeSend: function() {
        $(div).html("<img style='margin-top:20%;margin-left:50%;' src="+loading_icon+">");
        },
        success: function(msg) {
        $(div).html(msg);
        }
        });
        } 
        $(".excel_").click(function(e) {
        e.preventDefault();
        
        var county_id=$('#placeholder').val();
       // alert(county_id);
        var type=$(this).attr('id'); 
        
        var link='';
        
        if(type=='hcwtrained'){ 
        link='national/hcw/'+county_id+'/NULL/NULL/excel';
        }
        
        if(type=='rolledout'){
        link='national/facility_over_view/'+county_id+'/NULL/NULL/excel';
        }
        
       
        window.open(url+link,'_parent'); 
        });  

		</script>

	</body>

</html>

