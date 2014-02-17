<!-- [extend src="screen"] -->



<?php 

$destd = nicifyDataSingle($this->get('DestinationStructure')); 
$srcd = nicifyDataSingle($this->get('SourceStructure')); 

	
		SingleItemVertical($srcd, $destd); 
?>


<hr />

<?php echo $this->display('queries_to_execute'); ?>