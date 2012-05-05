<?php

/*
 *
 * Forums Functions
 *
 */

function show_forums () {
	$header = new Engine("header.tp");
	$index = new Engine("index.tp");
	$cat = new Engine("cat.tp");
	$forum = new Engine("forum.tp");
	$footer = new Engine("footer.tp");
	
	$header->fill('title','Home');
	
	echo $header->render();
	echo $index->render();
	
	
	$conn = new MySQL();
	$res_cat = $conn->query("SELECT * FROM categories");
	
	while ($row = mysql_fetch_assoc($res_cat)) {
		$id = $row['id'];
		$cat = new Engine("cat.tp");
		$cat->autofill("SELECT name FROM categories WHERE id = '$id'");
		$sect = new Engine("forum.tp");
		$sect->autofill_iter("SELECT forum_name FROM forums WHERE category = '$id'");
		$cat->append($sect);
		echo $cat->full_render();
	}
	
	echo $footer->render();

}

?>
