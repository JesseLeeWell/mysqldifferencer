<!-- [extend src="screen"] -->

<h2>Your queries have executed in <?php echo $this->get('timelapse'); ?> seconds</h2>

<hr />

<?php foreach ($this->get('results') as $statement => $count): ?>

<code>
	<?php echo $statement; ?> <br /><br />
	
	<?php if (strlen($count) == 0): ?>
	[ No Affect ]
	<?php else: ?>
	[ Affected <?php echo $count; ?> rows ]
	<?php endif; ?>
</code>

<br /><br /><br />

<?php endforeach; ?>

<hr />

<?php foreach ($this->get('errors') as $logged): ?>

<code><?php 

$echo = str_replace(" // ", "<br />", $logged); 

if ($echo != $logged)
	echo $echo;

?></code>

<br /><br /><br />

<?php endforeach; ?>
