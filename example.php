<?php

require('IniParser.class.php');

$ini = new IniParser();

$user = $ini->file_get('Eduardo');
echo $user['money'];
