

<pre>
<?php

$sql = $this->get('queries'); 

		
?>


<?php foreach ($sql as $num => $statement): ?>
	# Query no. <?php echo $num; ?>;
<?php echo htmlentities(diff_decode($statement)); ?>;
	
	
<?php endforeach; ?>


</pre>