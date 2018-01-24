<?php echo $this->Html->script(array('script_dynamic_content','Chart'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<script>
  var colors = ['255,255,255','247,70,74','70,191,189','253,180,90','148,159,177','77,83,96'];
</script>
<div class="content">
      <!-- Main row -->
      <div class="connectedSortable">

        <?php if(!empty($closings)):?>
        <section class="col-lg-6" id="TripWorkFuelExpense">
          <!-- solid TruckExpenses graph -->
          <div class="box box-solid bg-green-gradient">
            
            <div class="box-header">
              <i class="fa fa-th"></i>

              <h3 class="box-title">&nbsp;&nbsp;&nbsp; <span style="color:#fff">Profits</span> Vs <span style="color:rgba(247,70,74,1)">Expenses</span></h3>
            </div>  

            <div class="box-body border-radius-none">
                <canvas id="canvasPerformance2Lines" width="400" height="200"></canvas>

                <script>
                  var barChartDataPerformance2 = '[<?php
                    $counter = 0;$total = count($closings);foreach($closings as $closing) {$counter ++;
                      echo '{"Transaction":{"amount":"'.date('d/M',strtotime($closing['Opening']['date'])).'","total_profits":"'.$closing['Opening']['total_profits'].'","total_expenses":"'.$closing['Opening']['total_expenses'].'"}}';
                      if($counter!=$total) echo ',';
                    }
                  ?>]';
                  barChartDataPerformance2 = jQuery.parseJSON(barChartDataPerformance2);
                  var D_labels = [],all_labels = [];
                  var D_dataTotalProfit = [];
                  var D_dataTotalExpenses = [];
                  $.each(barChartDataPerformance2,function(i,j){
                    //if(this.Transaction.total_count>5){
                      D_labels.push(this.Transaction.amount);
                      D_dataTotalProfit.push(this.Transaction.total_profits);
                      D_dataTotalExpenses.push(this.Transaction.total_expenses);
                    //}
                    all_labels.push(this.Transaction.amount);
                  });
                 
                  var canvasPerformance2Data = {
                    labels : D_labels,
                    datasets : [
                      {data : D_dataTotalProfit,fillColor : "rgba("+colors[0]+",0.1)",strokeColor : "rgba("+colors[0]+",0.8)",highlightFill: "rgba("+colors[0]+",0.75)",highlightStroke: "rgba("+colors[0]+",1)"},
                      {data : D_dataTotalExpenses,fillColor : "rgba("+colors[1]+",0.1)",strokeColor : "rgba("+colors[1]+",0.8)",highlightFill: "rgba("+colors[1]+",0.75)",highlightStroke: "rgba("+colors[1]+",1)"},
                    ]
                  };
                </script>
            </div>
          </div>
          <!-- /.box -->
        </section>
        <?php endif;?>

        <?php if(0):?>
        <section class="col-lg-6" id="TruckExpenses">
          <div class="box box-solid bg-yellow-gradient">
            <div class="box-header">
              <i class="fa fa-th"></i>

              <h3 class="box-title">Truck Expenses</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="box-body border-radius-none">
              <canvas id="canvasTruckExpensesLines" width="400" height="200"></canvas>

              <script>
                var barChartDataTruckExpenses = '[<?php
                  $counter = 0;$total = $trackExpenses->count();foreach($trackExpenses as $row) {$counter ++;
                    echo '{"Transaction":{"amount":"'.$row->truck->truck_number.'","total_count":"'.$row->total_amount.'"}}';
                    if($counter!=$total) echo ',';
                  }
                ?>]';
                barChartDataTruckExpenses = jQuery.parseJSON(barChartDataTruckExpenses);
                var D_labels = [],all_labels = [];
                var D_dataTotalAmount = [];
                $.each(barChartDataTruckExpenses,function(i,j){
                  //if(this.Transaction.total_count>5){
                    D_labels.push(this.Transaction.amount);
                    D_dataTotalAmount.push(this.Transaction.total_count);
                  //}
                  all_labels.push(this.Transaction.amount);
                });
               
                var canvasTruckExpensesData = {
                  labels : D_labels,
                  datasets : [
                    {data : D_dataTotalAmount,fillColor : "rgba("+colors[0]+",0.1)",strokeColor : "rgba("+colors[0]+",0.8)",highlightFill: "rgba("+colors[0]+",0.75)",highlightStroke: "rgba("+colors[0]+",1)"},
                  ]
                }
              </script>

            </div>
          </div>
        </section>
        <?php endif;?>

      </div>
      <?php if(!empty($truckPerformanceRates)):?>
      <div class="row connectedSortable">
        <section class="col-lg-6" id="TripWorkFuel">
          <!-- solid TruckExpenses graph -->
          <div class="box box-solid bg-teal-gradient">
            <div class="box-header">
              <i class="fa fa-th"></i>

              <h3 class="box-title">Performance <sup><small class="label bg-teal">(Trip+Work-Fuel)</small></sup></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="box-body border-radius-none">
                <canvas id="canvasPerformanceLines" width="400" height="200"></canvas>

                <script>
                  var barChartDataPerformance = '[<?php
                    $counter = 0;$total = count($truckPerformanceRates);foreach($truckPerformanceRates as $row) {$counter ++;
                      echo '{"Transaction":{"amount":"'.$row['truck'].'","total_count":"'.$row['totalRate1'].'"}}';
                      if($counter!=$total) echo ',';
                    }
                  ?>]';
                  barChartDataPerformance = jQuery.parseJSON(barChartDataPerformance);
                  var D_labels = [],all_labels = [];
                  var D_dataTotalAmount = [];
                  $.each(barChartDataPerformance,function(i,j){
                    //if(this.Transaction.total_count>5){
                      D_labels.push(this.Transaction.amount);
                      D_dataTotalAmount.push(this.Transaction.total_count);
                    //}
                    all_labels.push(this.Transaction.amount);
                  });
                 
                  var canvasPerformanceData = {
                    labels : D_labels,
                    datasets : [
                      {data : D_dataTotalAmount,fillColor : "rgba("+colors[0]+",0.1)",strokeColor : "rgba("+colors[0]+",0.8)",highlightFill: "rgba("+colors[0]+",0.75)",highlightStroke: "rgba("+colors[0]+",1)"},
                    ]
                  }
                </script>
            </div>
          </div>
          <!-- /.box -->
        </section> 
      </div>
      <?php endif;?>
      <!-- /.row (main row) -->
</div>

<script>
  window.onload = function(){
    var scaleFontColor = "#fff";
    var scaleGridLineColor = "rgba(249, 249, 249, 0.2)";
    var scaleGridLineWidth = 1;
    
    <?php if(!empty($closings)):?>
    var ctxCanvasPerformance2Lines = document.getElementById("canvasPerformance2Lines").getContext("2d");
    window.myLine = new Chart(ctxCanvasPerformance2Lines).Line(canvasPerformance2Data, {
      responsive: true,
      scaleFontColor: scaleFontColor,
      scaleLineColor: "rgba(0,0,0,0)",
      scaleGridLineColor : scaleGridLineColor,
      scaleShowVerticalLines: false,
      scaleGridLineWidth : scaleGridLineWidth
    });
    <?php endif;?>

    <?php if(0):?>
     /*var ctxCanvasDepositTruckExpensesBars = document.getElementById("canvasDepositAmountGroupsBars").getContext("2d");*/
    var ctxCanvasTruckExpensesLines = document.getElementById("canvasTruckExpensesLines").getContext("2d");
    /*window.myBar = new Chart(ctxCanvasTruckExpensesBars).Bar(canvasTruckExpensesData, {responsive : true});*/
    window.myLine = new Chart(ctxCanvasTruckExpensesLines).Line(canvasTruckExpensesData, {
      responsive: true,
      scaleFontColor: scaleFontColor,
      scaleLineColor: "rgba(0,0,0,0)",
      scaleGridLineColor : scaleGridLineColor,
      scaleShowVerticalLines: false,
      scaleGridLineWidth : scaleGridLineWidth
    });
    <?php endif;?>

    <?php if(!empty($truckPerformanceRates)):?>
    var ctxCanvasPerformanceLines = document.getElementById("canvasPerformanceLines").getContext("2d");
    window.myLine = new Chart(ctxCanvasPerformanceLines).Line(canvasPerformanceData, {
      responsive: true,
      scaleFontColor: scaleFontColor,
      scaleLineColor: "rgba(0,0,0,0)",
      scaleGridLineColor : scaleGridLineColor,
      scaleShowVerticalLines: false,
      scaleGridLineWidth : scaleGridLineWidth
    });
    <?php endif;?>

  }
</script>

<style>
.bg-green-gradient {
  background: #00a65a !important;
  background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #00a65a), color-stop(1, #00ca6d)) !important;
  background: -ms-linear-gradient(bottom, #00a65a, #00ca6d) !important;
  background: -moz-linear-gradient(center bottom, #00a65a 0%, #00ca6d 100%) !important;
  background: -o-linear-gradient(#00ca6d, #00a65a) !important;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00ca6d', endColorstr='#00a65a', GradientType=0) !important;
  color: #fff;
}
.connectedSortable {
  min-height: 100px;
}
.box-body{
  margin:10px;
}
</style>