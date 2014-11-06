 <style type="text/css">
 #stock_card{
  min-height: 350px;
  height: auto;
  border: 1px ridge;
  width: 96%;
  margin-top: 2%;
  float: left;
  margin-left: 0px;
 }
 #stock_status{
  min-height: 150px;
  height:auto;
  border: 1px ridge;
  width: 96%;
  margin-top: 2%;
  float: left;
  margin-left: 0px;
 }
 #stock_card_table{
  font-size: 12px;
 }
 #th-banner{
  text-align: center;
 }
 </style>



<?php include('allocation_links.php');?>
<div class="row" style="width:100%; margin-top:2%;margin-left:2%;">  
  <div id="stock_card">    
    <table id="stock_card_table" class="data-table">
      <thead>
        <tr>
          <th>Commodity Name</th>
          <th>AMC</th>
          <th>Stock on Hand at Facility</th>
          <th>MOS Central</th>
        </tr>
        <tr>
          <th colspan="4" id="th-banner">HIV Rapid Test Kit Stock Status as at end of September 2014</th>
        </tr>
      </thead>
      <tbody style="border-top: solid 1px #828274;">
        
        <?php 
          foreach ($stock_details as $key => $value) { ?>
            <tr>
              <td><?php echo $value['commodity_name'];?></td>
              <td><?php echo $value['amc'];?></td>
              <td><?php echo $value['endbal'];?></td>
              <td><?php echo $value['ratio'];?></td>
            </tr>
        <?php }
        ?>
               
      </tbody>
    </table>
  </div>
  <div id="stock_status">
    
  </div>
</div>

<script type="text/javascript">
  $('#trend_tab').removeClass('active_tab');    
  $('#stocks_tab').addClass('active_tab');  
  $('#allocations_tab').removeClass('active_tab');
</script>

<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>assets/datatable/jquery.dataTables.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#stock_card_table').dataTable({
      "bJQueryUI": false,
      "bPaginate": false
  });
  $("#stock_card_table").tablecloth({theme: "paper",         
    bordered: true,
    condensed: true,
    striped: true,
    sortable: true,
    clean: true,
    cleanElements: "th td",
    customClass: "data-table"
  }); 

});
</script>