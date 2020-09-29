<?php

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

function num_cpu(): int {
    
    if (defined ( 'PHP_WINDOWS_VERSION_MAJOR' )) {

        if(empty(getenv('APPSETTING_WEBSITE_SITE_NAME'))){ //Fix for Azure App Service Windows
            $str = trim ( shell_exec ( 'wmic computersystem get NumberOfLogicalProcessors 2>&1' ) );
            if (! preg_match ( '/(\d+)/', $str, $matches )) {
                throw new \RuntimeException ( 'wmic failed to get number of logical cores on windows!' );
            }
            return (( int ) $matches [1]);
        } else {
            return ( int ) getenv("NUMBER_OF_PROCESSORS");
        }
    } 
    
    $ret = @shell_exec ( 'nproc' );
	if (is_string ( $ret )) {
		$ret = trim ( $ret );
		if (false !== ($tmp = filter_var ( $ret, FILTER_VALIDATE_INT ))) {
			return $tmp;
		}
    }
    
	if (is_readable ( '/proc/cpuinfo' )) {
		$cpuinfo = file_get_contents ( '/proc/cpuinfo' );
		$count = substr_count ( $cpuinfo, 'processor' );
		if ($count > 0) {
			return $count;
		}
	}
}

function kill($pid){ return stripos(php_uname('s'), 'win')>-1 
    ? exec("taskkill /F /PID $pid") : exec("kill -9 $pid"); }

function processKill($pid) {
    $isRunning = false;
    if (defined ( 'PHP_WINDOWS_VERSION_MAJOR' )) {
        $out = [];
            exec("TASKLIST /FO LIST /FI \"PID eq $pid\"", $out);
            if(count($out) > 1) {
                $isRunning = true;
            }
    }  elseif(posix_kill(intval($pid), 0)) {
        $isRunning = true;
    }
    
    if($isRunning) {
        kill($pid);  
    }
}