<div class="users form">
<?php echo $this->Form->create('User',array('type'=>'file','id'=>'user_edit_form'));?>
	<fieldset>
		<legend><?php __('Edit User'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('username');
		echo $this->Form->input('password',array('value'=>''));
		echo $this->Form->input('password_confirmation',array('type'=>'password'));
		if($admin){		
			if(isset($this->data['User']['role'])){
				switch($this->data['User']['role']){
					case 'admin':
						echo $this->Form->input('role',array('type'=>'select','options'=>array('others','admin','store'),'selected'=>1,'class'=>'role'));
						break;
					case 'store':
						echo $this->Form->input('role',array('type'=>'select','options'=>array('others','admin','store'),'selected'=>2,'class'=>'role'));
						break;
						
				}
			}
			if(isset($this->data['User']['other_role'])){
				switch($this->data['User']['other_role']){
					case 'giveOutItems':
						echo $this->Form->input('other_role',array('type'=>'select','options'=>array('','giveOutItems','returnItems'),'selected'=>1,'class'=>'other_role'));
						break;
					case 'returnItems':
						echo $this->Form->input('other_role',array('type'=>'select','options'=>array('','giveOutItems','returnItems'),'selected'=>2,'class'=>'other_role'));
						break;
					default:
						echo $this->Form->input('other_role',array('type'=>'select','options'=>array('','giveOutItems','returnItems'),'class'=>'other_role'));
						break;
						
				}
			}else{
				echo $this->Form->input('other_role',array('type'=>'select','options'=>array('','giveOutItems','returnItems'),'Label select if you select role as "store"','class'=>'other_role'));
			}			
		}
		//echo $this->Form->input('fileField',array('type'=>'file','label'=>'Browse to change profile image.(png,jpg)','name'=>'fileField'));
		//echo $this->Form->input('profile_image',array('type'=>'hidden'));
		if($super_admin){
			//echo 'Choose below if either bank or director is being added. But not both.';
			//echo $this->Form->input('is_bank',array('label'=>'Is Bank'));
			echo $this->Form->input('is_director',array('label'=>'Is Director'));
		}
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('User.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('User.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('action' => 'index'));?></li>
	</ul>
</div>


<script>
    $(document).ready(function(){
       
        va=$('.role').val();
        if(va!=2){
            $('.other_role').hide();
        }
       
       $('.role').change(function(){
            va=$(this).val();
            if(va!=2){
                $('.other_role').hide();
            }else{
                $('.other_role').show();
            }
        });
        
        $('#user_edit_form').submit(function(){
            va=$('.role').val();
            if(va!=2){
                $('.other_role').remove();
            }
        });
        
    });
</script>