<!-- [extend src="screen"] -->


<form method="post" action="?task=navigation.credentialsave">

	<table class="credentials">
	
	
		<tr>
			<td></td>
			<th colspan="1">Copy To</th>
			<th colspan="1">Copy From</th>
		</tr>
	
	
		<tr>
			<th>Stored Connections</th>
			<td><select class="picker" id="dest"><?php echo HtmlElement::SelectOptions($this->get('previousConnectionsGood')); ?></select></td>
			<td><select class="picker" id="source"><?php echo HtmlElement::SelectOptions($this->get('previousConnectionsGood')); ?></select></td>
		</tr>

		<tr>
			<th>Server</th>
			<td><input tabindex="1" class="copyable" type="text" name="dest_server" value="<?php echo $this->get('dest_server'); ?>" /></td>
			<td><input tabindex="16" class="autofillable" type="text" name="source_server" value="<?php echo $this->get('source_server'); ?>" /></td>
		</tr>
		<tr>
			<th>User</th>
			<td><input tabindex="2" class="copyable" type="text" name="dest_user" value="<?php echo $this->get('dest_user'); ?>" /></td>
			<td><input tabindex="17" class="autofillable" type="text" name="source_user" value="<?php echo $this->get('source_user'); ?>" /></td>
		</tr>
		<tr>
			<th>Password</th>
			<td><input tabindex="3" class="copyable" type="text" name="dest_password" value="<?php echo $this->get('dest_password'); ?>" /></td>
			<td><input tabindex="18" class="autofillable" type="text" name="source_password" value="<?php echo $this->get('source_password'); ?>" /></td>
		</tr>
		<tr>
			<th>Same Server?</th>
			<td colspan="2"><input tabindex="4" type="checkbox" name="same_server" value="1" /></td>
		</tr>

		<tr>
			<th>Database</th>
			<td>
				<div class="dest_refresh"><?php echo $this->get('dest_database'); ?></div>
				<a style="display: block; padding: 0.5em 0; font-weight: bold; " href="#" onclick="testConnectivity('.copyable', 'dest')" >[+]</a>
			</td>
			<td>
				<div class="source_refresh"><?php echo $this->get('source_database'); ?></div>
				<a style="display: block; padding: 0.5em 0; font-weight: bold; " href="#" onclick="testConnectivity('.autofillable', 'source')" >[+]</a>
			</td>
		</tr>


	</table>
	

	<p><input type="submit" value="Connect to databases" /></p>
		

</form>