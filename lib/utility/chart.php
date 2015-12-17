<?php
include_once '_approot.php';

/**
 * 图表颜色
 *
 * @var unknown
 */
$CHART_COLOR_ARRAY = array (
		'#CC3333',
		'#FF9900',
		'#FFFF33',
		'#CCFF99',
		'#339933',
		'#339999',
		'#336666',
		'#0099CC',
		'#6666CC',
		'#9966CC',
		'#CC3399',
		'#006600',
		'#003366',
		'#F00000' 
);

/**
 * 图表高亮颜色
 *
 * @var unknown
 *
 */
$CHART_HIGHLIGHT_ARRAY = array (
		'#FF6666',
		'#FFCC33',
		'#FFFF99',
		'#CCFFCC',
		'#99CC00',
		'#66CCCC',
		'#669999',
		'#99CCFF',
		'#9999FF',
		'#CC99CC',
		'#FF99CC',
		'#66CC66',
		'#006699',
		'#FF0033' 
);

$colorIndex = 0;

$CHART_MONTH_ARRAY = array (
		'1月',
		'2月',
		'3月',
		'4月',
		'5月',
		'6月',
		'7月',
		'8月',
		'9月',
		'10月',
		'11月',
		'12月' 
);

/**
 * 将统计数据转为饼状图数据
 *
 * @param unknown $rows        	
 * @param unknown $labelFieldName        	
 * @param unknown $valueFieldName        	
 */
function get_pin_chart_array($rows, $labelFieldName, $valueFieldName) {
	global $CHART_COLOR_ARRAY;
	global $CHART_HIGHLIGHT_ARRAY;
	
	$colorLength = count($CHART_COLOR_ARRAY);
	global $colorIndex;
	
	$dataList = array ();
	foreach ($rows as $row) {
		$data['value'] = $row[$valueFieldName];
		$data['label'] = $row[$labelFieldName] . ': ' . number_format($row[$valueFieldName], 2);
		$data['color'] = $CHART_COLOR_ARRAY[$colorIndex];
		$data['highlight'] = $CHART_HIGHLIGHT_ARRAY[$colorIndex];
		
		$dataList[] = $data;
		
		$colorIndex ++;
		if ($colorIndex == $colorLength) {
			$colorIndex = 0;
		}
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
function get_bar_chart_array($dataSet, $barLabelArray, $valueLabelArray, $valueFieldName) {
	global $CHART_COLOR_ARRAY;
	global $CHART_HIGHLIGHT_ARRAY;
	
	$colorLength = count($CHART_COLOR_ARRAY);
	global $colorIndex;
	
	//$colorIndex = rand(0, $colorLength - 1);
	
	$dataSetList = array();
	$rowIndex = 0;
	foreach ($dataSet as &$dataList) {
		$data = array();
		foreach ($dataList as &$row) {
			
			$data[] = (float)$row[$valueFieldName];
		}
		$dataSetList[] = array(
				'label' => $valueLabelArray[$rowIndex++],
				'fillColor' => $CHART_COLOR_ARRAY[$colorIndex],
				'strokeColor' => $CHART_COLOR_ARRAY[$colorIndex],
				'highlightFill' => $CHART_HIGHLIGHT_ARRAY[$colorIndex],
				'highlightStroke' => $CHART_HIGHLIGHT_ARRAY[$colorIndex],
				'data' => $data
		);
		$colorIndex ++;
	}
	$chartSet[] = array(
			'labels' => $barLabelArray,
			'datasets' => $dataSetList
	);
	return $chartSet[0];
}

?>