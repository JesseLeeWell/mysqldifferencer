

function grabData(controlName)
{
	keyArray = new Array();
	dataArray = new Array();
	
	jQuery(controlName).each( function(index, element) 
	{
		rawval = jQuery(this).val();
		rawname = jQuery(this).attr('name');
		
		if (rawval != "null" && rawval != null)
		{
			value = escape(rawval)
		
			if (keyArray[rawname])
				keyArray[rawname] = keyArray[rawname]+','+value;
			else
				keyArray[rawname] = value;
		}
	});
	
	
	for (indexName in keyArray)
	{
		dataArray.push(indexName+'='+keyArray[indexName]);
	
	}
	
	return dataArray.join("&");
}

function testConnectivity(findclass, dbside)
{

	connectionstring = grabData(findclass) + '&dbside=' + dbside;
	
	torefresh = '.' + dbside + '_refresh'
	
	jQuery(torefresh).html( '(1) Contacting Server' )
	
	jQuery.ajax('index.php?task=navigation.databaselist', 
	{
	
		type: 'POST',
		data: connectionstring, 
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown)
		{ 
			alert("The system was unable to connect due to an error: \n\n"+textStatus)
			jQuery(torefresh).html( '(2) Error Contacting Server' );
			
		}, 
		success: function(returnData) 
		{
			if (returnData.message)
				if (returnData.message.length > 0)
					alert(returnData.message)
			
			jQuery(torefresh).html( returnData.content )
		
		
		}
	
	});

}



jQuery(document).ready(function(){

	
	jQuery('.picker').change(function(){
	
		toeval = jQuery(this).val();
		
		if (toeval)
		{
			objection = eval("("+toeval+")");
			side = jQuery(this).attr('id');
		
			jQuery.each( objection, function(i, value){
				fullprop = side+'_'+i;
				jQuery('input[name="'+fullprop+'"]').val(value);
				jQuery('input[name="'+fullprop+'"]').keyup();
			})
		}
		
	});
	




	jQuery('input.copyable').keyup(function()
	{
	
		checked = jQuery('input[name="same_server"]').attr('checked');
		if (checked)
		{
		
			nameatt = jQuery(this).attr('name');
			altername = 'input[name="'+nameatt.replace("dest_", "source_")+'"]'
			thisval = jQuery(this).val();
			jQuery(altername).val(thisval);
		}
					
	});


	



	jQuery('input[name="same_server"]').bind('change', function(){
	
		checked = jQuery(this).attr('checked');
		
		if (checked)
		{
			jQuery('#source').attr('disabled', 'disabled')
		}
		else
		{
			jQuery('#source').removeAttr('disabled')
		}
		
		jQuery('input.autofillable').each(function()
		{
		
			if (checked)
			{
				nameatt = jQuery(this).attr('name');
				altername = 'input[name="'+nameatt.replace("source_", "dest_")+'"]'
				correspondingval = jQuery(altername).val();
				jQuery(this).val(correspondingval);
				jQuery(this).attr('readonly', 'readonly')
			}
			else
			{
				jQuery(this).removeAttr('readonly')
			}
			
		
		});
	
	});


});
