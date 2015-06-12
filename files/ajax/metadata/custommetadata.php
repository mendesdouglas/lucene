<?php 

function getCustomMetadataKeys($type) {
    switch ($type) {
        case 'pdf':
        case 'application/pdf':
            return array('title', 'subject', 'author', 'creator', 'format', 'keywords');
            break;

        case 'audio/mpeg3':
        case 'audio/x-mpeg-3':
        case 'audio/mpeg':
            return array('title', 'artist', 'album', 'year', 'gender', 'keywords');
        break;

        case 'text/plain':
            return array('title', 'subject', 'author', 'creator', 'format', 'keywords');
            break;

        default:
            # code...
            break;
    }
}

function forceMetadataExtract($file) {

    $tika = __DIR__ . "/./../tika/tika-app-1.8.jar";
    //$tika = "/Users/adirkuhn/Projetos/b3/owncloud/apps/files/tika/tika-app-1.8.jar";

    $cmd = "java -jar {$tika} -j " . $file;
    //echo $cmd;
    $ret = shell_exec($cmd);

    return json_decode($ret, true);
}

function getFileId($view, $file) {
    
    $fileInfo = $view->getFileInfo($file);

    if (!empty($fileInfo)) {
        return $fileInfo->getId();
    }

    return null;
}

function indexFile($app, $fileId, $metadata = null) {

    $container = $app->getContainer();
    $indexer = $container->query('Indexer');
    $index = $container->query('Index');
    $indexer->removeFileIndex($fileId);
    $indexer->indexFiles(array($fileId), null, $metadata);
}

function searchFile($app, $fileId) {

    $container = $app->getContainer();
    $indexer = $container->query('Indexer');
    $index = $container->query('Index');
    $r = $index->find('fileId:' . $fileId);

    if (isset($r[0])) {
        return $r[0];
    }

    return null;
}