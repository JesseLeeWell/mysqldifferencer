<!-- [extend src="screen"] -->

<?php $showingtables = array(); ?>

<form action="index.php?task=difference_data.overview" method="post">

<table class="overviews">
	<tr>
		<th colspan="4"><span style="font-style: italic; color: #888; ">Changes will be made to: </span> <?php echo $this->get('DestinationTitle'); ?></th>
	</tr>
	<tr>
		<th>Before</th>
		<th>Detected</th>
		<th>After</th>
		<th>Show</th>
	</tr>


<?php if ($this->is('compiledData')): ?>

	<?php foreach ($this->get('compiledData') as $table => $instruction): ?>
	
		<?php 
		
			if (is_array($this->get('preHiddenTables')))
				if (in_array($table, $this->get('preHiddenTables')) == false)
					continue; 
					
			$showingtables[] = "hidden[]=".$table;
				
		?>
		
		
		<tr>
		
			<td>
			<?php if ($instruction != "no dest"): ?>
				<?php echo $table; ?>
				<br />
				<a href="index.php?task=difference_data.data_view&dbside=dest&table=<?php echo $table; ?>">SELECT DATA</a>
				| 
				<a href="index.php?task=difference_table.table_view&dbside=dest&table=<?php echo $table; ?>" target="_blank">SHOW STRUCTURE</a>
			<?php endif; ?>
			</td>

			<?php if ($instruction == 'equal'): ?>
			<td>
				<a target="_blank" href="index.php?task=difference_data.more_options&table=<?php echo $table; ?>"><?php echo ucwords($instruction); ?></a>
			</td>
			<?php elseif ($instruction == 'difference'): ?>
			<td style="background-color: yellow; ">
				<a target="_blank" style="color: black; " href="index.php?task=difference_data.more_options&table=<?php echo $table; ?>"><?php echo ucwords($instruction); ?></a>
			</td>
			<?php else: ?>
			<td>
			</td>
			<?php endif; ?>
			
			<td>
			<?php if ($instruction != "no source"): ?>
				<?php echo $table; ?>
				<br />
				<a href="index.php?task=difference_data.data_view&dbside=source&table=<?php echo $table; ?>">SELECT DATA</a>
				| 
				<a href="index.php?task=difference_table.table_view&dbside=source&table=<?php echo $table; ?>" target="_blank">SHOW STRUCTURE</a>
			<?php endif; ?>
			</td>
			
			<td align="center">
				<input type="checkbox" name="hidden[]" value="<?php echo $table; ?>" />
			</td>

		</tr>

	<?php endforeach; ?>
	
		<tr>
			<th colspan="4" align="right"><button type="submit">Show Only Selected Tables</button></th>
		</tr>
		
		<?php if (is_array($this->get('preHiddenTables'))): ?>
		<tr>
			<th colspan="4"><?php echo Request::selfurl()."&".implode("&", $showingtables); ?></th>
		</tr>
		<?php endif; ?>
<?php endif; ?>
</table>

</form>
