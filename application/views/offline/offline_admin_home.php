<style type="text/css">
	.panel-body,span:hover,.status_item:hover
	{ 
		cursor: pointer !important; 
	}
	.panel {
		border-radius: 0;
	}
	.panel-body {
		padding: 8px;
	}
	#addModal .modal-dialog {
		width: 54%;
	}
	.borderless{
		border-radius: 0px;	
	}
	.form-group{
		margin-bottom: 10px;
	}
</style>

 <script>
 function alertify_exec(user_id){
 	if (user_id != '') {
    	// message = "The Password for User ID: "+user_id+" <?php echo ucfirst($fname).' '.ucfirst($lname); ?> has been reset to : 123456";
    	message = "The password for <?php echo $username; ?> has been reset to : 123456";

    	alertify.set({ delay: 10000 });
    	alertify.success(message, null);
 	}else{
 		message = "No action has been taken";

    	alertify.set({ delay: 10000 });
    	alertify.success(message, null);
 	}
 	
 }
 </script>

<?php 
// $pwd_reset = 1;
	if (isset($pwd_reset) && $pwd_reset == 1) {
		// echo $reset_user_id;exit;
		echo "
		<script>alertify_exec(".$reset_user_id.");</script>
		";
	}
 ?>
<div class="container-fluid">
	<div class="page_content">
		<div class="container-fluid">
			
			<div class="row" style="padding:5%;">
					<div class="col-md-3" style="margin:10px 0;float:right;">
						<?php if ($available_update == 1) { ?>
						<a class="btn btn-success" href="<?php echo base_url().'git_updater/update_system'; ?>" style="width:100%;">Update Available</a>
						<?php } else{ ?>
						<a class="btn btn-success" disabled="disabled" href="<?php echo base_url().'update_system'; ?>" style="width:100%;">You are currently up to date</a>
					<?php } ?>
					</div>
				<div class="col-md-12" style="border: 1px solid #ddd;padding-top: 1%; " id="test">
					

					<table class="table table-hover table-bordered table-update">
						<thead>
							<th>Date of Commit</th>
							<th>Hash</th>
						</thead>
						<tbody>
							<?php foreach ($git_records as $records) { ?>
								<tr>
									<td><?php echo $records['update_time']; ?></td>
									<td><?php echo $records['hash_value']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
<!-- Modal add user -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="myform">
	<div class="modal-dialog editable" >
		<div class="modal-content">
			<div class="modal-header" style="">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center;line-height: 1">Add User</h4>
			</div>
			<div class="row" style="margin:auto" id="error_msg">
				<div class=" col-md-12">
					<div class="form-group">

					</div>
				</div>

			</div>
			<div class="modal-body" style="padding:0">
				<div class="row" style="margin:auto">
					<div class="col-md-12 ">
					<center>
						<form role="form">

							<fieldset class = "col-md-12">
							<center>
							<!--
								<legend style="font-size:1.5em">
									Add User
								</legend>
								-->

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">First Name</span>
									<input type="text" required="required" name="first_name" id="first_name" class="form-control " placeholder="Enter First Name" >
								</div>

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Last Name</span>
									<input type="text" name="last_name" required="required" id="last_name" class="form-control " placeholder="Last Name" >
								</div>

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Phone Number</span>
									<input type="telephone" name="telephone" required="required" id="telephone" class="form-control " placeholder="Enter Phone Number eg, 254" tabindex="5">
								</div>

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Email</span>
									<input type="email" name="email" id="email" required="required" class="form-control " placeholder="email@domain.com" tabindex="6">
								</div>

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">User Name</span>
									<input type="email" name="username" id="username" required="required" class="form-control " placeholder="email@domain.com" tabindex="5" readonly>
								</div>

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">User Type</span>
									<select class="form-control " id="user_type" name="user_type" required="required">
												<option value='NULL'>Select User type</option>
												<?php
												foreach ($user_types as $user_types) :
													$id = $user_types ['id'];
													$type_name = $user_types ['level'];
													echo "<option value='$id'>$type_name</option>";
												endforeach;
												?>
									</select>
								</div>
									<?php

									$identifier = $this -> session -> userdata('user_indicator');
									
									if ($identifier=='district') {
									?>
									<div class="input-group form-group u_mgt">
										<span class="input-group-addon sponsor">Facility Name</span>
										<select class="form-control " id="facility_id" required="required">
												<option value='NULL'>Select Facility</option>

												<?php
												foreach ($facilities as $facility) :
													$id = $facility ['facility_code'];
													$facility_name = $facility ['facility_name'];
													echo "<option value='$id'>$facility_name</option>";
												endforeach;
												?>
											</select>
									</div>


									<?php }elseif ($identifier=='county') { ?>
									<div class="input-group form-group u_mgt">
										<span class="input-group-addon sponsor">Subcounty Name</span>
										<select class="form-control " id="district_name" required="required">
											<option value=''>Select Sub-County</option>

											<?php
											foreach ($district_data as $district_) :
												$district_id = $district_ ['id'];
												$district_name = $district_ ['district'];
												echo "<option value='$district_id'>$district_name</option>";
											endforeach;
											?>
										</select>
									</div>

									<div class="input-group form-group u_mgt">
										<span class="input-group-addon sponsor">Facility Name</span>
										<select class="form-control " id="facility_id" required="required">
												<option value="">Select Facility</option>
												
										</select>

									</div>

								<?php }/*elseif ($identifier=='facility_admin') {
									//code if facility admin
									
								?>
								<div class="input-group form-group u_mgt">
										<span class="input-group-addon sponsor">User Type</span>
										<select class="form-control " id="user_type" name="user_type" required="required">
													<option value=''>Select User type</option>
													<?php
													foreach ($user_types as $user_type) :
														$display_id = $user_type ['id'];
														$name = $user_type ['level'];
														echo "<option value='$display_id'>$name</option>";
													endforeach;
													?>
										</select>
								</div>
								<?php }*/?>
								<div class="row" style="margin:auto" id="processing">
									<div class=" col-md-12">
										<div class="form-group">
										</div>
									</div>
								</div>
								</center>

							</fieldset>

						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default borderless" data-dismiss="modal">
					Close
				</button>
				
				<button class="btn btn-primary borderless" id="create_new">
					Save changes
				</button>
			</div>
		</div>
	</div>
</div><!-- end Modal new user -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%">
		<div class="modal-content editable">
			<div class="modal-header" style="">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel" style="text-align: center;line-height: 1">Edit User</h4>
			</div>
			<div class="modal-body" style="">
				<div id="contents">
				<center>
					<form role="form">

							<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">First Name</span>
									<input type="text" required="required" name="fname_edit" id="fname_edit" class="form-control " placeholder="First Name" >
							</div>

							<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Last Name</span>
										<input type="text" name="lname_edit" required="required" id="lname_edit" class="form-control " placeholder="Last Name" >
							</div>

							<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Phone No</span>
										<input type="telephone" disabled="disabled" name="telephone_edit" required="required" id="telephone_edit" class="form-control " placeholder="telephone eg, 254" tabindex="5">
							</div>

							<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Email</span>
										<input type="email" data-id="" name="email_edit" id="email_edit" required="required" class="form-control " placeholder="email@domain.com" tabindex="6">
							</div>

							<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">User Name</span>
										<input type="email" name="username_edit" id="username_edit" required="required" class="form-control " placeholder="email@domain.com" tabindex="5" readonly>
							</div>

							<div class="col-md-6">
									<div class="err" style="padding: 6px;">
										
									</div>
							</div>
						
						<!-- <h4>Other details</h4> -->
									<?php

									$identifier = $this -> session -> userdata('user_indicator');
									if ($identifier=='district') {
									?>

									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Facility Name</span>

									<select class="form-control " id="facility_id_edit_district" required="required">
												<option value=''>Select Facility</option>

												<?php
												foreach ($facilities as $facility) :
													$id = $facility ['facility_code'];
													$facility_name = $facility ['facility_name'] ;
													echo "<option value='$id'>$facility_name</option>";
												endforeach;
												?>
											</select>

									</div>

									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">User Type</span>
									<select class="form-control " id="user_type_edit_district" name="user_type_edit_district" required="required">
													
													
									</select>
									</div>

									<div class="input-group form-group u_mgt">
												<input type="hidden" name="district_name_edit" class="" id="district_name_edit" >
									
									</div>

									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Activation Status</span>
									<div class="col-md-6">
									<div class="onoffswitch">
									    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
									    <label class="onoffswitch-label" for="myonoffswitch">
									        <div value="1" class="onoffswitch-inner"></div>
									        <div class="onoffswitch-switch"></div>
									    </label>
									</div>
									</div>
									</div>

									<?php }elseif ($identifier=='county') { ?>

									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">User Type</span>
										<select class="form-control " id="user_type_edit_district" name="user_type_edit_district" required="required">
													
										</select>
									</div>

									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">District Name</span>
									<select class="form-control " id="district_name_edit" required="required">
												<option value=''>Select Sub-County</option>
												<?php
												foreach ($district_data as $district) :
													$d_id = $district ['id'];
													$d_name = $district ['district'];
													echo "<option value='$d_id'>$d_name</option>";
												endforeach;
												?>
											</select>
									</div>

									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Facility Name</span>
										<select class="form-control " id="facility_id_edit" required="required">
													
										</select>		
									</div>


									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Activation Status</span>
									<div class="col-md-6">
									<div class="onoffswitch">
									    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" >
									    <label class="onoffswitch-label" for="myonoffswitch">
									        <div  class="onoffswitch-inner"></div>
									        <div  class="onoffswitch-switch"></div>
									    </label>
									</div>
									</div>
									</div>

								<?php }elseif ($identifier=='facility_admin') {
									//code if facility admin
									
								?>

								<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">User Type</span>
												<select class="form-control " id="user_type_edit_district" name="user_type_edit_district" required="required">
													<option value=''>Select User type</option>
													<?php
													foreach ($user_types as $user) :
														$id = $user['id'];
														$type_name = $user['level'];
														echo "<option value='$id'>$type_name</option>";
													endforeach;
													?>
												</select>
									</div>


									<div class="input-group form-group u_mgt">
									<span class="input-group-addon sponsor">Status</span>
									</div>
										<div class="col-md-6">
										<div class="onoffswitch">
									    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" >
									    <label class="onoffswitch-label" for="myonoffswitch">
									        <div  class="onoffswitch-inner"></div>
									        <div  class="onoffswitch-switch"></div>
									    </label>
										</div>
										</div>
								<?php }?>


							</form>
							</center>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default borderless" data-dismiss="modal">
					Close
				</button>
				<button type="button" class="btn btn-primary edit_user borderless">
					Save changes
				</button>
			</div>
		</div>
	</div>
</div><!-- end Modal edit user -->
<script>
      $(document).ready(function () {
      	$("#create_new,.edit_user").attr("disabled", 'disabled');
      	
      	//activates the edit button on click
      	$(".editable").on('click',function() {
			$(".edit_user").attr("disabled", false);
			$("#create_new").attr("disabled", false);
		});
      	
      	$('#myModal').on('hidden.bs.modal', function () {
  			$("#datatable,.modal-content").hide().fadeIn('fast');
		 	location.reload();
		});
		
		$('.dataTables_filter label input').addClass('form-control');
		$('.dataTables_length label select').addClass('form-control');
		
		$('#datatable').dataTable( {
			"sDom": "T lfrtip",
	       	"sScrollY": "320px",   
            "sPaginationType": "bootstrap",
            "oLanguage": {"sLengthMenu": "_MENU_ Records per page","sInfo": "Showing _START_ to _END_ of _TOTAL_ records",},
            "oTableTools": {"aButtons": ["copy","print",{"sExtends":"collection","sButtonText": 'Save',"aButtons":[ "csv", "xls", "pdf" ]}],"sSwfPath": "<?php echo base_url(); ?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"}
	    
	  	} ); 
	  
	  	$('div.dataTables_filter input').addClass('form-control search');
	  	$('div.dataTables_length select').addClass('form-control');
		

		$(".dataTables_paginate").click(function(e){
			initialize_checkboxes();
		});

		//populate facilities to drop down depending on district selected
		$("#district_name").change(function() {
			var option_value=$(this).val();
    		if(option_value=='NULL'){
    			$("#facility_name").hide('slow'); 
    		}else{
				var drop_down='';
 				var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json/"+$("#district_name").val();
 				$.getJSON( hcmp_facility_api ,function( json ) {
 					$("#facility_id").html('<option value="NULL" selected="selected">Select Facility</option>');
      				$.each(json, function( key, val ) {
      					drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>"; 
      				});
      				$("#facility_id").append(drop_down);
    			});
    			$("#facility_id").show('slow');   
    		}
    	}); //end of district name change funtion
  	
  	$("#district_name_edit").change(function() {
  		var option_value=$(this).val();
    	if(option_value=='NULL'){
    		$("#facility_name_edit").hide('slow'); 
    	}else{
    		var drop_down='';
 			var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json/"+$("#district_name_edit").val();
  			$.getJSON( hcmp_facility_api ,function( json ) {
  				$("#facility_id_edit").html('<option value="NULL" selected="selected">Select Facility</option>');
     		 	$.each(json, function( key, val ) {
     		 		drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>"; 
      			});
      			$("#facility_id_edit").append(drop_down);
    		});
    		$("#facility_id_edit").show('slow');   
    	}
    }); //end of district name edit change function
    
    //handle edits
	$("#test").on('click','.edit',function() {
		//capture relevant data
		var email = $(this).closest('tr').find('.email').html();
		var phone = $(this).closest('tr').find('.phone').html();
		var district = $(this).closest('tr').find('.district').html();
		var fname = $(this).closest('tr').find('.fname').html();
		var lname = $(this).closest('tr').find('.lname').html();
		
		//populate dropdown on click and selected current 
		var drop_down='';
		var facility_id=$(this).closest('tr').find('.facility_name').attr('data-attr');
 		var hcmp_facility_api = "<?php echo base_url(); ?>reports/get_facility_json_data/"+$(this).closest('tr').find('.district').attr('data-attr');
  		
  		$.getJSON( hcmp_facility_api ,function( json ) {
  			$("#facility_id_edit").html('<option value="NULL" selected="selected">Select Facility</option>');
      		$.each(json, function( key, val ) {
        		drop_down +="<option value='"+json[key]["facility_code"]+"'>"+json[key]["facility_name"]+"</option>"; 
      		});
      	
	      	$("#facility_id_edit").append(drop_down);
	      	$('#facility_id_edit').val(facility_id)
    	});
   		
   		//fill inputs with relevant data
		$('#email_edit').val(email)
		$('#email_edit').attr('data-id',$(this).closest('tr').find('.email').attr('data-attr'))
		$('#telephone_edit').val(phone)
		$('#fname_edit').val(fname)
		$('#lname_edit').val(lname)
		$('#username_edit').val(email)
		$('#user_type_edit').val($(this).closest('tr').find('.level').attr('data-attr'))
		$('#district_name_edit').val($(this).closest('tr').find('.district').attr('data-attr'))
		
		var drop_down_user='';
		var type_id=$(this).closest('tr').find('.level').attr('data-attr');
 		var get_type_json = "<?php echo base_url(); ?>user/get_user_type_json/";
 
  		$.getJSON( get_type_json ,function( json ) {
  			$("#user_type_edit_district").html('<option value="NULL" >Select User Type</option>');
      		
      		$.each(json, function( key, val ) {
      			drop_down_user +="<option value='"+json[key]["id"]+"'>"+json[key]["level"]+"</option>";
      	 
      		});
      		$("#user_type_edit_district").append(drop_down_user);
      		$('#user_type_edit_district').val(type_id)
    	});
   	
   		if($(this).closest('tr').find('.status_item').attr('data-attr')=="false"){
   			$('.onoffswitch-checkbox').prop('checked', false) 	
		}else if($(this).closest('tr').find('.status_item').attr('data-attr')=="true"){
			$('.onoffswitch-checkbox').prop('checked', true) 
		}

		if($(this).closest('tr').find('.facility_name').attr('data-attr')==""){
			$("#facility_id_edit").attr("disabled", "disabled"); 
		}

		$('#facility_id_edit_district').val(facility_id)

  });
  
  //make sure email==username  for edits
  $('#email_edit').keyup(function() {
  	var email = $('#email_edit').val();
   	$('#username_edit').val(email);
   	$('#username').val(email);
   	
   	$.ajax({
      type: "POST",
      dataType: "json",
      url: "<?php echo base_url()."user/check_user_json";?>", //Relative or absolute path to response.php file
      data:{ 'email': $('#email_edit').val()},
      success: function(data) {
        if(data.response=='false'){
        	$('.err').html(data.msg);
			$( '.err' ).addClass( "alert-danger alert-dismissable" );
			$(".edit_user,#create_new").attr("disabled", "disabled");
		}else if(data.response=='true'){
			$(".err").empty();
			$(".err").removeClass("alert-danger alert-dismissable");
			$( '.err' ).addClass( "alert-success alert-dismissable" );
			$(".edit_user,#create_new").attr("disabled", false);
			$('.err').html(data.msg);
		}
      }
    });
    return false;
	})

  $('#email').bind('input change paste keyup mouseup',function() {
  	// var email = $('#email').val();   	   	
   	
   	$.ajax({
      type: "POST",
      dataType: "json",
      url: "<?php echo base_url()."user/check_user_json";?>", //Relative or absolute path to response.php file
      data:{ 'email': $('#email').val()},
      beforeSend: function(){
        	$('#processing').html('Checking Email...');

      },
      success: function(data) {
      	console.log(data);
        if(data.response=='false'){
        	$('#processing').html(data.msg);
			$( '#processing' ).addClass( "alert-danger alert-dismissable" );
			$("#create_new").attr("disabled", "disabled");
		}else if(data.response=='true'){
			$("#processingr").val('');
			$("#processing").removeClass("alert-danger alert-dismissable");
			$('#processing' ).addClass( "alert-success alert-dismissable" );
			$("#create_new").attr("disabled", false);
			$('#processing').html(data.msg);
		}
      }
    });
    return false;
	})

    
   //Handle adding new users 
   $("#add_new").on('click',function() {

  var drop_down_user='';
var type_id=$(this).closest('tr').find('.level').attr('data-attr');
 var get_type_json = "<?php echo base_url(); ?>user/get_user_type_json/";
 
  $.getJSON( get_type_json ,function( json ) {
     $("#user_type").html('<option value="NULL" selected>Select User Type</option>');
      $.each(json, function( key, val ) {
      	
      	drop_down_user +="<option value='"+json[key]["id"]+"'>"+json[key]["level"]+"</option>";
      	 
      });
      
      $("#user_type").append(drop_down_user);
      

    })
    });
    
     //make sure email==username

$('#email').keyup(function() {

  var email = $('#email').val()

   $('#username').val(email)
   
   $.ajax({
      type: "POST",
      dataType: "json",
      url: "<?php echo base_url()."user/check_user_json";?>", //Relative or absolute path to response.php file
      data:{ 'email': $('#email').val()},
      success: function(data) {
        if(data.response=='false'){
						
						 $('#err').html(data.msg);
							$( '#err' ).addClass( "alert-danger alert-dismissable" );
							$(".edit_user,#create_new").attr("disabled", "disabled");
							}else if(data.response=='true'){
								
								$("#err").empty();
								$("#err").removeClass("alert-danger alert-dismissable");
								$( '#err' ).addClass( "alert-success alert-dismissable" );
								$(".edit_user,#create_new").attr("disabled", false);
								$('#err').html(data.msg);
								
								
							}
      }
    });
    return false;
  });

    // $("#reset_pwd").on('click',function() {//karsan
   $(".dataTable").on('click','.reset_pwd',function(event) {//titus
    // $('.reset_pwd').click(function(){    	
    	var user_id = $(this).attr('data-attr');
    	var user_email = $(this).attr('data-name');
    	var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";
    	var message_success = "The Password for User: "+user_email+" has been reset to : 123456";
    	var message_fail = "The Password for User: "+user_email+" has NOT reset";
    	var message_cancel = "No action has been taken";    	
    	var my_url = "<?php echo base_url()."user/reset_pass_to_default";?>"+'/'+user_id;
    	$.ajax({
    		type:"POST",
    		data:{
    			'user_id' : user_id
    		},
    		url:my_url,
    		beforeSend: function() {
            var message = confirm("This will reset the selected user's credentials to the default which is 123456. Are you sure you want to proceed?");
		        if (message){
		            return true;
		        } else {		            
		            alertify.set({ delay: 10000 });
            		alertify.success(message_cancel, null);
            		return false;
		        }
           
          },
          success: function(msg){          		
	 			if(msg==true){
	 			 	alertify.set({ delay: 10000 });
            		alertify.success(message_success, null);
	            }else{
	            	alertify.set({ delay: 10000 });
            		alertify.success(message_fail, null);
	            }
 			}

    	});
    });
    
    
   
$("#create_new").click(function() {

      var first_name = $('#first_name').val()
      var last_name = $('#last_name').val()
      var telephone = $('#telephone').val()
      var email = $('#email').val()
      var username = $('#username').val()
      var facility_id = $('#facility_id').val()
      var district_name = $('#district_name').val()
      var user_type = $('#user_type').val()

       if(first_name==""||last_name==""||telephone==""||email==""||user_type=="NULL"||district_name=="NULL"){
						alert('Please make sure you have selected all relevant fields.');
							return;
							}
      
      var div="#processing";
      var url = "<?php echo base_url()."user/addnew_user";?>";
      ajax_post_process (url,div);
           
    });

   function ajax_post_process (url,div){
    var url =url;

     //alert(url);
    // return;
     var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";
     $.ajax({
          type: "POST",
          data:{ 'first_name': $('#first_name').val(),'last_name': $('#last_name').val(),
          'telephone': $('#telephone').val(),'email': $('#email').val(),
          'username': $('#username').val(),'facility_id': $('#facility_id').val(),
          'district_name': $('#district_name').val(),'user_type': $('#user_type').val()},
          url: url,
          beforeSend: function() {
           
            var message = confirm("Are you sure you want to proceed?");
        if (message){
            $('.modal-body').html("<img style='margin:30% 0 20% 42%;' src="+loading_icon+">");
        } else {
            return false;
        }
           
          },
          success: function(msg) {
          	
          	//$('.modal-body').html(msg);return;
         
        setTimeout(function () {
          	$('.modal-body').html("<div class='bg-warning' style='height:30px'>"+
							"<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>"+
							"<h3>Success!!! A new user was added to the system. Please Close to continue</h3></div>")
							
			$('.modal-footer').html("<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>")
				
        }, 4000);
            
                  
          }
        }); 
        
}
	//WhenEditing Users
	$(".edit_user").click(function() {
		var div="#process";
      	var url = "<?php echo base_url()."user/edit_user";?>";
      	ajax_post (url,div);
    });

   function ajax_post (url,div){
   	var url =url;
	var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";
 	$.ajax({
 		type: "POST",
      	data:{ 'fname_edit': $('#fname_edit').val(),'lname_edit': $('#lname_edit').val(),
          'telephone_edit': $('#telephone_edit').val(),'email_edit': $('#email_edit').val(),
          'username_edit': $('#username_edit').val(),'facility_id_edit_district': $('#facility_id_edit_district').val(),
          'user_type_edit_district': $('#user_type_edit_district').val(),'district_name_edit': $('#district_name_edit').val(),
			'facility_id_edit': $('#facility_id_edit').val(),'status': $('.onoffswitch-checkbox').prop('checked'),'user_id':$('#email_edit').attr('data-id')},
      	url: url,
      	beforeSend: function() {
      		//$(div).html("");
            var answer = confirm("Are you sure you want to proceed?");
        	if (answer){
        		$('.modal-body').html("<img style='margin:30% 0 20% 42%;' src="+loading_icon+">");
        	} else {
            	return false;
        	}
       },success: function(msg) {
       	setTimeout(function () {
       		$('.modal-body').html("<div class='bg-warning' style='height:30px'>"+
       		"<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>"+
			"<h3>Success Your records were Edited. Please Close to continue</h3></div>")
			$('.modal-footer').html("<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>")
        }, 4000);
        
              
          }
           
        }); 
}
			
			
			oTable = $('#datatable').dataTable();
			
			$('#active').click(function () {
				
				oTable.fnFilter('active');
			})
			
			$('#inactive').click(function () {
				
				oTable.fnFilter('deactivated');
			})
	
	function initialize_checkboxes(){
	$('input[name="status-checkbox"]').change(function(e){
		// e.prevenDefault();
      value = $(this).attr('checked');//member id
      user_id = $(this).attr("data-attr");//member id
      if ($(this).prop('checked') == false){
        // alert($(this).prop("checked"));
        // console.log(user_id);
        change_status(user_id,0,"unchecked");
        // $('input[name="status-checkbox"]').prop('checked', false);
      
      } else{
        // alert("checked");
        // console.log(user_id);
        // alert($(this).prop("checked"));
        change_status(user_id,1,"checked");
        // $('input[name="status-checkbox"]').prop('checked', true);
      };
      
      // console.log(value);
   });	
	}//end of initialize checkboxes
			
	$('input[name="status-checkbox"]').change(function(e){
		// e.prevenDefault();
      value = $(this).attr('checked');//member id
      user_id = $(this).attr("data-attr");//member id
      if ($(this).prop('checked') == false){
        // alert($(this).prop("checked"));
        // console.log(user_id);
        change_status(user_id,0,"unchecked");
        // $('input[name="status-checkbox"]').prop('checked', false);
      
      } else{
        // alert("checked");
        // console.log(user_id);
        // alert($(this).prop("checked"));
        change_status(user_id,1,"checked");
        // $('input[name="status-checkbox"]').prop('checked', true);
      };
      
      // console.log(value);
   });	

    function change_status(user_id,stati,checked){//seth
      // alert(checked);return;
      message = "";
      if (stati == 0) {
        message_after = "User has been Deactivated";
      }else{
        message_after = "User has been Activated";

      };
      var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";
      // alert(stati);

      $.ajax({
          type:"POST",
          data:{
            'user_id': user_id,
            'status': stati
        },

          url:"<?php echo base_url()."admin/change_status";?>",

          beforeSend: function() {
            //$(div).html("");
            // alert($('#email_recieve_edit').prop('checked'));return;
            var answer = confirm("Are you sure you want to proceed?");
            if (answer){
                $('.modal-body').html("<img style='margin:30% 0 20% 42%;' src="+loading_icon+">");
            } else {
            	message_denial = "No action has been taken";
            	alertify.set({ delay: 10000 });
             	alertify.success(message_denial, null);
            	if (checked == "checked") {
            		// alert("im checked");
            		$('input[data-attr="'+user_id+'"]').prop('checked' ,false);
            	}else{
            		// alert("im unchecked");
            		$('input[data-attr="'+user_id+'"]').prop('checked' ,true);


            	};
                return false;
            }},
            success: function(msg){
              alertify.set({ delay: 10000 });
              alertify.success(message_after, null);
            }

        });
    }//end of change status function
			
			});
    </script>