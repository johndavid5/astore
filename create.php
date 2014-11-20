<?php
require_once("security.php");
?><p>Creating username='joes', password='Aa.1', firstname="Joseph", lastname="Smith"...</p><?php
Security::new_account("joes", "Aa.1", "Joseph", "Smith");
?><p>Creating username='joeko', password='Aa.1', firstname="Joseph", lastname="Kovacs"...</p><?php
Security::new_account("joeko", "Aa.1", "Joseph", "Kovacs");
?>
