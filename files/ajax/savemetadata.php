<?php
use OC\Files\View;
use OCA\Search_Lucene\AppInfo\Application;
use OCA\Search_Lucene\Core\Logger;

require __DIR__ . '/metadata/custommetadata.php';

OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();

// Get data
$datadir = \OC_User::getHome(\OC_User::getUser()) . '/files';
$userdir = '/' . \OC_User::getUser() . "/files";
$dir = isset($_POST['dir']) ? $_POST['dir'] : '';
$dir = \OC\Files\Filesystem::normalizePath($dir);
$metadata = $_POST['metadata'];

$userdir = $userdir . $dir;

$file = isset($_POST['file']) ? $_POST['file'] : '';

$path = $datadir . $dir . $file;

$fileInfo = \OC\Files\Filesystem::getFileInfo($file);

$app = new Application();
$view = new View($dir);

$response = array();
if (file_exists($path)) {

    $response['fileid'] = $fileInfo->getId();

    indexFile($app, $response['fileid'], $metadata);

    OCP\JSON::success(array('data' => $response));
}
else {
    OCP\JSON::error(array('data' => 'file do not exists'));
}