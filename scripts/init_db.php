<?php

if (!isset($argv[1])) {
  echo "You must provide a scheme no.\n";
  exit(0);
}

$schemeNo = $argv[1];
$schemeFile = __DIR__."/sql/schema-{$schemeNo}.sql";

if (!file_exists($schemeFile)) {
  echo "$schemeNo is not a valid schema number.\n";
  exit(0);
}

include __DIR__.'/../dependencies.php';

$host = sems_config('db.host');
$db = sems_config('db.db');
$user = sems_config('db.user');
$pwd = sems_config('db.pwd');

echo "About to execute the script $schemeFile on database $host@$db with user $user.\n";
echo "WARNING: This will completely overwrite the data on $db. Are you sure you wish to continue? (Y/n) ";

$stdin = fopen('php://stdin', 'r');
$yn = fgets($stdin);
fclose($stdin);

if (trim($yn) != 'Y') {
  echo "Cancelling operation.\n";
  exit(0);
}


$cmd = "mysql -h " . escapeshellarg($host) . " -u" . escapeshellarg($user) . " -p" . escapeshellarg($pwd) . " " . escapeshellarg($db) . " < " . escapeshellarg($schemeFile);
echo "Executing $cmd...\n";

passthru($cmd, $exit);

echo "Command exited with code $exit.\n";
exit($exit);

?>
