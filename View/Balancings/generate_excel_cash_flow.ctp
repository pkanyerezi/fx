<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<a class="no-ajax" href="<?php echo $this->webroot.'ExcelFiles/cashflow.xls'; ?>" target="_blank"><span class="btn btn-success"><i class="icon-white icon-download"></i> download cashflow data</span></a>