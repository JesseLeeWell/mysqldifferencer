<!-- [extend src="screen"] -->


<?php $dataset = $this->get('RoutineStructure'); ?>


<h3><?php echo $this->get('LocationTitle'); ?></h3>

<?php echo SingleItemVertical(nicifyDataSingle($dataset)); ?>
	
	



