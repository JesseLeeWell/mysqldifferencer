<!-- [extend src="screen"] -->


<p>Here is your backup command:</p>
<pre><?php echo $this->get('backup_command_dest'); ?></pre>
<pre><?php echo $this->get('backup_command_source'); ?></pre>


<p>Here are your database copy commands:</p>
<pre><?php echo $this->get('restore_command'); ?></pre>
<pre><?php echo $this->get('restore_command_short'); ?></pre>