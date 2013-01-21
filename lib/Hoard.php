<?php

/**
 * Store raw data for later querying
 *
 * Usage:
 *	
 *		$event = Hoard::track('php_syntax_error', array(
 *			'uid'		=> 123456,
 *			'file'		=> '/path/to/root/index.php'
 *			'line'		=> 10,
 *			'uri'		=> 'http://www.marcqualie.com/'
 *			'message'	=> 'You have an error in your PHP Syntax',
 *			'trace'		=> array()
 *		));
 *
 * @version 0.0.1
 * @author Marc Qualie <http://marcqualie.com>
 */

class Hoard
{
	
	/* Application Settings */
	public static $appkey                   = '';
	public static $secret                   = '';
	
	/* Remote server settings */
	public static $server                   = '';
	public static $version                  = '0.0.1';
	public static $initialized              = false;

	public static $error                    = '';
	public static $last_raw_response        = '';
		
	/**
	 * Initialize Hoard Config
	 *
	 * @param config		Array			Contains connection info and application keys
	 * @return 				Boolean			True on success, false if config is invalid
	 */
	public static function init ($config)
	{
		foreach ($config as $k => $v)
		{
			self::$$k = $v;
		}
		if (self::$server && self::$appkey && self::$secret)
		{
			self::$initialized = true;
			self::registerExceptionHandler();
			self::registerErrorHandler();
			return true;
		}
		self::$error = 'Invalid Configuration';
		return false;
	}

	/**
	 * Verify
	 */
	public static function verify ()
	{
		if (self::$initialized === false)
		{
			self::$error = 'Not initialized';
			return false;
		}
		if (self::$server === '')
		{
			self::$error = 'No server defined';
			return false;
		}
		if (self::$appkey === '')
		{
			self::$error = 'No $APPKEY';
			return false;
		}
		return true;
	}

	/**
	 * Request
	 */
	public static function req (array $options = array())
	{

		// Build URL
		$action = isset($options['action']) ? $options['action'] : 'track';
		$method = $action === 'track' ? 'POST' : 'GET';
		$data = isset($options['data']) ? $options['data'] : false;
		$query = isset($options['query']) ? $options['query'] : '';
		$event = isset($options['event']) ? $options['event'] : '';
		if ($query)
		{	
			//$query['secret'] = self::$secret;
			//$data = 'appkey=' . self::$appkey;
			$query = '?appkey=' . self::$appkey . '&' . http_build_query($query);
		}
		$url = self::$server . '/' . $action . '/' . $event . $query;

		// Get parts
		$parts = parse_url($url);
		if ( ! isset($parts['query']))
		{
			$parts['query'] = '';
		}

		// Build Request
		$fp = fsockopen(
			$parts['host'],
			array_key_exists('port', $parts) ? $parts['port'] : 80,
			$errno,
			$errstr,
			30);
		$response = '';
		if ($fp !== 0)
		{
			$out  = $method . " " . $parts['path'] . "?" . $parts['query'] . " HTTP/1.1\r\n";
			$out .= "Host: " . $parts['host'] . "\r\n";
			$out .= "User-Agent: PHP " . self::$version . "\r\n";
			$out .= "Connection: close\r\n";
			if ($data)
			{
				$out .= "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
				$out .= "Content-Length: " . strlen($data) . "\r\n";
				$out .= "\r\n" . $data;
			}
			$out .= "\r\n";
			fwrite($fp, $out);
			if ( ! isset($options['async']))
			{
				while ( ! feof($fp))
				{
					$response .= fgetss($fp);
				}
			}
			fclose($fp);
		}
		if ($response)
		{
			self::$last_raw_response = $response;
			$response = str_replace("\r", '', $response);
			$response = substr($response, strpos($response, "\n\n") + 2);
			return $response;
		}
		return isset($options['async']) ? true : false;
	}
	
	/**
	 * Track events
	 *
	 * @param file			String			Location of the file which triggered the event on the server
	 * @param line			Number			Line number where the error can be isolated
	 * @param location		String			Url where you can navigate to replicate the problem
	 */
	public static function track ($event, array $data)
	{
		
		if ( ! self::verify()) return false;
		
		// Auto generate some data from the environment
		$data['event']				= $event;
		if (isset($_SERVER['HTTP_HOST']))
		{
			$data['host']				= $_SERVER['HTTP_HOST'];
		}
		if (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REMOTE_ADDR']))
		{
			$data['server'] = array(
				'name' => $_SERVER['SERVER_NAME'],
				'ipv4' => $_SERVER['SERVER_ADDR']
			);
		}
		$data['appkey']				= self::$appkey;
		
		// TODO: Parse / Verify Data
		if (array_key_exists('file', $data))
		{
			if (defined('DOCROOT'))
			{
				$data['file'] = str_replace(DOCROOT, '', $data['file']);
			}
		}
		
		// Track sessions for tracking breadcrumbs style events
		$sess = session_id();
		if ($sess)
		{
			$data['sess'] = $sess;
		}
		
		// Generate Signature and encode
		$data['hash'] = md5(uniqid());
		$data['sig'] = sha1(self::$secret . $data['hash']);
		
		// Check for special params
		$async = true;
		if (isset($data['$async']) && $data['$async'] === false)
		{
			$async = false;
			unset($data['$async']);
		}
		
		// Format data into post string
		$postfields = array(
			'format' => 'json',
			'data' => json_encode($data)
		);
		$post_params = array();
		foreach ($postfields as $key => $val)
		{
			$post_params[] = $key . '=' . urlencode($val);
		}
		$post_string = implode('&', $post_params);
		
		// Send Data
		$req = self::req(array(
			'action'      => 'track',
			'data'        => $post_string,
			'async'       => $async
		));

		// Trigger Event
		self::dispatchEvent($event, $data);
		
		// Output
		return $async ? true : $req;
		
	}
	
	/**
	 * Find data based on input params
	 *
	 * @return		Array				Data matching your query. Blank array if no data
	 */
	public static function find ()
	{
		if ( ! self::verify())
		{
			return false;
		}

		// Request Data
		$req = self::req(array(
			'action'      => 'find',
			'query'       => array(
				'query'   => '',
				'fields'  => '',
				'sort'    => '',
				'limit'   => 25
			)
		));

		return $req;
	}
	
	/**
	 * Stats
	 *
	 * TODO: Need to create stats to display info such as usage information and any hoard related errors
	 */
	public static function stats ()
	{
		
	}

	/**
	 * Application error reporting
	 */
	public static function last_error ()
	{
		return self::$error;
	}

	/**
	 * Attach events to events..
	 */
	public static $callbacks = array();
	public static function addEventListener ($event, $callback)
	{
		if ( ! isset(self::$callbacks[$event]))
		{
			self::$callbacks[$event] = array();
		}
		self::$callbacks[$event][] = $callback;
	}
	public static function dispatchEvent ($event, $data)
	{
		foreach (self::$callbacks as $ev => $cbs)
		{
			if ($ev === '*' || $ev === $event)
			{
				foreach ($cbs as $cb)
				{
					$cb($data, $event);
				}
			}
		}
	}

	/**
	 * Self attach to common php problems
	 */
	private static function registerExceptionHandler ()
	{
		//set_exception_handler('self::exceptionHandler');
	}
	public static function handleException ($e)
	{
		self::track('error', array(
			'm' => $e->getMessage(),
			'f' => $e->getFile(),
			'l' => $e->getLine(),
			'c' => $e->getCode(),
			's' => $e->getTrace()
		));
		self::dispatchEvent('exception', array(
			'e'      => $e
		));
	}
	private static function registerErrorHandler ()
	{
		set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
		});
	}
	
}