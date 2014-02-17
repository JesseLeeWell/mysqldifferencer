<!-- [extend src="screen"] -->


<?php $destd = nicifyDataSingle($this->get('DestinationStructure')); ?>
<?php $srcd = nicifyDataSingle($this->get('SourceStructure')); ?>
<?php
	
		SingleItemVertical($srcd, $destd); 
	?>


<hr />


<?php echo $this->display('queries_to_execute'); ?>