<?php
function main ()
{
	echo "IT WORKS!";
}
/* creating the output file (example.exe) */
$fh = fopen("./test.exe", "w");
/* 1) writing a stub (phpe.exe) */
/**
* phpe.exe - это переименованный файл php.exe
* с инстала php - это правильно ?
*/
$size = filesize("php.exe");
$fr = fopen("php.exe", "r");
fwrite($fh, fread($fr, $size), $size);
$startpos = ftell($fh);
/* 2) writing bytecodes */
bcompiler_write_header($fh);
bcompiler_write_function($fh, "main");
bcompiler_write_footer($fh);
/* 3) writing EXE footer */
bcompiler_write_exe_footer($fh, $startpos);
/* closing the output file */
fclose($fh);
?>