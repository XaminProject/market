<?php 
date_default_timezone_set('GMT');

//1- file we want to serve : 
$data_file = "/home/f0rud/Aalto Talk with Linus Torvalds [Full-length].mp4";

$data_size = filesize($data_file); //Size is zero base

$mime = 'application/otect-stream'; //Mime type of file. to begin download its better to use this.
$filename = basename($data_file); //Name of file, no path included

//2- Check for request, is the client support this method?
if (isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE'])) {
    $ranges_str = (isset($_SERVER['HTTP_RANGE']))?$_SERVER['HTTP_RANGE']:$HTTP_SERVER_VARS['HTTP_RANGE'];
    $ranges_arr = explode('-', substr($ranges_str, strlen('bytes=')));
    //Now its time to check the ranges
    if ((intval($ranges_arr[0]) >= intval($ranges_arr[1]) && $ranges_arr[1] != "" && $ranges_arr[0] != "" )
        || ($ranges_arr[1] == "" && $ranges_arr[0] == "")
    ) {
        //Just serve the file normally request is not valid :( 
        $ranges_arr[0] = 0;
        $ranges_arr[1] = $data_size - 1;
    }
} else { //The client dose not request HTTP_RANGE so just use the entire file
    $ranges_arr[0] = 0;
    $ranges_arr[1] = $data_size - 1;
}

//Now its time to serve file 
$file = fopen($data_file, 'rb');

$start = $stop = 0;
if ($ranges_arr[0] === "") { //No first range in array
    //Last n1 byte
    $stop = $data_size - 1;
    $start = $data_size - intval($ranges_arr[1]);
} elseif ($ranges_arr[1] === "") { //No last
    //first n0 byte
    $start = intval($ranges_arr[0]);
    $stop = $data_size - 1;
} else {
    // n0 to n1
    $stop = intval($ranges_arr[1]);
    $start = intval($ranges_arr[0]);
}    
//Make sure the range is correct by checking the file

fseek($file, $start, SEEK_SET);
$start = ftell($file);
fseek($file, $stop, SEEK_SET);
$stop = ftell($file);

$data_len = $stop - $start;

//Lets send headers 

if (isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE'])) {
    header('HTTP/1.0 206 Partial Content');
    header('Status: 206 Partial Content');
}
header('Accept-Ranges: bytes');
header('Content-type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"'); 
header("Content-Range: bytes $start-$stop/" . $data_size );
header("Content-Length: " . ($data_len + 1));

//Finally serve data and done ~!
fseek($file, $start, SEEK_SET);
$bufsize = 2048000;

ignore_user_abort(true);
@set_time_limit(0);
while (!(connection_aborted() || connection_status() == 1) && $data_len > 0) {
    echo fread($file, $bufsize);
    $data_len -= $bufsize;
    flush();
}

fclose($file);
