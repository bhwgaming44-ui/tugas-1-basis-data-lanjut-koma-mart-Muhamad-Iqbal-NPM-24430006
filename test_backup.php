<?php
$f = fopen("backup-sebelum-erd.sql", "r");
while (($line = fgets($f)) !== false) {
    if (stripos($line, "barangs") !== false && stripos($line, "INSERT") !== false) {
        echo substr($line, 0, 400) . "\n";
        break;
    }
}
fclose($f);
