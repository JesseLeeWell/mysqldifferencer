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


<?php if ($this->is('compiledViews')): ?>

	<?php foreach ($this->get('compiledViews') as $table => $instruction): ?>
		
		<tr>
			

			<td align="right">
			<?php if ($instruction != "create"): ?>
				<a href="index.php?task=difference_view.view_view&dbside=dest&view=<?php echo $table; ?>" target="_blank"><?php echo $table; ?></a>
			<?php endif; ?>
			</td>

			<td class="alternatecolor">
			<?php if ($instruction): ?>
				<a href="index.php?task=difference_view.<?php echo $instruction; ?>&view=<?php echo $table; ?>"><?php echo ucwords($instruction); ?></a>
			<?php endif; ?>
			</td>
			
			<td>
			<?php if ($instruction != "drop"): ?>
				<a href="index.php?task=difference_view.view_view&dbside=source&view=<?php echo $table; ?>" target="_blank"><?php echo $table; ?></a>
			<?php endif; ?>
			</td>
				
		</tr>
	<?php endforeach; ?>

	
		<tr>
			<th></th>
			<td class="alternatecolor"><a href="index.php?task=difference_view.resolveall">Resolve All</a></td>
			<th></th>
		</tr>

<?php endif; ?>
</table>


