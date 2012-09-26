<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.								   |
// | Copyright (c) 2012 Parspooyesh co.								|
// |																		   |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the	|
// | LICENSE file online at http://www.agavi.org/LICENSE.txt				   |
// |   vi: set noexpandtab:													|
// |   Local Variables:														|
// |   indent-tabs-mode: t													 |
// |   End:																	|
// +---------------------------------------------------------------------------+

/**
 * Provides Redis connectivity through phpredis extension
 *
 * @package	agavi
 * @subpackage database
 *
 * @author	 Behrooz Shabani
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since	  1.0.8
 *
 * @version	$Id$
 */
class AgaviRedisDatabase extends AgaviDatabase
{
	/**
	 * Connect to the database.
	 *
	 * @throws	 <b>AgaviDatabaseException</b> If a connection could not be 
	 *										   created.
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	protected function connect()
	{
		try {
			$redis = new Redis();

			// make persistent connection if persistent is set to true
			$method = $this->getParameter('persistent', false) ? 'pconnect'
				: 'connect';

			// make connection
			$redis->{$method}($this->getParameter('host', 'localhost'),
				$this->getParameter('port', 6379),
				$this->getParameter('timeout'));

			// set options
			if($this->hasParameter('options')) {
				foreach((array)$this->getParameter('options') as $key => $value) {
					$redis->setOption($key, $value);
				}
			}

			$this->resource = $this->connection = $redis;
		} catch(PDOException $e) {
			throw new AgaviDatabaseException($e->getMessage(), 0, $e);
		}
	}

	/**
	 * Execute the shutdown procedure.
	 *
	 * @throws	 <b>AgaviDatabaseException</b> If an error occurs while shutting
	 *										   down this database.
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	public function shutdown()
	{
		$this->connection->close();
	}
}

?>