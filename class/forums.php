<?php

/*
 *
 * TODO: ALL
 *
 */

class Forum {
	function __construct() {
		include_once('db.php');
		include("config.php");
		$this->db = new MySQL($date['db_host'], $date['db_name'], $date['db_user'], $date['db_password']);
	}

	function show_forums(){
		echo "<div id='forums'>";
		$cat_query = "SELECT * FROM categories";
		$result_cat = $this->db->sendQuery($cat_query);

		while ($result = mysql_fetch_array($result_cat)) {
			$id = $result['id'];
			echo "<div class='hn'>" . $result['name'] . "<br></div><br>";
			$query = "SELECT * FROM forums WHERE category = '$id'";
			$res = $this->db->sendQuery($query);
			while($row = mysql_fetch_array($res)) {
				$section_id = $row['id'];
				echo "<a href='section.php?id=$section_id'>" . $row['name'] ."</a><br><br>";
			}
			echo "<br>";
		}
		echo "</div>";
	}
}

?>
