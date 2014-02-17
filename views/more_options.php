<!-- [extend src="screen"] -->




<table>
	<tr>
		<th>
			Option
		</th>
		<th>
			Explanation
		</th>
	</tr>
	<tr>
		<td>
		<?php if ($this->get('localcopy')): ?>
			<a href="index.php?task=difference_data.localcopy&table=<?php echo $this->get('table'); ?>">Local Copy</a>
		<?php else: ?>
			Local Copy
		<?php endif; ?>
		</td>
		<td>
			This option is available for datasets of any size, small or large, but only works if the databases are on the same server.
		</td>
	</tr>
	<tr>
		<td>
			<a href="index.php?task=difference_data.remotecopy&table=<?php echo $this->get('table'); ?>">Remote Copy</a>
		</td>
		<td>
			This option is available for all datasets.
		</td>
	</tr>
	
	<tr>
		<td>
		<?php if ($this->get('rowbyrow')): ?>
			<a href="index.php?task=difference_data.rowbyrow&table=<?php echo $this->get('table'); ?>">Row by row</a>
		<?php else: ?>
			Row by row
		<?php endif; ?>
		</td>
		<td>
			This option is the most complete way of differencing data.  An integer primary key is required for this feature
		</td>
	</tr>
</table>


<?php 

$destd = nicifyDataSingle($this->get('sourceMetaData')); 
$srcd = nicifyDataSingle($this->get('destMetaData')); 

SingleItemVertical($destd, $srcd); 

?>

<hr />


<p align="center">

	<?php if ($this->is('previousTable')): ?>
		&lt;&lt;  
		<?php echo $this->get('previousTable'); ?>
		&nbsp; &nbsp; &nbsp; 
	<?php endif; ?>
	
	<?php echo $this->get('table'); ?> 
	&nbsp; &nbsp; &nbsp; 
	<?php echo $this->get('nextTable'); ?>
	&gt;&gt;  
</p>


