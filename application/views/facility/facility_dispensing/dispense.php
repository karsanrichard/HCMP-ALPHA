<?php //echo "<pre>";print_r($sp_commodities);exit; ?>
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

	#search_results table tr.active{
		background-color: #e3e3e3;
	}
</style>

<div class="container-fluid">
	<div class="page_content">

		<div class="container-fluid">

			<div class="row">

				<div class="col-md-12" style="padding-left: 1%; right:0; float:right; margin-bottom:5px;margin-left:1%;">
					<input type="text" class="form-control" id="patient_number" name="patient_number" placeholder="Enter the Patient Number or First Name or Last Name" style="width:29%;float:left;" />
					<button class="btn btn-primary" id="find_patient">
						<span class="glyphicon glyphicon-search"></span>Find
					</button>
				</div>
				<div class="col-md-12">
					
				<div class="clearfix" style="border:1px solid #ccc;padding:1px;">
					<div class="col-md-4 search_container" style="padding:0 1%;height:100%;background-color:#428bca;border:1px ridge #e3e3e3;float:left;color:#ffffff">
						<h4>Search Results</h4>
						<!-- <textarea id="search_results" disabled="disabled" style="width:96%;height:80%;color:black;"></textarea> -->
						<table class="table table-bordered" id="search_results">
							<thead>
								<th>Name</th>
								<th>Patient No.</th>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div class="col-md-8">
					 <div class="col-md-5">
					 	<h4>Patient Details</h4>
					 	<label>Patient Number:</label> <input type="text" id="p_no" disabled="disabled" style="width:80%;float-left;" class="form-control"><br/>
						<label>Name:</label> <input type="text" id="name" disabled="disabled" style="width:80%" class="form-control"><br/>
						<label>DOB:</label> <input type="text" id="dob" disabled="disabled" style="width:80%" class="form-control"><br/>
						<label>Gender:</label> <input type="text" id="gender" disabled="disabled" style="width:80%" class="form-control"><br/>
					 </div>

					<div class="col-md-7" style="">
					<h4>Prescription</h4>
						<table class="table table-bordered">
							<thead>
								<th>Drugs</th>
								<th>Total Available Units</th>
								<th>Issued Units</th>
							</thead>
							<tbody>
								<tr>
									<td>
									<select class="form-control input-small drug_select">
										<option selected="selected" value="0">Select Commodity</option>
										 <?php 
										 foreach ($sp_commodities as $stock) :						
											$commodity_name=$stock['commodity_name'];
											$commodity_id=$stock['commodity_id'];
											$current_balance=$stock['current_balance'];
											// $source_name=$stock['source_name'];
											// $total_commodity_units=$stock['total_commodity_units'];
											// $commodity_balance=$stock['commodity_balance'];		
										// echo "<option special_data='$commodity_id^$unit^$source_name^$total_commodity_units^$commodity_balance' value='$commodity_id'>$commodity_name. ($source_name)</option>";		
										echo "<option obesity='$commodity_id^$current_balance' value='$commodity_id' data-name = '$commodity_name'>$commodity_name</option>";		
										endforeach;

										 /*foreach ($sp_commodities as $stock) {
										 	echo "<option value=".$stock['commodity_id'].">".$stock['commodity_name']."</option>";
										 }*/ 
										 ?>
									</select>

									</td>
									<td><input type="number" class="form-control total_available" disabled="disabled"></td>
									<td><input type="number" class="form-control quantity_issued"></td>

 								
								</tr>
								<tr>
									<td colspan="3"><button class="btn btn-success prescribe" id="prescribe" style="float:right">Prescribe</button></td>
								</tr>
							</tbody>
						</table>

						<table class="table table-bordered prescribed_cart" id="prescribed_cart">
							<thead>
								<th>Commodity</th>
								<th>Units Prescribed</th>
								<th>Action</th>
							</thead>
							<tbody>
								
							</tbody>
						</table>
						<div class="col-md-12">
							<center><button class="btn btn-success" id="dispense" style="float:right;width:150px">Dispense</button></center>
						</div>
						<?php $att=array("name"=>'dispense_form','id'=>'dispense_form'); echo form_open('dispensing/dispense_commodities',$att); ?>
						<?php form_close(); ?>	
					</div>
					

					</div>
				</div>
				<!-- <div class="col-md-12" style="margin:5px 0">
					<div class="col-md-6" style="height:250px">					
						<h4>Diagnosis</h4>
						<textarea style="width:100%;height:90%;overflow:scroll;"></textarea>
					</div>
					<div class="col-md-6" style="height:250px">
						<h4>Prescription</h4>
						<textarea style="width:100%;height:90%;overflow:scroll;"></textarea>
					</div>
				</div> -->
					<!-- <div class="col-md-12" style="margin:20px 0;padding:0 28px 0 0">
					<button class="btn btn-success" id="dispense" style="float:right">
						Dispense
					</button>
					</div> -->
			</div>
			
			</div>

		</div>
	</div>
</div>

<script>
$(document).ready(function () {
 // $('#search_results').datatable();
var global_details = null;
var counter = 0;
var search_table = $('#search_results,#prescribed_cart').dataTable( {
	   "sDom": "T lfrtip",
	     "sScrollY": "150px",
	     "sScrollX": "100%",
                    "sPaginationType": "bootstrap",
                    "oLanguage": {
                        "sLengthMenu": "_MENU_ Records per page",
                        "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
                    },
			      "oTableTools": {
                 "aButtons": [
				"copy",
				"print",
				{
					"sExtends":    "collection",
					"sButtonText": 'Save',
					"aButtons":    [ "csv", "xls", "pdf" ]
				}
			],

			"sSwfPath": "<?php echo base_url(); ?>assets/datatable/media/swf/copy_csv_xls_pdf.swf"
		}
	} );

$(".search_container").on("click", "table tr", function() {
	var patient_number = $(this).attr('data-href');	
	var row_id = $(this).find('.form_patient_row').val();	
	var p_no = global_details[row_id][0];
	var name = global_details[row_id][1];
	var gender = global_details[row_id][2];
	var dob = global_details[row_id][3];
	// var names_and_no = global_details[4];
	// var patient_id = global_details[5];
	$('#name').val(name);
	$('#dob').val(dob);
	$('#gender').val(gender);
	$('#p_no').val(p_no);
	$('#p_no').attr('data-patient-id', patient_id);
	$(this).addClass('active');
} );
// $('#search_results tbody').on( 'click', 'tr', function () {
   // alert('click');
// });
$("#find_patient").click(function() {
	  clear_dets();
	  var patient_number = $('#patient_number').val();
	  var url = "<?php echo base_url()."dispensing/get_patient_detail";?>";      
	  var loading_icon="<?php echo base_url().'assets/img/Preloader_4.gif' ?>";      
   if(patient_number==""){
		alert('Please make sure you have filled in all required  fields.');
		return;
	}
	$('#search_results').html("	");
	$.ajax({
        type: "POST",
        url: url,
        data:{'patient_number': patient_number},
        dataType: "html",
        // async: !1,
        // beforeSend: function() {
	       //  var message = confirm("Are you sure you want to proceed?");
	       //  if (message){
	       //      $('.modal-body').html("<img style='margin:30% 0 20% 42%;' src="+loading_icon+">");
	       //  } else {
	       //      return false;
	       //  }           
        //   },

        success: function(msg) {
        	console.log(msg);
        	if (msg == 0) {
	       		$('#search_results').html("<tr><td colspan = 2> There are no records partaining to your search </td></tr>");

	       		var patient_details = JSON.parse(msg);
	       		// console.log(patient_details);
	       		var p_no = "";
	       		var name = "";
	       		var gender = "";
	       		// var dob = "";
	       		var names_and_no = "";
	       		var patient_id = "";

	       		$('#name').val(name);
	       		$('#dob').val(dob);
	       		$('#gender').val(gender);
	       		$('#p_no').val(p_no);
	       		$('#p_no').attr('data-patient-id', patient_id);
	       		$( ".form_patient_id" ).remove();
        	}else{
	       		var patient_details = JSON.parse(msg);
	       		global_details = JSON.parse(msg);

	       		for (var i = 0; i < patient_details.length; i++) {
	       			var p_no = patient_details[i][0];
		       		var name = patient_details[i][1];
		       		var gender = patient_details[i][2];
		       		var dob = patient_details[i][3];
		       		var names_and_no = patient_details[4];
		       		var patient_id = patient_details[5];
		       		// $('#name').val(name);
		       		// $('#dob').val(dob);
		       		// $('#gender').val(gender);
		       		// $('#p_no').val(p_no);
		       		// $('#p_no').attr('data-patient-id', patient_id);
		       		// $('#search_results').val(names_and_no);
		       		$('#search_results').append("<tr class='clickable-row' data-href='"+p_no+"'><td>"+name+"</td><td>"+p_no+"</td><input type=\"hidden\" value="+i+" class=\"form_patient_row\"></tr>");
		       		// $('#search_results').append("<tr class='clickable-row' data-href='"+p_no+"'><td>"+name+"</td><td>"+p_no+"</td></tr>");
			    	$('#dispense_form').append("<input type=\"hidden\" value="+patient_id+" name=\"form_patient_id\" class=\"form_patient_id\">");
	       			// Things[i]
	       		};
	      //  		var p_no = patient_details[0];
	      //  		var name = patient_details[1];
	      //  		var gender = patient_details[2];
	      //  		var dob = patient_details[3];
	      //  		var names_and_no = patient_details[4];
	      //  		var patient_id = patient_details[5];
	      //  		// alert(patient_id);return;
	      //  		$('#name').val(name);
	      //  		$('#dob').val(dob);
	      //  		$('#gender').val(gender);
	      //  		$('#p_no').val(p_no);
	      //  		$('#p_no').attr('data-patient-id', patient_id);
	      //  		// $('#search_results').val(names_and_no);
	      //  		$('#search_results').html("<tr><td>"+name+"</td><td>"+p_no+"</td></tr>");
		    	// $('#dispense_form').append("<input type=\"hidden\" value="+patient_id+" name=\"form_patient_id\" class=\"form_patient_id\">");

	       		 // var search_table = $('#search_results').DataTable().ajax.reload();
	       			// search_table.ajax.reload();
        	};

      	},
        error: function() {
            alert('Error occured');
        }
    });
});

$("#finfd_patient tr").click(function(){
	alert('Click');
});
$(".drug_select").on('change',function(){
	// alert("i work");
      		var row_id=$(this).closest("tr").index();	
      		var locator=$('option:selected', this);
			var data =$('option:selected', this).attr('obesity'); 
	       	var data_array=data.split("^");	 
	           	// alert(data_array);
	        locator.closest("tr").find(".total_available").val(data_array[1]);

	     	/*
	     	locator.closest("tr").find(".supplier_name").val(data_array[2]);
	     	locator.closest("tr").find(".commodity_id").val(data_array[0]);
	     	locator.closest("tr").find(".available_stock").val("");
	     	locator.closest("tr").find(".total_units").val(data_array[3]);
	     	locator.closest("tr").find(".expiry_date").val("");
	     	locator.closest("tr").find(".quantity_issued").val("0");
	     	locator.closest("tr").find(".clone_datepicker_normal_limit_today").val("");	 
	    	*/
});

$(".quantity_issued").on('keyup',function (){
        	var available=parseInt($(this).closest("tr").find(".total_available").val());
        	var input=parseInt($(this).closest("tr").find(".quantity_issued").val());

        	if (input > available) {
        		alert("Kindly input an amount less or equal to your available unit balance");
        		$(".quantity_issued").val("0");
        	};

});

$(".prescribe").click(function(){
	var quantity_issued = $(".quantity_issued").val();
	if (quantity_issued <= 0) {
		alert("Kindly indicate amount of issue")
	}else{
		counter = counter + 1;
		var drug_select = $(".drug_select").val();
		var drug_name = $(".drug_select").find(':selected').data("name");
		var total_available = $(".total_available").val();
		// alert(drug_name);return;
		// alert(drug_select + total_available + quantity_issued);
		if (counter == 1) {
	    $('#prescribed_cart').html("<tr><td>"+drug_name+"</td><td><input type=\"number\" value="+quantity_issued+" class=\"form-control input-small prescribed_units\" data-available = "+total_available+" disabled></td><td><button class=\"btn btn-danger\">Remove</button></td></tr>");
	    $('#dispense_form').append("<input type=\"hidden\" value="+quantity_issued+" name=\"quantity["+counter+"]\"><input type=\"hidden\" value="+drug_select+" name=\"id["+counter+"]\">");
		}else{
	    $('#prescribed_cart').append("<tr><td>"+drug_name+"</td><td><input type=\"number\" value="+quantity_issued+" class=\"form-control input-small prescribed_units\" data-available = "+total_available+" disabled></td><td><button class=\"btn btn-danger\">Remove</button></td></tr>");
	    $('#dispense_form').append("<input type=\"hidden\" value="+quantity_issued+" name=\"quantity["+counter+"]\"><input type=\"hidden\" value="+drug_select+" name=\"id["+counter+"]\">");
		
		};
		};

		
});

function clear_dets(){
	var p_no = "";
	var name = "";
	var gender = "";
	var dob = "";
	var names_and_no = "";
	var patient_id = "";

	$('#name').val(name);
	$('#dob').val(dob);
	$('#gender').val(gender);
	$('#p_no').val(p_no);
	$('#p_no').attr('data-patient-id', patient_id);
	$( ".form_patient_id" ).remove();
}

function populate_dets(patient_number){
  	var url = "<?php echo base_url()."dispensing/get_patient_detail";?>";      

	$.ajax({
        type: "POST",
        url: url,
        data:{'patient_number': patient_number},
        dataType: "html",       
        success: function(msg) {
        	console.log(msg);  
	       	var patient_details = JSON.parse(msg);

	       	var p_no = patient_details[0][0];
	       	var name = patient_details[0][1];
	       	var gender = patient_details[0][2];
	       	var dob = patient_details[0][3];
	       	var names_and_no = patient_details[0][4];
	       	var patient_id = patient_details[0][5];	       	
	       	$('#name').val(name);
	       	$('#dob').val(dob);
	       	$('#gender').val(gender);
	       	$('#p_no').val(p_no);
	       	$('#p_no').attr('data-patient-id', patient_id);
	       	// $('#search_results').val(names_and_no);
	       		// $('#search_results').html("<tr><td>"+name+"</td><td>"+p_no+"</td></tr>");
		    $('#dispense_form').append("<input type=\"hidden\" value="+patient_id+" name=\"form_patient_id\" class=\"form_patient_id\">");

	       		 // var search_table = $('#search_results').DataTable().ajax.reload();
	       			// search_table.ajax.reload();
        	

      	},
        error: function() {
            alert('Error occured');
        }
    });

}

$(".prescribed_units").on('keyup',function (){
        	var available=$(".prescribed_units").data("available");
        	// var input=parseInt($(this).closest("tr").find(".quantity_issued").val());
        	// alert(available);return;
        	if (input > available) {
        		alert("Kindly input an amount less or equal to your available unit balance");
        		$(".quantity_issued").val("0");
        	};

});

$("#dispense").click(function(){
	$("#dispense_form").submit();
});


});//end of jquery

</script>
<script src="<?php echo base_url().'assets/bower_components/intro.js/intro.js'?>" type="text/javascript"></script>
