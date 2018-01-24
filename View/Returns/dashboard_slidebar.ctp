<div id="sidebar">
	<a href="#" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
	<ul style="display: block;">
		<li class="submenu active linc1">
			<a href="#" linc='linc1'><i class="icon icon-edit"></i> <span>Modules</span></a>
			<ul>
				<li><?php echo $this->Html->link('Names',array('controller'=>'names','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc1','sub_linc'=>'linc1a')); ?></li>
				<li><?php echo $this->Html->link('Users',array('controller'=>'users','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc1','sub_linc'=>'linc1b')); ?></li>
			</ul>
		</li>		
		<li class="submenu linc2">
			<a href="#" linc='linc2'><i class="icon icon-file"></i> <span>Sale Receipts</span></a>
			<ul>
				<li><?php echo $this->Html->link('List',array('controller'=>'sold_receipts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2a')); ?></li>
				<li><?php echo $this->Html->link('Add',array('controller'=>'sold_receipts','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2b')); ?></li>
			</ul>
		</li>
		
		<li class="submenu linc3">
			<a href="#" linc='linc3'><i class="icon icon-file"></i> <span>Purchase Receipts</span></span></a>
			<ul>
				<li><?php echo $this->Html->link('List',array('controller'=>'purchased_receipts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3a')); ?></li>
				<li><?php echo $this->Html->link('Add',array('controller'=>'purchased_receipts','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3b')); ?></li>
			</ul>
		</li>
		
		<li class="submenu linc4">
			<a href="#" linc='linc4'><i class="icon icon-print"></i> <span>Reports</span></a>
			<ul>				
				<li><?php echo $this->Html->link('Daily returns',array('controller'=>'purchased_receipts','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4a')); ?></li>
				<li><?php echo $this->Html->link('General returns',array('controller'=>'returns','action'=>'returns_weekly'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4b')); ?></li>
			</ul>
		</li>
		<li class="submenu linc19">
			<a href="#" linc='linc19'><i class="icon icon-edit"></i> <span>Forex-Bureax</span></a>
			<script>
				$(document).ready(function(){
					ggg();					
				});
				function ggg(){
					$.get("<?php echo $this->webroot;?>foxes/index", function(data) {$('.my-forex-bureaux').html((data));});
				}
			</script>
			<ul>
				<li>
					<div class='my-forex-bureaux'><div>
				</li>

			</ul>
		</li>
	</ul>		
</div><br/>


<script>
	var current_linc='linc_a';//set the first active link
	$(document).ready(function(){
		$('#sidebar a').click(function(){
			
			$('#sidebar ul li').removeClass('active');
			
			var linc=$(this).attr('linc');
			$('.'+linc).addClass('active');
			$('.display-none').fadeIn('slow');
			
			var _url=$(this).attr('data-taget');
			
			if($(this).hasClass('sub_link')){
				linc=$(this).attr('sub_linc');
			}
			
			if(_url!='' && _url!='#' && current_linc!=linc ){
				current_linc=linc;
				show_loading();
				$.get(_url, function(data) {
					after_fetching_data(data);
					remove_loading();
				});
			}
			
		});
		
	});
</script>

