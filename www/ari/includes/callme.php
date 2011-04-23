<?php

/* 
 * Call Me constants
 */
define("CALLME_SUCCESS", "The call has been answered.");
define("CALLME_FAILURE", "The call failed.  Perhaps the line was busy.");
define("CALLME_ERROR", "System error.");

/*
require_once('php-asmanager.php');
global $astman;
$astmanconf=array();
$astmanconf['server']='127.0.0.1';
$astmanconf['username']='cron';
$astmanconf['secret']='1234';
$astman = new AGI_AsteriskManager($astmanconf);
$astman->connect($mgrhost.':5038', $mgruser, $mgrpass, 'off');
*/

function callme_close()
{
	global $astman;
	if (is_object($astman))
	{
		$astman->logoff();
		$astman->disconnect();
	}
	unset($astman);
}
/*
 * Call Me functions
 */
 /* Return the call me number stored in the database. */
function callme_getnum($exten)
{
        global $astman;
        $cmd 		= "database get OSDUSER $exten/callmenum";
	$callme_num 	= '';
        $results 	= $astman->Command($cmd);

	if (is_array($results))
	{
		foreach ($results as $results_elem)
		{
			if (preg_match('/Value: [^\s]*/', $results_elem, $matches) != 0)
			{
				$parts = preg_split('/\s/', trim($matches[0]));
				$callme_num = $parts[1];
			}

		}
	}

        return $callme_num;
}

/* Set the call me number to a new value.  No return value. */
function callme_setnum($exten, $callme_num)
{
        global $astman;

        $cmd = "database put OSDUSER $exten/callmenum $callme_num";
        $astman->Command($cmd);
        return;
}

/* Perform the Originate action to the call me number for message playing. */
/* Return the result of the call (success/failure/error).                  */
function callme_startcall($to, $from, $new_path)
{
	global $astman;
	$channel	= "Local/$to@osdial/n";
	$context	= "osdial_arivmcall";
	$extension	= "s";
	$priority	= "1";
	$callerid	= "VMAIL/$from";
	if (is_dir('/dev/dahdi')) {
		$version = '1.6.2';
                $variable       = "MSG=$new_path,MBOX=$from";
	} else {
		$version = '1.2';
                $variable       = "MSG=$new_path|MBOX=$from";
	}

	/* Arguments to Originate: channel, extension, context, priority, timeout, callerid, variable, account, application, data */
	$status = $astman->Originate($channel, $extension, $context, $priority, NULL, $callerid, $variable, NULL, NULL, NULL);
	if (is_array($status))
	{
		foreach ($status as $status_elem)
		{
			if (preg_match('/Originate successfully queued/', $status_elem, $matches) != 0)
			{
				return CALLME_SUCCESS;
			}
		}
	} 
	return CALLME_FAILURE;
}

function callme_eventsoff()
{
	global $astman;
	$astman->Events("off");
	return;
}

/* Returns boolean value for a call's success status. */
function callme_succeeded($status)
{
	if (strcmp($status, CALLME_SUCCESS) == 0)
		return true;
	else
		return false;
}

/* Hangs up an existing channel $exten is associated with.  No return value. */
function callme_hangup($exten)
{
	global $astman;
	$cmd 		= "local show channels";
        $chan_pat 	= '/[\s]*Local\/' . preg_quote(trim($exten)) . '@osdial\-[a-zA-Z0-9]*.(1|2)[\s]*/';
	$matches[0] 	= "";
	$response 	= "";
	$channel 	= "";
	$local_channels = $astman->Command($cmd);

	/* Look for our local channel. */
	if (is_array($local_channels))
	{
		foreach ($local_channels as $local_channels_elem)
		{
			preg_match($chan_pat, $local_channels_elem, $matches);
			if ($matches[0] != "")
			{
				$channel = $matches[0];
				break;
			}
		}
	} else
	{
		$channel = "";
	}

	/* If the channel was still up, hang it up. */ 
	if ($channel != "")
	{
		$astman->Hangup(trim($channel));
	}
	return; 
}
?>
