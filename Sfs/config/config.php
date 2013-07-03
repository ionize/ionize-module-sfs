<?php

$config['module']['sfs'] = array
(
	'name' => "Stop Form Spam",
	'description' => "Protect your forms against spam",
	'author' => "Ionize Dev Team",
	'version' => "1.0",

	'has_admin' => TRUE,

	 // Module's config items
	'api_server' => 'http://www.stopforumspam.com',


	'api_user' => '',
	'api_key' => '',

	// Send the email and IP of one spambot again to stopforumspam.com
	'track' => true,

	// Input / Textarea which contains the user's send message
	'evidence_input' => 'message',

	// Inpt which contains the username (needed for stopforumspam submission)
	'username_input' => 'firstname,lastname',

	// Registered events
	'events' => 'User.register.check.before',
);

return $config['module']['sfs'];

