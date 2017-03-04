<?php
function path2url($file, $Protocol='http://') {
    if(isset($file) || !empty($file)) {
        return $Protocol . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
    }
    else return "";
}
?>
