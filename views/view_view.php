<!-- [extend src="screen"] -->



<?php $dataset = $this->get('ViewStructure'); ?>


<?php echo SingleItemVertical(nicifyDataSingle($dataset)); ?>
	
	


<h1>Columns:</h1>


<?php $dataset = $this->get('Columns'); ?>


<?php echo differ(nicifyDataDouble($dataset), null); ?>
	
	

