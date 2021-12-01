<?php

/** 新計算應上班時間 **/
function buildTimeBar($start="08:37",$end="18:00"){
	$result = [$start,$end];

	#預設午休時間
	$this->diffSet($result,'12:00','13:30');

	return $result;
}
/** 扣除請假時間 **/
function diffSet(& $set,$diff_set_start,$diff_set_end,$cover_set=false){
	$start = min($diff_set_start,$diff_set_end);
	$end   = max($diff_set_start,$diff_set_end);


	$includeTime = [];
	$unsetKey = [];

	# 區間兩兩成對，故偶數代表在需上班區間中，奇數代表於已請假區間
	foreach ($set as $key => $value) {

		# 起使時間 in需上班時間，則納入做新區間
		if( $set[$key] < $start && $start < $set[$key+1] ){
			if($key % 2 == 0)
				array_push($includeTime,$start);
		}

		# 結束時間 in需上班時間，則納入做新區間
		if( $set[$key] < $end && $end < $set[$key+1] ){
			if($key % 2 == 0)
				array_push($includeTime,$end);
		}

		# 請假時間移除
		if( $start <= $value && $value <= $end ){
			if($cover_set && $start == $value){
				continue;
			}
			array_push($unsetKey, $key);
		}
	}
	foreach ($unsetKey as $key) {
		unset($set[$key]);
	}

	# 請假起訖超過上班時間不納入
	foreach ($includeTime as $key => $value) {
		if($value>="18:00" || $value<="08:37")
			continue;
		array_push($set, $value);
		$set = array_unique($set);
	}

	array_multisort($set);
}
function demoTimeBar(){
	$workBar = $this->buildTimeBar();
	print_r($workBar);
	
	# 假單1 09:30~11:00
	$this->diffSet($workBar,'08:00','12:00');

	# 假單2 13:30~14:30
	$this->diffSet($workBar,'13:30','18:00');

	$should_clock_in  = min($workBar); //應上班打卡時間
	$should_clock_out = max($workBar); //應下班打卡時間

	print_r($workBar);
	print("20210927 08:39" > "20210927 08:00:32");
	// print(date("H:i:s",strtotime("20210927 07:39")));
}

?>