 )   ___
(__/_____)         /)
  /        _  __  (/_  _  __      _
 /       _(/_/ (_/_) _(/_/ (_(_(_/_)_
(______)

/******************************************************************
 * Cerberus - Simple deployment system.
 *****************************************************************/

No MVC or anything...

Create a config.php in this directory. You can add as many
configurations as you like, following this structure:


return array(
	array(
		'title' => 'Project Title',
		'login' => array(
			'domain' => 'mydomain.com',
			'user' => 'my-ftp-user',
			'password' => 'my-ftp-password'
		),
		'projectDir' => 'D:/your/project/folder',
		'root' => './httpdocs/',
		'folders' => array(
			'.' => true, // "true" = Files only in base directory.
			'src' => false, // "false" = Recursive upload.
			// ... further directories here.
		),
		'configs' => array( // Config files that generate code by echoing it will be generated if changed.
			'source/config_generator.php' => 'build/config_target.php',
			// ... further config generators here.
		),
		'clean' => array(
			'source/build', // Add directories with built files to clean.
			// ... further directories here
		)
	)