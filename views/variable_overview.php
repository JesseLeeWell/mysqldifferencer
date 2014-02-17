<!-- [extend src="screen"] -->

<form method="GET" action="index.php">

	SHOW <select name="kind">
	<?php echo HtmlElement::SelectOptions($this->get('kindList'), Request::getVar('kind'), true); ?>
	</select>

	LIKE <input type="text" name="search" value="<?php echo Request::getVar('search'); ?>" />
	<input type="submit" value="Search" />
	
	<p>Helpful keywords:  <i>size, innodb, myisam, version, cache, log</i></p>
	
	<input type="hidden" name="task" value="difference_variables.overview" />

</form>



<?php SingleItemVertical($this->get('sourceVariables'), $this->get('destVariables')); ?>

