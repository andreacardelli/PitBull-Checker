<?php
switch ($action) {
	case 'getfailure':
		returnLogs(DIR_LOGS.'/'.$parameters['userid'].'/'.$parameters['userid'].'.'.$parameters['checkid'].'.csv','/FAILURE/');
	break;
	case 'getsuccess':
		returnLogs(DIR_LOGS.'/'.$parameters['userid'].'/'.$parameters['userid'].'.'.$parameters['checkid'].'.csv','/SUCCESS/');
	break;
	case 'getnosuccess':
		returnLogs(DIR_LOGS.'/'.$parameters['userid'].'/'.$parameters['userid'].'.'.$parameters['checkid'].'.csv','/FAILURE|UP_AGAIN/');
	break;
	case 'errors':
		returnLogs(DIR_LOGS.'/'.$parameters['userid'].'/errors/'.$parameters['userid'].'.'.$parameters['checkid'].'.csv');
	break;
	case 'globalerrors':
		returnLogs(DIR_LOGS.'/globalerrors.csv','ALL','natural',100);
	break;
	case 'partial':
		returnLogs(DIR_LOGS.'/'.$parameters['userid'].'/'.$parameters['userid'].'.'.$parameters['checkid'].'.csv','ALL','natural',$parameters['linestoshow']);
	break;
	case 'partialreverse':
		returnLogs(DIR_LOGS.'/'.$parameters['userid'].'/'.$parameters['userid'].'.'.$parameters['checkid'].'.csv','ALL','reverse',$parameters['linestoshow']);
	break;
}
 
?>