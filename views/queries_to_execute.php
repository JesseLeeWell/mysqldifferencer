<h1>Solution:</h1>


<?php

$sql = $this->get('sql'); 

if (is_string($sql))
	$sql = array( $sql );
		
?>


<p>The system has determined that the following <?php echo count($sql); ?> queries will resolve the differences.</p>


<?php if (count($sql)): ?>
<form method="post" action="index.php?task=resolve.preview">

	<input type="submit" name="going_to_execute" value="Select queries" />
	<input type="submit" name="going_to_plain_text" value="Show queries as text" />

	<table>

	<?php foreach ($sql as $num => $rawstatement): ?>

	<?php
	
//		if (substr($rawstatement, -1, 1) == "=")
			$statement = diff_decode($rawstatement);
		// else
			// $statement = $rawstatement;
	
	?>
	
	
		<tr>
			<td>
				<input type="checkbox" checked="checked" name="sql_<?php echo ($num+1); ?>" value="<?php echo htmlentities(diff_encode($statement)); ?>" />
			</td>
			<td>
			
				<?php if (strlen($statement) > 200): ?>
			
				<div id="prev_short_<?php echo $num; ?>" >
				
					<code class="preview"><?php echo htmlentities(LibStrings::truncate($statement, 190)); ?></code>
				
					<p><a onclick="javascript: jQuery('#prev_short_<?php echo $num; ?>, #prev_long_<?php echo $num; ?>').toggle('slow')">
						This statement is <?php echo strlen($statement); ?> characters long.  Click to show all.
					</a></p>
				
				</div>
				
				<div id="prev_long_<?php echo $num; ?>" style="display: none; ">
					
					<p><a onclick="javascript: jQuery('#prev_short_<?php echo $num; ?>, #prev_long_<?php echo $num; ?>').toggle('slow')">
						Click to hide.
					</a></p>
					
					<code class="preview"><?php echo htmlentities($statement); ?></code>
				
				</div>
				
				<?php else: ?>
				<code class="preview"><?php echo htmlentities($statement); ?></code>
				<?php endif; ?>
			</td>
		</tr>
		
	<?php endforeach; ?>
		
	</table>

	<input type="submit" name="going_to_execute" value="Select queries" />
	<input type="submit" name="going_to_plain_text" value="Show queries as text" />


</form>
<?php endif; ?>