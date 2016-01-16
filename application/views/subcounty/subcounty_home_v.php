<?php
$identifier = $this -> session -> userdata('user_indicator');
        switch ($identifier):
			case 'district':
				$for = 'subcounty';
			break;
			case 'county':
			$for = 'county';
			break;
			 endswitch;
?>

<div class="container-fluid" style="margin-top: 1%;">
      <div class="row row-offcanvas row-offcanvas-right" id="sidebar" >
      	<p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Click to view Side Menu</button>
          </p>
        <div class="col-sm-3 col-md-2 sidebar-offcanvas"  id="bar" role="navigation" style="margin-left:0.5%">
           <div class="panel-group " id="accordion" style="padding: 0;">
           	<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour" id="consumption"><span class="glyphicon glyphicon-file">
                            </span>Consumption</a>
                        </h4>
                    </div>
                </div>
               <!--  <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" id="expiries"><span class="glyphicon glyphicon-trash">
                            </span>Expiries</a>

                        </h4>
                    </div>
                </div> -->

        <div class="panel panel-default <?php echo $active_panel=='expiries'? 'active-panel': null; ?>">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseone" id="expiries" ><span class="glyphicon glyphicon-trash">
                </span>Expiries</a>
            </h4>
        </div>

        <div id="collapseone" class="panel-collapse collapse <?php echo $active_panel=='expiries'? 'in': null; ?>">
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td>
                            <a href="#collapseten" data-toggle="collapse" data-parent="#accordion" id="potential_expiries"><span class="glyphicon glyphicon-upload"></span>Potential Expiries</a>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
        
        </div>

                 <!--Orders-->
                <div class="panel panel-default <?php echo $active_panel=='orders'? 'active-panel': null; ?>">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseNine" id="orders"><span class="glyphicon glyphicon-list-alt">
                            </span>Orders</a>
                        </h4>
                    </div>
                    <?php
                        $identifier = $this -> session -> userdata('user_indicator');
                        
                        if ($identifier=='district') {
                        ?>
                   <!--  <div class="panel-body">
                        <table class="">
                            <tr>
                                <td>                                    
                                    <a href="<?php //echo base_url("reports/facility_transaction_data_other/KEMSA") ?>">Submit Facility Order</a>
                                </td>
                            </tr>

                        </table>

                    </div> -->
                    <?php }?>
                </div>

                <!--Program Reports-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" id="program_reports"><span class="glyphicon glyphicon-th-list">
                            </span>Program Reports</a>
                        </h4>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive" id="redistributions"><span class="glyphicon glyphicon-retweet">
                            </span>Redistributions</a>
                        </h4>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" id="stocking_levels"><span class="glyphicon glyphicon-sort-by-attributes">
                            </span>Stocking Levels</a>
                        </h4>
                    </div>
                </div>
                <!-- <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight" id="system_usage"><span class="glyphicon glyphicon-signal">
                            </span>System Usage</a>
                        </h4>
                    </div>
                    
                </div> -->
                <!--Stocking Levels-->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <!-- <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" id="system_usage"><span class="glyphicon glyphicon-signal"> -->
                            <a  href="<?php echo base_url().'reports/system_usage_temp'; ?>" id="system_usage"><span class="glyphicon glyphicon-signal">
                            </span>System Usage</a>
                        </h4></a>
                        </h4>
                    </div>
                     <?php
                        $identifier = $this -> session -> userdata('user_indicator');
                        // echo "This".$identifier;
                        if (($identifier=='district') || ($identifier=='county')) {
                        // if ($identifier=='district' || $identifier=='county') {
                        ?>
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <td>
                                    <!-- <a href="<?php echo base_url().'reports/potential_expiries' ?>">Potential Expiries</a> -->
                                    <a href="<?php echo base_url("facility_activation/facility_dash") ?>">Facility Activation</a>
                                </td>
                            </tr>

                        </table>

                    </div>
                    <?php }?>
                  <!--  <div id="collapseTwo" class="panel-collapse collapse <?php echo $active_panel=='stocking_levels'? 'in': null; ?>">
                         <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>  -->
                    
                    
                </div>
                <!--To be removed once the redesign is done
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="notifications"><span class="glyphicon glyphicon-bullhorn">
                            </span>Notifications</a>
                        </h4>
                    </div>
                </div>-->
                <!--<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix" id="orders"><span class="glyphicon glyphicon-list-alt">
                            </span>Orders</a>
                        </h4>
                    </div>
                </div>-->
                
                <!-- Needs to be worked on ASAP Commented out till queries are redesigned. -->
				
            </div>
        </div>
      </div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 " style="padding:0;border-radius: 0;margin-top: -2% ">
 <h1 class="page-header" style="margin-top: 2.4%;font-size: 1.6em;"></h1>
<div class="">
<div style="height:100%;width:100%;margin: 0 auto;" id="notification"></div>
</div>
</div>
    </div>
    <script>
    	/**
 *@author Kariuki & Mureithi
 */
  $(document).ready(function () {
  	//default 

  $('.page-header').html('Consumption');
  $('#consumption').parent().parent().parent().addClass('active-panel');
  ajax_request_replace_div_content('reports/generate_county_filter/',"#notification");
  // ajax_request_replace_div_content('reports/consumption_data_dashboard/NULL/NULL/NULL/NULL/NULL/NULL/NULL/1',"#notification");
 
 
  $('[data-toggle=offcanvas]').click(function () {
    $('.row-offcanvas').toggleClass('active');
  });
  
  $(window).resize(function() {
    if (($(window).width() < 768))
    {
        $( ".col-md-2,.col-md-10" ).css( "position", "" );
    };
});
//notifications function
$("#notifications").on('click', function(){
$('.page-header').html('Notifications');
active_panel(this);
ajax_request_replace_div_content('reports/notification_dashboard',"#notification");
});
//expiries function
$("#expiries").on('click', function(){
$('.page-header').html('Expiries');
active_panel(this);
ajax_request_replace_div_content('reports/expiries_dashboard',"#notification");
});

$("#potential_expiries").on('click', function(){
$('.page-header').html('Potential Expiries');
// active_panel(this);
// active_panel(this);
ajax_request_replace_div_content('reports/county_expiries',"#notification");
// ajax_request_replace_div_content('reports/potential_exp_sub_county_titus/',"#notification");
// ajax_request_replace_div_content('reports/potential_expiries_dashboard',"#notification");
});

//orders function
$("#orders").on('click', function(){
$('.page-header').html('Orders');
active_panel(this);
ajax_request_replace_div_content('reports/order_listing/'+'<?php echo $for;?>'+'/true',"#notification");
});
//stocking_levels function
$("#stocking_levels").on('click', function(){
ajax_request_replace_div_content('reports/stock_level_dashboard',"#notification");
active_panel(this);
$('.page-header').html('Stocking Levels');
});
//Consumption function
$("#consumption").on('click', function(){
active_panel(this);
$('.page-header').html('Consumption');
//consumption_data_dashboard($commodity_id = null, $district_id = null, $facility_code=null, $option = null,$from = null,$to = null, $graph_type=null,$tracer = null) {
        
ajax_request_replace_div_content('reports/consumption_data_dashboard/NULL/NULL/NULL/NULL/NULL/NULL/NULL/1',"#notification");
});
//Redistributiions Functions
$("#redistributions").on('click', function(){
active_panel(this);
$('.page-header').html('Commodity Redistribution');
ajax_request_replace_div_content('reports/county_donation',"#notification");
});
//Program Reports
$("#program_reports").on('click', function(){
active_panel(this);
$('.page-header').html('');
ajax_request_replace_div_content('divisional_reports/program_reports',"#notification");
});
//System Usage function
$("#system_usage").on('click', function(){
active_panel(this);
$('.page-header').html('System Usage');
ajax_request_replace_div_content('reports/facility_mapping',"#notification");
});
// $("#system_usage").on('click', function(){
//      window.location.href = "<?php echo base_url();?>reports/system_usage_temp" ;
//  });
});
</script>
