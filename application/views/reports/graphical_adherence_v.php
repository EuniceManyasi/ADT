<div class="full-content container">
    <div class="row-fluid">
    <?php $this->load->view("reports/reports_top_menus_v");?>
  </div>
   <div class="row-fluid">
    <div class="span6">
      <h3>Overview</h3>
      <div id="overview"><div class="loadingDiv" style="margin:20% 0 20% 0;" ><img style="width: 30px;margin-left:50%" src="<?php echo asset_url().'images/loading_spin.gif' ?>"></div></div>
    </div>
    <div class="span6">
      <h3>ART vs Non-ART</h3>
      <div id="service"><div class="loadingDiv" style="margin:20% 0 20% 0;" ><img style="width: 30px;margin-left:50%" src="<?php echo asset_url().'images/loading_spin.gif' ?>"></div></div>
    </div>
   </div>
   <div class="row-fluid">
    <div class="span6">
      <h3>Male vs Female</h3>
      <div id="gender"><div class="loadingDiv" style="margin:20% 0 20% 0;" ><img style="width: 30px;margin-left:50%" src="<?php echo asset_url().'images/loading_spin.gif' ?>"></div></div>
    </div>
    <div class="span6">
      <h3>Age</h3>
      <div id="age"><div class="loadingDiv" style="margin:20% 0 20% 0;" ><img style="width: 30px;margin-left:50%" src="<?php echo asset_url().'images/loading_spin.gif' ?>"></div></div>
    </div>
   </div>
</div>

<script type="text/javascript" src="<?php echo asset_url().'Scripts/highcharts/modules/export_csv.js'?>"></script>
<!--custom script-->
<script type='text/javascript'>
    $(function(){
      var charts = ["overview","service","gender","age"];
      //Loop through Charts
      $.each(charts,function(i,v){
        //var url = "<?php echo base_url().'report_management/get_adherence_pill_count/'.$type.'/'.$start_date.'/'.$end_date.'/'; ?>"+v;
        var url = "<?php echo base_url().'report_management/getAdherence/'.$type.'/'.$start_date.'/'.$end_date.'/'; ?>"+v;
        //Load charts
            load_charts(v,url);
      });
    });


   function load_charts(div,url){
     //Load onto div
     $("#"+div).load(url);
   }
</script>