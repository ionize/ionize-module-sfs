<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sfs_Events
{
	protected static $ci;

	protected static $config = array();

	protected static $api_server;

	protected static $api_user;

	protected static $api_key;

	protected static $track = FALSE;

	protected static $username_input;
	protected static $evidence_input;


	public function __construct()
	{
		// If the CI object is needed :
		self::$ci =& get_instance();

		self::$config = Modules()->get_module_config('Sfs');
		self::$api_server = ( ! empty(self::$config['api_server'])) ? self::$config['api_server'] : NULL;
		self::$api_user = ( ! empty(self::$config['api_user'])) ? self::$config['api_user'] : NULL;
		self::$api_key = ( ! empty(self::$config['api_key'])) ? self::$config['api_key'] : NULL;
		self::$track = ( ! empty(self::$config['track'])) ? self::$config['track'] : FALSE;
		self::$evidence_input = ( ! empty(self::$config['evidence_input'])) ? self::$config['evidence_input'] : NULL;
		self::$username_input = ( ! empty(self::$config['username_input'])) ? self::$config['username_input'] : NULL;

		// Events registration
		if ( ! is_null(self::$api_server))
		{
			self::$ci->load->library('rest', array('server' => self::$api_server));
			self::$ci->rest->api_key(self::$config['api_key']);

			$events = explode(',', self::$config['events']);

			foreach($events as $event)
			{
				$event = trim($event);
				Event::register($event, array($this, 'on_post_check_before'));
			}
		}
	}


	/**
	 * Return TRUE if the check passed.
	 * FALSE if the user is one Bot
	 *
	 * Doc : http://www.stopforumspam.com/usage
	 *
	 * @param $post
	 *
	 * @return string
	 */
	public function on_post_check_before($post)
	{
		$trusted = TRUE;

		$post['ip'] = self::$ci->input->ip_address();

		$params = 'email='.$post['email'].'&ip='.$post['ip'].'&f=serial';

		$response = self::$ci->rest->get('api', $params);

		if (is_string($response))
		{
			$response = unserialize($response);

			if ( ! empty($response['ip']['appears']) && intval($response['ip']['appears']) > 0)
				$trusted = FALSE;

			if ( ! empty($response['email']['appears']) && intval($response['email']['appears']) > 0)
				$trusted = FALSE;

			if ( ! $trusted && self::$track == TRUE && ! empty(self::$api_key))
				self::submit($post);
		}

		return $trusted;
	}


	function submit($post)
	{
		$username = $email = $ip = $evidence = '';

		if ( ! is_null(self::$username_input))
		{
			$fields = explode(',', self::$username_input);
			{
				foreach ($fields as $field)
				{
					$field = trim($field);
					if ( ! empty($post[$field]))
					{
						if ( ! empty($username)) $username .= ' ';
						$username .= urlencode($post[$field]);
					}
				}
			}
			$username = urlencode($username);
		}

		if ( ! is_null(self::$evidence_input) && !empty($post[self::$evidence_input]))
			$evidence = urlencode($post[self::$evidence_input]);

		if ( ! empty($post['ip']))
			$ip = $post['ip'];

		if ( ! empty($post['email']))
			$email = $post['email'];

		if (
			empty($ip)
			OR empty($username)
			OR empty($email)
			OR empty($evidence)
		)
		{
			log_message('error', 'Stop Forum Spam Module : Cannot submit, $ip, $username, $email & $evidence must be set');
		}
		else
		{
			$params = 'api_key='.self::$api_key;
			$params .= '&ip_addr='.$ip;
			$params .= '&email='.$email;
			$params .= '&username='.$username;
			$params .= '&evidence='.$evidence;

			self::$ci->rest->get('add.php', $params);
		}
	}
}
