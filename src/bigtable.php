<?php
require '../vendor/autoload.php';
require 'auth-cache.php';

use Google\Cloud\Bigtable\BigtableClient;

$project_id	= $_GET['project'];
$table_id	= $_GET['table'];
$instance_id	= $_GET['instance'];
$key		= $_GET['key'];
$cache_auth     = $_GET['cache_auth'] ?? false;
$num_requests   = isset($_GET['num_requests']) ? intval($_GET['num_requests']) : 1;

$config = [
  'projectId' => $project_id,
];
if ($cache_auth) {
   $config['credentialsConfig'] = [ 'authCache' => new YacItemPoolCache() ];
}

$bigtable = new BigtableClient($config);
$table = $bigtable->table($instance_id, $table_id);

$request_uuid = uniqid();

while ($num_requests--) {
  try {
    $start_time = microtime(true);
    $row = $table->readRow($key);
  } catch (Exception $e) {
    die($e);
  }

  printf("pid %d\tuuid %s\tkey %s\tvalue %.10s...\ttime %.2f ms\n",
    getmypid(), 
    $request_uuid,
    $key,
    json_encode($row),
    (microtime(true) - $start_time) * 1000 );
}
