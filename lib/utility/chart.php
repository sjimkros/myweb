<?php
include_once '_approot.php';

/**
 * 将统计数据转为饼状图数据
 *
 * @param unknown $rows        	
 * @param unknown $labelFieldName        	
 * @param unknown $valueFieldName        	
 */
function get_pin_chart_array($rows, $labelFieldName, $valueFieldName) {
	$dataList = array ();
	foreach ($rows as $singleRow) {
		$dataList[] = array($singleRow[$labelFieldName], (float)$singleRow[$valueFieldName]);
	}
	return $dataList;
}

/**
 * 将统计数据转为柱状图
 * 
 * @param unknown $rows        	
 * @param unknown $labelArray        	
 * @param unknown $valueFieldName        	
 */
function get_bar_chart_array($rows, $valueFieldName) {
	$dataList = array ();
	
	foreach ($rows as $singleRow) {
		

		$dataList[] = (float)$singleRow[$valueFieldName];
		
	}
	
	return $dataList;
}



?>