<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div id="use">
    <div class="users view">
        <div class="related">    
            <?php echo $this->Form->create('SafeTransaction');?>
                <?php
                	echo $this->Form->input('id');
                    echo $this->Form->input('amount',['label'=>'Amount ('.$this->data['SafeTransaction']['currency'].')']);
                    echo $this->Form->input('currency');
					echo $this->Form->input('comment');
                    echo $this->Form->input('date');
                ?>
            <?php echo $this->Form->end(__('Update', true));?>
        </div>
    </div>
</div>