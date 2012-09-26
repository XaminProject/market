<?php
//Add agavi installation folder to php include_path 
set_include_path(
		realpath('../libs/agavi/') . PATH_SEPARATOR .
		get_include_path()
);

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to the agavi/agavi.php script.                |
// +---------------------------------------------------------------------------+
require('agavi/agavi.php');

// +---------------------------------------------------------------------------+
// | An absolute filesystem path to our app/config.php script.                 |
// +---------------------------------------------------------------------------+
require('../app/config.php');

// +---------------------------------------------------------------------------+
// | Initialize the framework. You may pass an environment name to this method.|
// | By default the 'development' environment sets Agavi into a debug mode.    |
// | In debug mode among other things the cache is cleaned on every request.   |
// +---------------------------------------------------------------------------+
Agavi::bootstrap('%%PUBLIC_ENVIRONMENT%%');

// +---------------------------------------------------------------------------+
// | Call the controller's dispatch method on the default context              |
// +---------------------------------------------------------------------------+
AgaviContext::getInstance('web')->getController()->dispatch();

?>