<!-- [extend src="screen"] -->



<table class="overviews">
	<tr>
		<th>
			Before
			<br />
			 <?php echo $this->get('PrefetchedDestinationTitle'); ?>
			<div style="font-style: italic; color: #888; ">Changes will be made to this database</div>
		</th>
		<th class="alternatecolor">Differences</th>
		<th>
			After
			<br />
			 <?php echo $this->get('PrefetchedSourceTitle'); ?>
		</th>
	</tr>


<?php if ($this->is('compiledIndexes')): ?>

	<?php foreach ($this->get('compiledIndexes') as $index => $instruction): ?>
		
		<tr>

			<td align="right">
			<?php if ($instruction != "add"): ?>
				<a href="index.php?task=difference_index.index_view&dbside=dest&table=<?php echo $this->get('table'); ?>&index=<?php echo $index; ?>" target="_blank"><?php echo $index; ?></a>
			<?php endif; ?>
			</td>

			<td class="alternatecolor">
			<?php if ($instruction): ?>
				<a href="index.php?task=difference_index.<?php echo $instruction; ?>&table=<?php echo $this->get('table'); ?>&index=<?php echo $index; ?>"><?php echo ucwords($instruction); ?></a>
			<?php endif; ?>
			</td>
			
			<td>
			<?php if ($instruction != "drop"): ?>
				<a href="index.php?task=difference_index.index_view&dbside=source&table=<?php echo $this->get('table'); ?>&index=<?php echo $index; ?>" target="_blank"><?php echo $index; ?></a>
			<?php endif; ?>
			</td>

		</tr>
	<?php endforeach; ?>

	
		<tr>
			<th></th>
			<td class="alternatecolor"><a href="index.php?task=difference_index.resolveall&table=<?php echo $this->get('table'); ?>">Resolve All</a></td>
			<th></th>
		</tr>

<?php endif; ?>
</table>


