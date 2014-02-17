<!-- [extend src="screen"] -->

<?php

$sql = $this->get('queries'); 

		
?>


<h1>Is that your final answer?</h1>


<p>You have selected to run the following <?php echo count($sql); ?> queries.  </p>

<form method="post" action="index.php?task=resolve.execute">

	<input type="submit" value="Execute queries" />

<?php foreach ($sql as $num => $statement): ?>
	<input type="hidden" name="<?php echo ($num); ?>" value="<?php echo htmlentities(($statement)); ?>" />
	<code class="preview"><?php echo htmlentities(diff_decode($statement)); ?></code>
<?php endforeach; ?>

	<input type="submit" value="Execute queries" />


</form>