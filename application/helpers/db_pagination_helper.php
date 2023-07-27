<?php
	function get_page($sql,$driver,$start,$end)
	{
		$listdata = array();
		switch($driver) :
			case "mysql" :  $listdata = mysql_new_query($sql,$start,$end); break;
			case "mssql" :  $listdata = mssql_new_query($sql,$start,$end); break;
			case "sqlsrv" :  $listdata = mssql_new_query($sql,$start,$end); break;
			case "oci8" :   $listdata = oci8_new_query($sql,$start,$end); break;
		endswitch;
		return $listdata;
	}

	function mysql_new_query($sql,$start,$end)
	{
		if($start==1)
			$start = 0;
		else 
			$start = $start - 1;
		$start = $start * $end;
		$sqlstr = $sql." LIMIT $start,$end";
		return $sqlstr;
	}

	function oci8_new_query($sql,$start,$end)
	{
		$start = ($start<=1) ? 0 : ($start+1);
		$end = $start+$end;
		$sqlstr = "SELECT * FROM (SELECT a.*, ROWNUM rn FROM ($sql) a WHERE ROWNUM <= $end) WHERE rn >= $start";
		return $sqlstr;
	}

	function mssql_new_query($sql,$start,$end)
	{
		  $start = ($start<=1) ? 0 : ($start+1);
		  $end = $start+$end;
		  $sql = substr($sql,6);
		  list($select,$order) = explode('ORDER BY',$sql);
		  $order = str_replace(", ",",",$order);
		  list($field,$orderby) = explode(" ",trim($order));
		  $sqlstr  = "WITH newquery AS(SELECT row_number() OVER (ORDER BY $field $orderby) AS RowNo,$select)
		SELECT * FROM newquery WHERE RowNo Between $start and $end";
		return $sqlstr;
	}

	function setDateformat($date)
	{
		if($date=="") {
			return null;
		} else {
			@list($d,$m,$y) = explode("/",$date);
			return "$y-$m-$d";
		}
	}

	function setDatetimeformat($datetime)
	{
		if($datetime=="") {
			return null;
		} else {
			@list($date,$time) = explode(" ",$datetime);
			@list($d,$m,$y) = explode("/",$date);
			@list($h,$i) = explode(":",$time);
			return "$y-$m-$d $h:$i:00";
		}
	}

	function getDateformat($datetime)
	{
	  if(strpos($datetime,'/')===false) {
	    return date("d/m/Y",strtotime($datetime));
	  } else {
	    return $datetime;
	  }
	}

	function getDatetimeformat($datetime)
	{
	  if(strpos($datetime,'/')===false) {
		return date("d/m/Y H:i",strtotime($datetime));
	  } else {
	    return $datetime;
	  }
	}
?>