<!-- [extend src="screen"] -->

<form method="get" action="index.php">

	Starting Row: <input name="starting" value="<?php echo $this->get('starting'); ?>" />
	Limit: <input name="limit" value="<?php echo $this->get('limit'); ?>" />
	<input type="submit" value="Select With These Parameters" />
	<input type="hidden" name="dbside" value="<?php echo $this->get('dbside'); ?>" />
	<input type="hidden" name="task" value="difference_data.data_view" />
	<input type="hidden" name="table" value="<?php echo $this->get('table'); ?>" />

</form>

<p><?php echo $this->get('grabQuery'); ?></p>
<?php $dataset = $this->get('data'); ?>


<?php echo differ(nicifyDataDouble($dataset), null); ?>
	
	

