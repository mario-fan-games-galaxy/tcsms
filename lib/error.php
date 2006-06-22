<?php

error_reporting(E_ALL);

Class ErrorHandler {
	
	var $_errors		= array();
	var $_phpDefHandler	= null;
	var $_errorCodes	= array();
	var $_errorSubCodes = array();
	
	function ErrorHandler () {
		$this->_initErrorCodes();
		$this->_aquireErrorHandler();
		$this->flushErrors();	
		ob_start();
	}
	
	function raiseError ($eCode = null, $description = 'No additional information available') {
		$this->_errors[] = array(
			'ecode' => $eCode, 
			'data' => $description);
		
		if ($this->_getErrorType($eCode) < 1000)
			$this->displayFatalError($eCode, $description);
		
		return $this->_errors;
	}
	
	function displayFatalError ($ecode, $data) {
		ob_clean();
		$errStruct = array(
			'errorType' => $this->_errorCodes[$this->_getErrorType($ecode)],
			'errorCode' => $ecode,
			'errorMsg' => $this->_errorSubCodes[$ecode],
			'addInfo' => $data);
			
		echo $this->_fatalTemplate($errStruct);
		exit;
	}
	
	function displayError () {
		ob_clean();
		$this->displayFatalError($this->_errors[0]['ecode'], $this->_errors[0]['data']);
		exit;
	}
	
	function flushErrors () {
		$this->_errors = array();
		return $this->_errors;
	}
	
	function getNumErrors () {
		return sizeof($this->_errors);
	}
	
	function _errorHandler ($errNo, $errStr, $errFile, $errLine) {
		$errCode = $this->_mapError($errNo);
		$this->raiseError(401, "<br><br><b>$errCode:</b> $errStr in <b>$errFile</b> on <b>$errLine</b>");
	}
	
	function _mapError ($phpErrCode) {
		switch ($phpErrCode) {
			case 1: return 'E_ERROR';
			case 2: return 'E_WARNING';
			case 4: return 'E_PARSE';
			case 8: return 'E_NOTICE';
			case 256: return 'E_USER_ERROR';
			case 512: return 'E_USER_WARNING';
			case 1024: return 'E_USER_NOTICE';
			default: return '';
		}
	}
	
	function _aquireErrorHandler () {
		$this->_phpDefHandler = set_error_handler(array(&$this, '_errorHandler'));
	}
	
	function _getErrorType ($subcode) {
		return floor($subcode / 10) * 10;
	}
	
	function _initErrorCodes () {
		$this->_errorCodes = array(
			100		=> 'Database Error',
			200		=> 'File I/O Error',
			300		=> 'Authenitcation Error',
			400		=> 'Script Error',
			1100	=> 'Form Input Error',
			1200	=> 'URL Error',
			1300	=> 'Database Record Error');
		$this->_errorSubCodes = array(
			101		=> 'Error in SQL Query',
			102		=> 'Could not Connect to Database Server',
			103		=> 'Database Allready Exists',
			104		=> 'Could not select database',
			105		=> 'Could not connect Database',
			201		=> 'Could not create directory',
			202		=> 'Could not copy file(s)',
			203		=> 'Could not execute command',
			204		=> 'Could not read configuration file',
			205		=> 'Could not delete file(s)',
			206		=> 'Could not read file(s)',
			207		=> 'Could not write file(s)',
			208		=> 'Could not open file(s)',
			209		=> 'File allready exists',
			210		=> 'Could not delete directory',
			301		=> 'Session expired',
			302		=> 'Username or Password is invalid',
			401		=> 'And internal script error ouccurred',
			1101	=> 'A required field was empty',
			1102	=> 'Invalid data in field',
			1103	=> 'File upload failed',
			1201	=> 'Missing record identifier(s)',
			1301	=> 'Record doesn\'t exist');
	}
	
	function _fatalTemplate ($values) {
		$html = "<html>
			<head>
			<basefont face='Arial'>
			</head>
			<body>
			<p> 
			<p>
			<table width='100%' border='0' cellspacing='0' cellpadding='5'
			bgcolor='Navy'> 
			<tr>
				<td bgcolor='Navy' width='100%'><font
			color='white'><b>Error!</b></font></td>
			</tr>
			</table>
			<p>
			The 
			following fatal error occurred:
			<p>
			<b>{{errorType}} (error code 
			{{errorCode}})</b>
			<br>
			{{errorMsg}} {{addInfo}}
			</body>
			</html>";
			
		reset($values);
		while (list($key, $value) = each($values))
			$html = str_replace('{{'.$key.'}}', $value, $html);
		
		return $html;
	}
}

?>