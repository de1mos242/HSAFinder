<?php
include("web_request.php");

function getMarksAndModels() {
	$result_array = array();
	$rawData = request('/ru/tokico');
	$prefix = "\<td\>\<a href=\"javascript\: newwin1\('";
	$suffix = "'\)\"";
	//preg_match_all("/\<td\>\<a href=\"javascript\: newwin1\([^\)]*\)\"/", $rawData,$results) . "\n";
	preg_match_all("/".$prefix."[^\)]*".$suffix."/", $rawData, $results);
	foreach ($results[0] as $value) {
		$line = mb_ereg_replace($suffix.'|'.$prefix, "", $value);
		$parts = mb_split("', '", $line);
		$result_array[] = array("mark" => $parts[0], "model" => $parts[1]);
	}
	return $result_array;
}

function parseModel($rawData) {
	$result = array();
	$rawData = mb_ereg_replace("\n|\r|\t", "", $rawData);
	//echo "$rawData\n";
	$prefix = "\<tr\>[\s]*\<td\>\<div align=\"center\">";
	$suffix = "\<\/tr\>";
	preg_match_all("/".$prefix.".*?".$suffix."/", $rawData, $results);
	foreach ($results[0] as $value) {
		$line = mb_ereg_replace($suffix.'|'.$prefix, "", $value);
		$line = preg_replace("/<[^t\/].*?>/", "", $line);
		$line = preg_replace("/<\/[^t].*?>/", "", $line);
		$cells = mb_split("<\/td>[\s]*<td>", $line);
		foreach ($cells as $key => $cellValue) {
			$cellValue = preg_replace("/&nbsp;|<\/td>/", "", $cellValue);
			$cellValue = trim($cellValue);
			$cells[$key] = $cellValue;
		}
		$result[] = array(
			'year'=>$cells[0], 
			'body' => $cells[1],
			'front oil' => $cells[2],
			'front gas' => $cells[3],
			'front oems' => $cells[5],
			'rear oil' => $cells[6],
			'rear gas' => $cells[7],
			'rear oems' => $cells[9]
			);
		//$parts = mb_split("', '", $line);
		//$result_array[] = array("mark" => $parts[0], "model" => $parts[1]);
	}
	return $result;
}

function request($uri, $getData = array()) {
	$data = web_request('GET', 'www.infodozer.com', 80, $uri, $getData);
	for ($counter = 1;$data == '';$counter++)
	{
		echo "retry $uri";
		for ($i = 0;$i<$counter;$i++)
			echo ".";
		echo "\n";
		$data = web_request('GET', 'www.infodozer.com', 80, $uri, $getData);
	}
	return $data;
}

function readModel($mark, $model) {
	$rawData = request('/catalogs/tokico/search.php', array("model" => $model, "maker" => $mark));
	$parsedModel = parseModel($rawData);
	//echo "parsed $mark $model\n";
	return $parsedModel;
	//echo "raw data = $rawData";
}

$file = fopen("/home/de1mos/tokikoSiteParse.csv", "w+");
$start = date("r");
$counter = 0;
$res = getMarksAndModels();
foreach ($res as $key => $value) {
	//echo "mark = ".$value["mark"] . "; model = " . $value["model"]."\n";
	$modelData = readModel($value["mark"], $value["model"]);
	foreach ($modelData as $data) {
		$counter++;
		$itemArray = array($value["mark"],$value["model"],
						$data['body'],$data['year'],
						$data['front oil'], $data['front gas'], $data['front oems'],
						$data['rear oil'], $data['rear gas'], $data['rear oems']);
		fputcsv($file, $itemArray);
		echo "  $counter entries\r";
	}
}
$end = date("r");
echo "started at $start\nended at $end\n";
fclose($file);    


?>