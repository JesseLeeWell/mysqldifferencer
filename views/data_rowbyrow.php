<!-- [extend src="screen"] -->

<?php

$differencesDisplaying = 0;
$rowscounted = 0;
$lastpk = null;

?>


<form method="get" action="index.php">

	Select: <select multiple="multiple" name="what[]" size="<?php echo count($this->get('tableColumns')); ?>"><?php echo HtmlElement::SelectOptions($this->get('tableColumns'), $this->get('what'), true); ?></select>
	Starting Row: <input name="starting" value="<?php echo $this->get('starting'); ?>" />
	Limit: <input name="limit" value="<?php echo $this->get('limit'); ?>" />
	
	<p>
		<?php if ($this->get('check')): ?>
		Checked By Default: <input type="checkbox" checked="checked" name="checked" value="1" />
		<?php else: ?>
		Checked By Default: <input type="checkbox" name="checked" value="1" />
		<?php endif; ?>
		<?php if ($this->get('showsome')): ?>
		Show Only Differences: <input type="checkbox" checked="checked" name="showsome" value="1" />
		<?php else: ?>
		Show Only Differences: <input type="checkbox" name="showsome" value="1" />
		<?php endif; ?>
	</p>
	
	<input type="submit" value="Select With These Parameters" />
	<input type="hidden" name="task" value="difference_data.rowbyrow" />
	<input type="hidden" name="table" value="<?php echo $this->get('table'); ?>" />

</form>

<p><?php echo $this->get('grabQuery'); ?></p>

<form method="post" action="index.php?task=resolve.queriespreview">

<?php foreach ($this->get('differenceMap') as $pk => $instruction): ?>

<?php

	$rowscounted++;
	$lastpk = $pk;

	if (empty($instruction))
	{
		continue;
	}
	
	$differencesDisplaying++;

	$allSource = $this->get('SourceData'); 
	$allDest = $this->get('DestinationData'); 
	$newQueries = $this->get('newQueries'); 

	$sourceData = isset($allSource[$pk]) ? $allSource[$pk] : null; 
	$destData = isset($allDest[$pk]) ? $allDest[$pk] : null; 
	
	?>
	
	
	
	<p>
		<label>
			<?php if ($this->get('check')): ?>
			<input type="checkbox" checked="checked" name="<?php echo $pk; ?>" value="<?php echo htmlentities(diff_encode($newQueries[$pk])); ?>" /> 
			<?php else: ?>
			<input type="checkbox" name="<?php echo $pk; ?>" value="<?php echo htmlentities(diff_encode($newQueries[$pk])); ?>" /> 
			<?php endif; ?>
			This difference can be resolved by: <?php echo strtoupper($instruction); ?>
		</label>
	<p>
	
	<?php 
	
	SingleItemHorizontal($sourceData, $destData, "yellow", $this->get('showsome'));
	

?>

	<hr >


<?php endforeach; ?>

<?php if ($differencesDisplaying): ?>
<input type="submit" value="Resolve Selected Differences" />
<?php endif; ?>

</form>


<p>Rows fetched: <?php echo $rowscounted; ?> &mdash; Differences found in: <?php echo $differencesDisplaying; ?></p>


<?php if ($this->get('limit') == $rowscounted): ?>
<form method="get" action="index.php">

	Starting Row: <input readonly="readonly" name="starting" value="<?php echo ($lastpk+1); ?>" />
	<input type="hidden" name="limit" value="<?php echo $this->get('limit'); ?>" />
	<input type="submit" value="Continue Paging Through Results" />
	<input type="hidden" name="task" value="difference_data.rowbyrow" />
	<input type="hidden" name="table" value="<?php echo $this->get('table'); ?>" />

</form>
<?php else: ?>

<p>There are no more results</p>

<?php endif; ?>