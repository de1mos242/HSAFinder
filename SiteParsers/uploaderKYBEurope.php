<?php
include("web_request.php");

function getMultiJson($data) {
	$multiJSON = mb_split('},{', mb_ereg_replace('\[|]', "", $data));
		$result = array();
	//	echo "multiJSON = $multiJSON";
		foreach ($multiJSON as $key => $value) {
			//echo "value = $value\n";
			$workVal = mb_ereg_replace('^{|}$', "", $value);
			if (mb_ereg("{", $workVal) && !mb_ereg('}', $workVal))
				$workVal .= '}';
			//echo "work value = { $workVal }\n";
			$obj = json_decode("{".$workVal."}", true);
			//echo "obj = $obj; but val = {".$workVal."}"."\n";
			if ($obj)
				$result[] = $obj;
		}
		return $result;
}

function getArrayByRequest($request) {
	$html = "";
	while ($html == '' || !mb_ereg(';$', $html)) {
		$html = web_request('GET', '85.233.166.94', 80, $request, array('lang'=>4,'callback'=>'jsonp1336902263777','_'=>'1336902264123'));
	}
	//echo "$html\n";
	// parse details
	if (mb_eregi("fa:\[", $html)) {
		//echo "check\n";
		$result = array();
		$json = mb_ereg_replace("^[\w\d]*\({|}\);$", "", $html);
		//echo "clean json = $json\n";
		if (mb_eregi("], ra:\[", $json)) {
			$strings = mb_split("], ra:\[", $json);
			//front
			$frontString = mb_ereg_replace("fa:\[", "", $strings[0]);
			//echo "decode front = $frontString\n";
			$result['FRONT'] = getMultiJson($frontString);
			//rear
			$rearString = mb_ereg_replace("]", "", $strings[1]);
			//echo "decode rear = $rearString\n";
			$result['REAR'] = getMultiJson($rearString);
		}
	}
	elseif (mb_ereg('\[{.*}]', $html, $innerData)) {
		$result = getMultiJson($innerData[0]);
	}
	else 
	{
		mb_ereg('{.*}', $html, $data);
		$result = json_decode($data[0], true);
	}
	if (!$result && $html != 'xxx([]);' && $html != '')
		echo "failse: $html\n";
	return $result;
}

$file = fopen("/home/de1mos/kybSiteParse.csv", "w+");
$marks = getArrayByRequest('/spares/manufacturers/');
foreach ($marks as $markName => $markId) {
	$counter = 0;
	//echo "$markName\n";
	$models = getArrayByRequest("/spares/models/$markId/");
	if (is_null($models)) continue;
	foreach ($models as $modelName => $ModelId) {
		//echo "    $modelName = $ModelId\n";
		$items = getArrayByRequest("/spares/data/$ModelId/");
		//echo "items = $items\n";
		//print_r($items);
		if (is_null($items)) continue;
		foreach ($items as $itemValue) {
			$detailsId = $itemValue['type_no'];
			//echo "$markName - $modelName - " . $itemValue["title"] ." = ".$itemValue['type_no']."\n";
			$details = getArrayByRequest("/spares/detail/$detailsId/");
			//var_dump($details);
			foreach ($details as $type => $detailsValue) {
				foreach ($detailsValue as $detailsData) {
					$detailsNubmer = $detailsData["art_no"];
					$detailsTitle = $detailsData['title'];
					$artInfo = $detailsData['art_info'];
					//echo "$markName , $modelName :\n";
					//var_dump($detailsData);
					//break;
					$counter++;
					$itemArray = array($markName,$modelName,
						$itemValue['title'],$itemValue['start'],$itemValue['end'],
						$detailsData['art_no'],$detailsData['title'],
						$artInfo['Fitting Position']);
					if (isset($artInfo['Shock Absorber System']))
						$itemArray[] = $artInfo['Shock Absorber System'];
					else
						$itemArray[] = '';
					if (isset($artInfo['hock Absorber Type']))
						$itemArray[] = $artInfo['Shock Absorber Type'];
					else
						$itemArray[] = '';
					if (isset($artInfo['Shock Absorber Design']))
						$itemArray[] = $artInfo['Shock Absorber Design'];
					else
						$itemArray[] = '';
					if (isset($artInfo['To construction year']))
						$itemArray[] = $artInfo['To construction year'];
					else
						$itemArray[] = '';
					fputcsv($file, $itemArray);
					echo "  $counter entries\r";
					//var_dump($detailsData);
					//echo "type = $type: $detailsNubmer - $detailsTitle\n";
				}
				
				//var_dump($itemValue);
			}
		}
	}
}

fclose($file);
?>