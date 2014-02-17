<!-- [extend src="screen"] -->


<?php $dataset = $this->get('TableStructure'); ?>


<?php echo SingleItemVertical(nicifyDataSingle($dataset)); ?>
	
	


<h1>Columns:</h1>


<?php $dataset = $this->get('Columns'); ?>


<?php echo differ(nicifyDataDouble($dataset), null); ?>
	
	

<h1>Indexes:</h1>


<?php $dataset = $this->get('Indexes'); ?>


<?php echo differ(nicifyDataDouble($dataset), null); ?>
	
	

