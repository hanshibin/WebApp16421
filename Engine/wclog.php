<?php

include_once dirname(__FILE__).'/globaldefine.php';

class B1WechatLog {

	private static $m_instance;
	private $m_path;
	private $m_handleArr;
	private $m_logPid;

	/**
	 *	Class Constuct Function
	 *	@param	$path	Log File Path 
	 **/
	function __construct($path) {

		$this->m_path = $path;
		$this->m_logPid = getmypid();
	}

	/**
	 *
	 */
	function __destruct() {
		foreach ($this->m_handleArr as $key => $value) {
			if ($value) {
				fclose($value);
			}
		}
	}

	/**
	 *
	 */
	public static function GetInstance($path) {

		if(!(self::$m_instance instanceof self)) {
			self::$m_instance = new B1WechatLog($path);
		}

		return self::$m_instance;

	}

	/**
	 *	Get file handle, if it doesn't exist, create one; or return it.
	 *	@param	$fileName
	 *	@return	file handle
	 */
	private function GetHandle($fileName) {

		if($this->m_handleArr[$fileName]) {
			return $this->m_handleArr[$fileName];
		}

		date_default_timezone_set('PRC');
		$nowTime = time();
		$logSuffix = date('ymd', $nowTime);
		$fileName = $this->m_path . '/' . $fileName . '_'. $logSuffix. '_' . ".log";
		$handle = fopen($fileName, "a");
		$this->m_handleArr[$fileName] = $handle;

		return $this->m_handleArr[$fileName];

	}

	/**
	 *	Write Content into Log
	 *	@param	$fileName
	 *	@param	$message
	 **/
	public function WriteLog($fileName, $message) {

		$handle = $this->GetHandle($fileName);
		$nowTime = time();
		$logPreffix = date('Y-m-d H:i:s', $nowTime);

		fwrite($handle, "[$logPreffix]$message\n");
	}

	/*
	 * 	
	 *	@param 
	 */

	private static function IsLogLeaveOff($logLevel) {

		$swithFile = ROOT_PATH . '/log/' . 'NO_' . $logLevel;
		if(file_exists($swithFile))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	/*
	 * 	
	 *	@param 
	 */
	public static function LogInternal($logMessage, $logLevel, $__FILE, $__LINE, $__FUNCTION) {

		if(B1WechatLog::IsLogLeaveOff(!logLevel))
		{
			return;
		}


		$__FILE = explode('/', rtrim($__FILE, '/'));
		$__FILE = $__FILE[count($__FILE) - 1];
		$prefix = " [$__FILE] [$__FUNCTION] [$__LINE] [$logLevel] ";

		if($logLevel == INFO_LEVEL)
		{
			$prefix = " [$logLevel] ";
		}


		$logFileName = LOG_FILE_NAME . '_' . $logLevel;

		B1WechatLog::GetInstance(ROOT_PATH . '/log') -> WriteLog($logFileName, $prefix . $logMessage);

	}

}

function LogInfo($logMessage, $logLevel = INFO_LEVEL) {

	$callstack = debug_backtrace();
	$__FILE = $callstack[0]["file"];
	$__LINE = $callstack[0]["line"];
	if (count($callstack) == 1)
	{
		$__FUNCTION = 'main';
	}
	else
	{
		$__FUNCTION = $callstack[1]["function"];
	}

	B1WechatLog::LogInternal($logMessage, $logLevel, $__FILE, $__LINE, $__FUNCTION);
}


?>