<?php



function diff_decode($str, $default = null)
{
	if (substr($str, 0, 12) == "DIFF_ENCODE_")
	{
		return base64_decode(substr($str, 12));
	}

//	trigger_error("Cannot decode: ".$str);
	
	return $str;
}



function diff_encode($str)
{
	if (substr($str, 0, 12) == "DIFF_ENCODE_")
	{
		trigger_error("Already encoded.  Cannot encode: ".$str);
		return $str;
	}


	$base = base64_encode($str);

	return "DIFF_ENCODE_".$base;

}




function cleanifyEntityName()
{
	$return = array();
	
	foreach (func_get_args() as $ent)
		$return[] = '`'.$ent.'`';

	return implode(".", $return);
}




function file_name_for_cache($db)
{
	return md5(serialize($db->credentials));
}




function cache_get($key)
{
	$file = "cache/".$key;
	
	if (is_file($file) == false)
		return null;
		
	return unserialize(file_get_contents($file));
}


function cache_set($key, $val)
{

	return file_put_contents("cache/".$key, serialize($val));

}



function cache_clear()
{
	foreach (glob("cache/*") as $file)
		unlink($file);
}






function SingleItemHorizontal($resource, $redest = null, $inlinecolor = "yellow", $hideSimilarities = false)
{
	echo '<table>';
		
	

	
	$availableKeys = ($resource) ? array_keys($resource) : array_keys($redest);
	$rowHeader = "<td></td>";
	$rowOne = "<th>After</th>";
	$rowTwo = "<th>Before</th>";
	

	foreach ($availableKeys as $firstIndex)
	{
	
		
		
			
			
		
		if ( $redest === null )
		{
			$rowOne .= '<td>'.showfield($resource[$firstIndex]).'</td>';
		}
		else if ( is_array($resource) && array_key_exists($firstIndex, $resource) == false )
		{
			$rowOne .=  '<td style="color: black; background-color: '.$inlinecolor.'"><span class="null">not found</span></td>';
			$rowTwo .=  '<td style="color: black; background-color: '.$inlinecolor.'">'.showfield($redest[$firstIndex]).'</td>';
		}
		else if ( is_array($redest) && array_key_exists($firstIndex, $redest) == false )
		{
			$rowOne .=  '<td style="color: black; background-color: '.$inlinecolor.'">'.showfield($resource[$firstIndex]).'</td>';
			$rowTwo .=  '<td style="color: black; background-color: '.$inlinecolor.'"><span class="null">not found</span></td>';
		}
		else if ( $redest[$firstIndex] != $resource[$firstIndex] )
		{
			$rowOne .=  '<td style="color: black; background-color: '.$inlinecolor.'">'.showfielddifference($resource[$firstIndex], $redest[$firstIndex]).'</td>';
			$rowTwo .=  '<td style="color: black; background-color: '.$inlinecolor.'">'.showfielddifference($redest[$firstIndex], $resource[$firstIndex]).'</td>';
		}
		else
		{
			if ($hideSimilarities)
			{
				continue;
			}
			else
			{
				$rowOne .=  '<td>'.showfield($resource[$firstIndex]).'</td>';
				$rowTwo .=  '<td>'.showfield($redest[$firstIndex]).'</td>';
			}
		}
			
		$rowHeader .= '<th>'.$firstIndex.'</th>';
			
	}
	
	if ($rowHeader)
	{
		echo '<tr>'.$rowHeader.'</tr>';
	}

	
	if ($rowTwo)
	{
		echo '<tr>'.$rowTwo.'</tr>';
	}
	if ($rowOne)
	{
		echo '<tr>'.$rowOne.'</tr>';
	}
		
	echo '</table>';

}




function SingleItemVertical($resource, $redest = null, $inlinecolor = "yellow")
{
	echo '<table>';
		
	
	if ( $redest !== null )
	{
		echo '
		<tr>
			<td></td>
			<th>Before</th>
			<th>After</th>
		</tr>';
	
	}
	
	
	$availableKeys = ($resource) ? array_keys($resource) : array_keys($redest);
	

	foreach ($availableKeys as $firstIndex)
	{
		echo '<tr>';
		echo '<th>'.$firstIndex.'</th>';
	
		
		
			
			
		
		if ( $redest === null )
		{
			echo '<td>'.showfield($resource[$firstIndex]).'</td>';
		}
		else if ( is_array($resource) && array_key_exists($firstIndex, $resource) == false )
		{
			echo '<td style="color: black; background-color: '.$inlinecolor.'">'.showfield($redest[$firstIndex]).'</td>';
			echo '<td style="color: black; background-color: '.$inlinecolor.'"><span class="null">not found</span></td>';
		}
		else if ( is_array($redest) && array_key_exists($firstIndex, $redest) == false )
		{
			echo '<td style="color: black; background-color: '.$inlinecolor.'"><span class="null">not found</span></td>';
			echo '<td style="color: black; background-color: '.$inlinecolor.'">'.showfield($resource[$firstIndex]).'</td>';
		}
		else if ( $redest[$firstIndex] != $resource[$firstIndex] )
		{
			echo '<td style="color: black; background-color: '.$inlinecolor.'">'.showfielddifference($redest[$firstIndex], $resource[$firstIndex]).'</td>';
			echo '<td style="color: black; background-color: '.$inlinecolor.'">'.showfielddifference($resource[$firstIndex], $redest[$firstIndex]).'</td>';
		}
		else
		{
			echo '<td>'.showfield($redest[$firstIndex]).'</td>';
			echo '<td>'.showfield($resource[$firstIndex]).'</td>';
		}
			
			
		
		echo '</tr>';
	}
		
	echo '</table>';

}





function nicifyDataSingle($data)
{
	$nicified = array();
	
	
	if ($data)
	{
		foreach ($data as $header => $val)
		{	
			$newheader = ucwords(str_replace("_", " ", strtolower($header)));
			$nicified[$newheader] = ($val);
		}
	
	}
	
	
	
	return $nicified;


}


function nicifyDataDouble($data)
{
	$nicified = array();

	if (is_array($data) && count($data) > 0)
	{
	
		$firstIndexes = array_keys($data);
		$firstIndex = $firstIndexes[0];
		
		$whatis = $data[$firstIndex];
		
		
		if (is_array($whatis))
		{
			
			foreach ($data as $row)
			{
			
				foreach ($row as $header => $val)
				{	
					$newheader = ucwords(str_replace("_", " ", strtolower($header)));
					$newrow[$newheader] = ($val);
					
				}
				
				$nicified[] = $newrow;
			}
		
		}
	}
		
	return $nicified;

}



class DataSetDifferencer
{
	private $diffencing_method;
	private $color_partial_difference;
	private $color_full_difference;
	private $data_source;
	private $data_comparison;
	
	
	
	function set($key, $val)
	{
		$this->$key = $val;
	}
	
	function get($key)
	{
		return $this->$key;
	}

	
	
	
	
	function difference_by_primary_key()
	{
		
	
	
	
	}


}






function differByMap($source, $dest, $map, $primaryKeys)
{

	$headerSource = $source;
	
	
	if (is_array($headerSource) == false)
		return null;
	
	$header = array_shift($headerSource);
	$iterations = 0;

	echo '<table>';
	echo '<tr><td></td>';
	
	foreach ($header as $key => $val)
		echo '<th>'.$key.'</th>';
	
	echo '</tr>';

	
	foreach ($source as $key => $row)
	{
		$buildKey = array();
		
		foreach ($primaryKeys as $pkey)
		{
			$buildKey[] = $row[$pkey];
		}
		
		$fullKey = implode(" ", $buildKey);
	
		$echos = array();
		
		$mapValue = $map[$fullKey];
	
	
		if ($mapValue == "in source" or $mapValue == "in dest")
		{
			$color = "orange";
		}
		else if ($mapValue == "changed")
		{
			$color = "yellow";
		}
		else
		{
			$color = "";
		}
	
		foreach ($row as $secondIndex => $t2)	
		{
		
			
			
			if ( $color )
			{
				$echos[] = '<td style="background-color: '.$color.'; ">'.$t2.'</td>';
			}
			else
			{
				$echos[] = '<td>'.$t2.'</td>';
			}
			
			
		}
		

		echo '<th>'.$iterations.'</th>';
		$values = implode("\n", $echos);
		echo $values;
		echo '</tr>';
		
		$iterations++;	
	}
	
	echo '</table>';
	
}






// Takes a two dimensional array

function differ($resource, $redest, $inlinecolor = "yellow")
{
	
	$headerSource = $resource;
	
	
	if (is_array($headerSource) == false || count($headerSource) == 0)
		return '<p>No data found</p>';
	
	$header = array_shift($headerSource);
	$iterations = 0;

	echo '<table>';
	echo '<tr><td></td>';
	
	foreach ($header as $key => $val)
		echo '<th>'.$key.'</th>';
	
	echo '</tr>';

	foreach ($resource as $firstIndex => $t1)
	{
	
		$echos = array();
		$primaryDifference = false;
		
	
		foreach ($t1 as $secondIndex => $t2)	
		{
			$secondaryDifference = false;
		
		
			if ( $redest === null )
			{
				
			}
			else if ( array_key_exists($firstIndex, $redest) == false )
			{
				$primaryDifference = true;
			}
			else if ( array_key_exists($secondIndex, $redest[$firstIndex]) == false )
			{
				$secondaryDifference = true;
			}
			else if ($redest[$firstIndex][$secondIndex] != $resource[$firstIndex][$secondIndex])
			{
				$secondaryDifference = true;
			}
			
			if ( $secondaryDifference )
			{
				$echos[] = '<td --SECONDARY-->'.$t2.'</td>';
			}
			else
			{
				$echos[] = '<td>'.$t2.'</td>';
			}
			
			
		}
		

		echo '<th>'.$iterations.'</th>';
		$values = implode("\n", $echos);
		
		if ($primaryDifference)
			$values = str_replace( array("<td>", "<td --SECONDARY-->"), '<td style="background-color: orange; ">', $values);
		else
			$values = str_replace( "<td --SECONDARY-->", '<td style="background-color: yellow; ">', $values);
		
		echo $values;
		
		echo '</tr>';
		
		$iterations++;
	}
		
	echo '</table>';
}



// Takes a two dimensional array

function reformshit($source, $dest, $primary)
{

	$newsource = array();
	
	foreach ($source as $key => $val)
	{
		$keyvalue = $val[$primary];
		$newsource[$keyvalue] = $val;
	}
	
	
	$newdest = array();
	
	foreach ($dest as $key => $val)
	{
		$keyvalue = $val[$primary];
		$newdest[$keyvalue] = $val;
	}
	
	
	
	differ($newsource, $newdest);

}





function differenceMerge($results1, $results2)
{
	$both = array_merge($results1, $results2);
	$both = array_unique($both);
//	sort($both);
	
	return $both;
	
	
	$columnsInBoth = array_intersect($results1, $results2);
	
	
	$newMerge = array();
	$secondCurrent = 0; 
	$firstCurrent = 0;
	
	for ($a = 0; $a < count($both); $a++)
	{
	
		$first = isset($results1[$firstCurrent]) ? $results1[$firstCurrent] : null;
		$second = isset($results2[$secondCurrent]) ? $results2[$secondCurrent] : null;
		
		
		if (in_array($first, $columnsInBoth))
		{
			$newMerge[] = $first;
			$firstCurrent++;
			$secondCurrent++;
		}
		else if ($second && in_array($second, $results1) == false)
		{
			$newMerge[] = $second;
			$secondCurrent++;
		}
		else if ($first && in_array($first, $results2) == false)
		{
			$newMerge[] = $first;
			$firstCurrent++;
		}
	}

	
	return $newMerge;

}



function showfield($field)
{
	if ($field === null)
		return '<span class="null">null</span>';
	else if (strlen($field) === 0)
		return ' &nbsp; &nbsp; &nbsp; ';
	else if (strpos($field, "\n") !== false)
		return '<pre>'.htmlentities($field).'</pre>';
		
	return htmlentities(($field));
}





function showfielddifference($field, $field2)
{
	if ($field === null)
		return showfield($field);
	else if (strlen($field) === 0)
		return showfield($field);
	else if (strpos($field, "\n") !== false)
		return '<pre>'.compareFields($field, $field2, "aliceblue").'</pre>';
	else if (is_numeric($field) && is_numeric($field2))
	{
		if ($field > $field2)
		{
			return '<span style="background-color: limegreen; ">'.number_format($field).'</span>';
		}
		else if ($field < $field2)
		{
			return '<span style="background-color: pink; ">'.number_format($field).'</span>';
		}
	}
	else if (strlen($field) > 100 and strlen($field2) > 100)
		return compareFields($field, $field2, "aliceblue");
		
	return showfield($field);
}




function compareFields($displays, $compares, $highlight)
{
	$str1 = $displays;
	$str2 = $compares;

    $len1 = mb_strlen($str1);
    $len2 = mb_strlen($str2);
   
    // strip common prefix
    $i = 0;
    do {
        if(mb_substr($str1, $i, 1) != mb_substr($str2, $i, 1))
            break;
        $i++;
        $len1--;
        $len2--;
    } while($len1 > 0 && $len2 > 0);
    if($i > 0) {
        $str1 = mb_substr($str1, $i);
        $str2 = mb_substr($str2, $i);
    }
   
    // strip common suffix
    $i = 0;
    do {
        if(mb_substr($str1, $len1-1, 1) != mb_substr($str2, $len2-1, 1))
            break;
        $i++;
        $len1--;
        $len2--;
    } while($len1 > 0 && $len2 > 0);
    if($i > 0) {
        $str1 = mb_substr($str1, 0, $len1);
        $str2 = mb_substr($str2, 0, $len2);
    }	
	

	if ($str1)
		$firstOccurence = strpos($displays, $str1);
	else
		$firstOccurence = 0;
	
	$sameFormer = substr($displays, 0, $firstOccurence);
	$sameLatter = substr($displays, $firstOccurence + strlen($str1));

	
	return htmlentities($sameFormer).'<span style="background-color: aliceblue; ">'.htmlentities($str1).'</span>'.htmlentities($sameLatter);
}

function similarityArrays($string1, $string2)
{

	$disp = (breakIntoArray($string1));
	$comp = (breakIntoArray($string2));
	
	$stringer = "";
	
	
	print_pre($disp); 
	print_pre($comp); 
	
	$conclusion = array();
	
	$foundkey = null;
	$comparisonKey = 0;
	
	foreach ($disp as $key => $value)
	{
	
		echo $comparisonKey.' '.$key.' | ';
		if (isset($comp[$comparisonKey]) && $comp[$comparisonKey] == $value)
		{
			$conclusion[$key]['same'] = $value;
		}
		else
		{
			$foundkey = array_search($value, $comp);
			
			if ($foundkey)
			{
				$comparisonKey = $foundkey;
				$conclusion[$comparisonKey]['shift'] = $value;
			}
			else
			{
				$conclusion[$comparisonKey]['new'] = $value;
			
			}
		
		}
	
		$comparisonKey++;
	
	
	}
	
	
	print_pre($conclusion); 
	return $stringer;


}



function breakIntoArray($string1)
{
	$arrayOne = array();
	
	$buffer = "";
	$lastCharIsSpace = null;
	$lastChar = null;


	for ($index = 0; $index < strlen($string1); $index++)
	{
		$value = substr($string1, $index, 1);
		$isSpace = preg_match('/\s+/', $value);
		$changeDetected = ($isSpace != $lastCharIsSpace);
		
//		echo 'value: '.$value.' isspace: '.$isSpace.' lastcharisspace: '.$lastCharIsSpace.' changedetected: '.$changeDetected."\n\n";
		
		
		if ($changeDetected)
		{
		
			$arrayOne[] = $buffer;
			$buffer = "";
			$buffer .= $value;
		}
		else
		{
			$buffer .= $value;
		
		}
		
		$lastChar = $value;
		$lastCharIsSpace = $isSpace;
	
	
	}
	
	$arrayOne[] = $buffer;

	return $arrayOne;

}





function extractColumnFromResult($result, $column)
{
	return Arrays::extractColumn($result, $column);
}


function linkermaker($text, $link, $src, $dest, $tooltip)
{
	if ($src != $dest)
		return '<a title="'.$tooltip.'" href="'.$link.'">'.$text.'</a>';

	return $text;
}



function getdb($type, $session)
{
	static $dbarray;
	
	$server = $session->get($type."_server");
	$user = $session->get($type."_user");
	$pass = $session->get($type."_password");
	$data = $session->get($type."_database");
	
	$connstring = $server.$user.$pass.$data;
	
	if (empty($dbarray[$connstring]))
	{
		$dbarray[$connstring] = new DBO_MySQLi($server, $user, $pass, $data);
	}
		
	return $dbarray[$connstring];


}



function grabTitle($dbside, $session)
{

			$server = $session->get($dbside."_server");
			$data = $session->get($dbside."_database");

	return $data." @ ".$server;
}



