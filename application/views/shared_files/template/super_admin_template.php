<style>  
    #footer{
        /*position: relative*/
    }
</style>

<div class="container admin_dashboard">

    <div class="row margin_top_sm">

        <div class="col-md-4">
                <a href="<?php echo base_url().'user/user_create';?>">
            <div class="background_blue dash_notifications">
                <span class="icon-lg glyphicon glyphicon-user"></span>
                User Management
            </div>
                </a>
        </div>

        <div class="col-md-4">
                <a href="<?php echo base_url().'admin/manage_facilities';?>">
            <div class="background_green_light dash_notifications">
                <span class="icon-lg fa fa-hospital-o"></span>
                Facility Management
            </div>
                </a>
        </div>

        <div class="col-md-4">
                <a href="<?php echo base_url().'admin/manage_commodities';?>">
            <div class="background_blue_light dash_notifications">
                <span class="icon-lg fa fa-medkit"></span>
                Registered Commodities 
            </div>
                </a>
        </div>
    </div>

    <div class="row margin_top_sm">

        <div class="col-md-4">
                <a href="<?php echo base_url().'git_updater/admin_updates_home';?>">
            <div class="background_yellow dash_notifications">
                <span class="icon-lg fa fa-cloud-upload"></span>
                Updates  
            </div>
                </a>  
        </div>
        <div class="col-md-4">
                <a href="<?php echo base_url().'home';?>">
            <div class="background_purple dash_notifications">
                <span class="icon-lg fa fa-envelope-o"></span>
                Messages    
            </div>
                </a>
        </div>
        <div class="col-md-4">
                <a href="<?php echo base_url().'admin/user_logs';?>">
            <div class="background_maroon dash_notifications">
                <span class="icon-lg glyphicon glyphicon-dashboard"></span>
                Online Users
            </div>
                </a>
        </div>
    </div>

</div>

<div class="container-fluid" style="border: 0px solid #036;">

    <div class="row" style="margin-top: 1%;border: 0px solid #036;" >


        <div class="col-md-6"  id="">
            <div class="row" style="margin-left: 0px;"> 
                <div class="col-md-12" style="height:350px;border: 0px solid #036;">

                </div>

            </div>
        </div>


        </div>

</div>

</div>

<script>
    $(document).ready(function(){

    });

</script>