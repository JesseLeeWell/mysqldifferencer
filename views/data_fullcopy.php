<!-- [extend src="screen"] -->


<h1>Current:</h1>

<?php $destd = nicifyDataSingle($this->get('DestinationStructure')); ?>
<?php $srcd = nicifyDataSingle($this->get('SourceStructure')); ?>

<?php if ($this->is('DestinationStructure')): ?>

	<?php
	
		reformshit($destd, $srcd, "Index Name"); 
	?>

	
<?php else: ?>

	<p>Desination data not found</p>

<?php endif; ?>


<hr />

<h1>Proposed Change:</h1>

<?php if ($this->is('SourceStructure')): ?>

	<?php 
		reformshit($srcd, $destd, "Index Name"); 
	
	?>
	
<?php else: ?>

	<p>After data not found</p>

<?php endif; ?>




<hr />


<?php echo $this->display('queries_to_execute'); ?>