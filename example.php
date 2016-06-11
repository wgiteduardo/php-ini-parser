<?php

require('IniParser.class.php');

$ini = new IniParser();

$user = $ini->GetFileContent('Eduardo');
echo $user['money'];
