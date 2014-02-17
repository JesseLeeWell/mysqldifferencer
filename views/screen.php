<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
	
		<?php if ($this->is('NavigationTitle')): ?>
		<title><?php echo strip_tags($this->get('NavigationTitle')); ?> - MySQL Schema Differencer</title>
		<?php else: ?>
		<title>MySQL Differencer</title>
		<?php endif; ?>
		
		<link href="assets/style.css" rel="Stylesheet" type="text/css"  />
		<script src="assets/jquery-1.6.2.min.js" type="text/javascript"></script>
		<script src="assets/lib.js" type="text/javascript"></script>
		
	
	</head>

	<body>
	
	<?php 
	
		$links['Change Logins'] = 'index.php?task=navigation.login';
		$links['Swap Sides'] = 'index.php?task=navigation.swap';
		$toollinks['Free Form Query'] = 'index.php?task=resolve.freeform';
		$toollinks['Backups'] = 'index.php?task=navigation.backup';
		$difflinks['Tables'] = 'index.php?task=difference_table.overview';
//		$difflinks['Indexes'] = 'index.php?task=difference_index.overview';
		$difflinks['Views'] = 'index.php?task=difference_view.overview';
		$difflinks['Stored Routines'] = 'index.php?task=difference_routine.overview';
		$difflinks['Data'] = 'index.php?task=difference_data.overview';
		$difflinks['Variables'] = 'index.php?task=difference_variables.overview';
		
		$thisTask = Request::getVar('task');
		
	?>
		<div id="header">
			<table width="100%">	
				<tr>
					<td>
						<h2><a href="index.php">Start</a> &ndash; <?php echo $this->get('NavigationTitle'); ?></h2>
					</td>
					<td align="right">
						<p>
							<i>Changes will be copied to:</i> <b><?php echo $this->get('PrefetchedDestinationTitle'); ?></b> [Before]
							<br />
							<b><?php echo $this->get('PrefetchedSourceTitle'); ?></b> [After]
						</p>
					
					</td>
				</tr>
			</table>
			
		</div>
	
	
		
		<div class="navigation">
		
			
			<fieldset>
				<legend>Servers</legend>
			
				<ul>
				
					<?php foreach ($links as $text => $link): ?>
						<?php if ($link): ?>
							<li><a href="<?php echo $link; ?>"><?php echo $text; ?></a></li>
						<?php else: ?>
							<li><?php echo $text; ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				
			</fieldset>
			
			
			<fieldset>
				<legend>Discover</legend>
			
				<ul>
				
					<?php foreach ($difflinks as $text => $link): ?>
						<?php if ($link): ?>
							<li><a href="<?php echo $link; ?>"><?php echo $text; ?></a></li>
						<?php else: ?>
							<li><?php echo $text; ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				
			</fieldset>

			<fieldset>
				<legend>Tools</legend>
			
				<ul>
				
					<?php foreach ($toollinks as $text => $link): ?>
						<?php if ($link): ?>
							<li><a href="<?php echo $link; ?>"><?php echo $text; ?></a></li>
						<?php else: ?>
							<li><?php echo $text; ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				
			</fieldset>

		</div>
		
		

		<div id="screen">
	
			<!-- [reserved block] -->
		
		</div>
	
		

		
		
		
		
		
<?php 
/*
	echo '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
	
print_pre($this);

$dbgo = new DebugObject;

print_pre($dbgo->dblog('source'));
print_pre($dbgo->dblog('dest'));
*/

?>
	
	</body>
	
</html>