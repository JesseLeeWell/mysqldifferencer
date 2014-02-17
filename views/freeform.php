<!-- [extend src="screen"] -->

<form method="post" action="index.php?task=resolve.freeformrun">


<table class="overviews">
	<tr>
		<th><span style="font-style: italic; color: #888; ">Query will run against: </span> <?php echo $this->get('PrefetchedDestinationTitle'); ?></th>
		<th><span style="font-style: italic; color: #888; ">Query will run against: </span> <?php echo $this->get('PrefetchedSourceTitle'); ?></th>
	</tr>
	<tr>
		<td><textarea name="dest"><?php echo htmlentities($this->get('dest_preloaded')); ?></textarea>
		<td><textarea name="source"><?php echo htmlentities($this->get('source_preloaded')); ?></textarea>
	</tr>


</table>

<input type="submit" value="Proceed" />


</form>