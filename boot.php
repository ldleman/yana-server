<?php
echo 'Installation dou bouzin ...'.PHP_EOL;
exec('crontab -l | { cat; echo "*/1 * * * * php '.__DIR__.'/action.php crontab -O /dev/null 2>&1"; } | crontab -');
exec('crontab -l | { cat; echo "@reboot '.__DIR__.'/nerve '.__DIR__.'/action.php -O /dev/null 2>&1"; } | crontab -');
exec('crontab -l | { cat; echo "@reboot php '.__DIR__.'/action.php crontab -O /dev/null 2>&1"; } | crontab -');
echo 'Tout est ok chef! :D'.PHP_EOL;
?>