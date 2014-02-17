<!-- [extend src="screen"] -->



<table class="overviews">
	<tr>
		<th>
			Before
			<br />
			 <?php echo $this->get('DestinationTitle'); ?>
			<div style="font-style: italic; color: #888; ">Changes will be made to this database</div>
		</th>
		<th class="alternatecolor">Differences</th>
		<th>
			After
			<br />
			 <?php echo $this->get('SourceTitle'); ?>
		</th>
	</tr>


<?php if ($this->is('compiledTables')): ?>

	<?php foreach ($this->get('compiledTables') as $table => $instruction): ?>
	
	
		<?php 
		
//			print_pre($this->get('compiledTables')); exit;
		
			$destItem = LibStrings::getPiece($table, " : ", 0);
			$sourceItem = LibStrings::getPiece($table, " : ", 1);
			
			if (empty($sourceItem))
				$sourceItem = $destItem;
		?>

		
		<tr>

			

			<td align="right">
				<?php if ($instruction != "create"): ?>
					<a href="index.php?task=difference_table.table_view&dbside=dest&table=<?php echo $sourceItem; ?>" target="_blank"><?php echo $sourceItem; ?></a>
				<?php endif; ?>
				</td>

				<td class="alternatecolor">
				<?php if ($instruction): ?>
					<?php if (in_array($instruction, array("create", "drop") )) : ?>
					<a href="index.php?task=difference_table.<?php echo $instruction; ?>&table=<?php echo $table; ?>"><?php echo ucwords($instruction); ?></a>
					<?php endif; ?>

					<?php if (in_array($instruction, array("rename", "rename to", "rename from") )) : ?>
					<a href="index.php?task=difference_table.rename&table=<?php echo $sourceItem; ?>"><?php echo ucwords($instruction); ?></a>
					<?php endif; ?>
					
					<?php if (stripos($instruction, "resolve") !== false): ?>
					<a href="index.php?task=difference_table.resolve&table=<?php echo $table; ?>">Resolve</a>
					<?php endif; ?>
					
					<?php if (stripos($instruction, "column") !== false): ?>
					<a href="index.php?task=difference_column.overview&table=<?php echo $table; ?>">Columns</a>
					<?php endif; ?>
					
					<?php if (stripos($instruction, "index") !== false): ?>
					<a href="index.php?task=difference_index.overview&table=<?php echo $table; ?>">Indexes</a>
					<?php endif; ?>
				<?php endif; ?>
				</td>
				
				<td>
				<?php if ($instruction != "drop"): ?>
					<a href="index.php?task=difference_table.table_view&dbside=source&table=<?php echo $destItem; ?>" target="_blank"><?php echo $destItem; ?></a>
				<?php endif; ?>
				</td>

		</tr>

	<?php endforeach; ?>
		<tr>
			<th></th>
			<td class="alternatecolor"><a href="index.php?task=difference_table.resolveall">Resolve All</a></td>
			<th></th>
		</tr>

<?php endif; ?>
</table>


