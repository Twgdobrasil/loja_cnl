<?php 
/*$link = mysql_connect('localhost', 'clicknal_newuser', 'cnlnew');
if (!$link) {
    die('Falha ao conectar ao banco de dados: ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db('clicknal_new', $link);
if (!$db_selected) {
    die ('Erro ao acessar banco... : ' . mysql_error());
}*/
$link = mysql_connect('localhost', 'clicknal_ljuser', 'Twg497339#');
if (!$link) {
    die('Falha ao conectar ao banco de dados: ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db('clicknal_loja', $link);
if (!$db_selected) {
    die ('Erro ao acessar banco... : ' . mysql_error());
}
?> 

