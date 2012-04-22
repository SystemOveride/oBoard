<?php

function secureVar ($var) {
	return mysql_real_escape_string (htmlentities (stripslashes($var)));
}

function _crypt ($var) {
	return sha1 (md5 ($var));
}

?>