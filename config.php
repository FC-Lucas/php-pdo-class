<?php
function __autoload($Class) {

	$cDir = array('DAL', 'BLL', 'Entity', 'Exception', 'Crud', 'Helpers');

	$iDir = null;

	foreach ($cDir as $dirName){
		if (!$iDir && file_exists(__DIR__."/{$dirName}/{$Class}.class.php")){
			include_once (__DIR__."/{$dirName}/{$Class}.class.php");
			$iDir = true;
		}
	}

	if (!$iDir){
		trigger_error("Não foi possível incluir {$Class}.class.php", E_USER_ERROR);
		die;
	}
}

define('MSG_ACCEPT', 'accept');
define('MSG_INFOR', 'infor');
define('MSG_ALERT', 'alert');
define('MSG_ERROR', 'error');


function MSG($ErrMsg, $ErrNo, $ErrDie = null) {
	$CssClass = ($ErrNo == E_USER_NOTICE ? MSG_INFOR : ($ErrNo == E_USER_WARNING ? MSG_ALERT : ($ErrNo == E_USER_ERROR ? MSG_ERROR : $ErrNo)));
	echo "
		<p class=\"message {$CssClass}\">
		<script>setTimeout(function(){ $('.{$CssClass}').slideUp(\"slow\"); },20000)</script>
		<span class=\"msgicon\"></span>{$ErrMsg}<span class=\"close\">X</span></p>
	";

	if ($ErrDie):
		die;
	endif;
}

function CSS($code){
    echo "<style type='text/css'>{$code}</style>";
}


function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
	$CssClass = ($ErrNo == E_USER_NOTICE ? MSG_INFOR : ($ErrNo == E_USER_WARNING ? MSG_ALERT : ($ErrNo == E_USER_ERROR ? MSG_ERROR : $ErrNo)));
	echo "<p class=\"message {$CssClass}\">";
	echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
	echo "<small>{$ErrFile}</small>";
	echo "<span class=\"ajax_close\"></span></p>";

	if ($ErrNo == E_USER_ERROR):
		die;
	endif;
}
set_error_handler('PHPErro');
