<?
function chk_array($array,$key) {
	// Verifica se a chave existe no array
	if (isset($array[$key]) && !empty($array[$key]))
		return $array[$key];
	return null;
}

/*
http://php.net/manual/pt_BR/function.autoload.php.
 * Classes estão na pasta classes/.
 * O nome do ficheiro deverá ser class-NomeDaClasse.php.
 Por exemplo: para a classe System, o ficheiro chamar-se class-System.php
*/
function my_autoloader($class_name) {
	$file = ABSPATH.'/classes/class-'.$class_name. '.php';
	if ( !file_exists($file)) {
		require_once ABSPATH.'404.php';
		return;
	}
	
	// inclui o ficheiro da classe
	require_once $file;
}
spl_autoload_register('my_autoloader');
?>