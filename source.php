<?
require_once('pachube.lib.php');

$api_key = "WfbokzWWHhqIyQGlFm2nlCmiHW7ulqHzuTlxXru8Sjo";
$pachube = new Pachube($api_key);

function getRowData($data)
{
	$data_count = count($data);
	$final = array();
	for ($i = 0, $c = 0; $i < $data_count; $i++) 
	{
		$a = 0;
		for($j = 0; $j < $data_count; $j++)
		{
			if($data[$j]->date == $data[$i]->date)
			{
				$array_id = (int)substr($data[$j]->date,-2);
				$final[$array_id] += $data[$j]->value;
				$c++; $a++;
			}
		}
		$final[$array_id] = $final[$array_id]/$a;
		$i = $c;
	}
	return $final;
}

// ToDo: Multistreams
function getJSONString($area_code = "", $area_name = "", $stream = "")
{
	global $pachube;
	$data = $pachube->retrieveArchive($stream);
	$rowData = getRowData($data);
	
	$area_code = strtoupper($area_code);
	$min = array_keys($rowData); $min = $min[0];
	$max = max(array_keys($rowData));
	
	$output = "'$area_code': {name:'$area_name', rate:[";
	for($i = $min; $i < $max; $i++)
	{
		$output .= (string)$rowData[$i];
		if($i != $max-1)
		{
			$output .= ",";
		}
	}
	$output .= "]}";
	return $output;
}

function getMinMax($stream = "")
{
	global $pachube;
	$data = $pachube->retrieveArchive($stream);
	$rowData = getRowData($data);

	$min = array_keys($rowData); $min = $min[0];
	$max = max(array_keys($rowData));
	
	return array($min, $max);
}

function getBulkStrings($data)
{
	$data_count = count($data);
	$output = "var uk_stats = {";
	
	for($i = 0; $i < $data_count; $i++)
	{
		$output .= getJSONString($data[$i][0],$data[$i][1],$data[$i][2]);
		if($i != $data_count-1)
		{
			$output .= ",";
		}
	}
	$output .= "};";
	
	$max = getMinMax($data[0][2]);
	$min = $max[0];
	$max = $max[1];
	$output .= "uk_stats.maxLevel = $max;";
	$output .= "uk_stats.minLevel = $min;";
	$output .= "uk_stats.levelIdx = function(level) { return level-$min-1; }";
	
	return $output;
}

$data = array(
	array(
		"E", "England", "http://www.pachube.com/feeds/7049/datastreams/0/archive.csv"
	),
	array(
		"S", "Scotland", "http://www.pachube.com/feeds/12398/datastreams/100/archive.csv"
	),
	array(
		"W", "Wales", "http://www.pachube.com/feeds/1603/datastreams/0/archive.csv"
	),
	array(
		"NI", "Northern Ireland", "http://www.pachube.com/feeds/6428/datastreams/1/archive.csv"
	)
);

echo getBulkStrings($data);
?>