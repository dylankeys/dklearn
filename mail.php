<?php
	# Include the Autoloader (see "Libraries" for install instructions)
	require 'vendor/autoload.php';
	use Mailgun\Mailgun;

	# Instantiate the client.
	$mgClient = new Mailgun('key-56d4aa15763b3c7df7f6028c96bec121');
	$domain = "lms.dylankeys.com";

	# Make the call to the client.
	$result = $mgClient->sendMessage($domain, array(
		'from'    => 'Excited User <mailgun@lms.dylankeys.com>',
		'to'      => 'Dylan Keys <dylan.keys@outlook.com>',
		'subject' => 'Hello',
		'text'    => 'Testing some Mailgun awesomness!'
	));
?> 