Stop Forum Spam
=======================

Version : 1.0

Released on july 2013

### About Stop Form Spam module

Stop Form Spam helps fighting against form spam.
It uses the spam database from : http://www.stopforumspam.com/


### Authors

[Michel-Ange Kuntz](http://www.partikule.net)


### Installation

* Copy the folder "Sfs" into the "/modules" folder of your Ionize installation.
* In the ionize backend, go to : Modules > Administration
* Click on "install"
* Reload the backend panel
* Setup the module


### Setup

* In the setup field "Event", set one event, for example "Myform.register.check"
* Create one custom registration form (see documentation)
* In the lib which processes your form data, fire the event you setup (here "Myform.register.check") :

	$result = Event::fire('Myform.register.check', $post);

	// Result == TRUE : The user can register
	if ( empty($result) OR $result == TRUE)
	{
		// ... Register
	}
	else
	{
		// ... No registration
		// ... But don't tell him... :-)
	}

