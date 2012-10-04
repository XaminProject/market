<?php

/**
 * Comment model
 * 
 * PHP versions 5.3
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Comment model class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Comments_MainModel extends MarketCommentsBaseModel
{
    /**
     * Comments prefix in redis
     */
    const PREFIX = 'Comments:';

    /**
     * Scope data prefix
     */
    const SCOPE_DATA_PREFIX = 'Data:';

    /**
     * Get comments for $scope
     *
     * @param string $scopeKey each comment section has a scope + id 
     * @param int    $page     page number 
     * @param int    $count    per page item
     *
     * @return array comments array
     */
    public function getComments($scopeKey, $page = 0, $count = 10)
    {
        $key = self::PREFIX . $scopeKey;
        $data = $this->getRedis()->lrange($key, $page * $count, ($page + 1) * $count);
        foreach ($data as &$item) {
            $item = json_decode($item, true);
        }
        return $data;
    }

    /**
     * Add new comment
     *
     * @param string $scopeKey scope key
     * @param string $user     User name
     * @param string $comment  User comments
     * 
     * @return boolean 
     */
    public function addComment($scopeKey, $user, $comment)
    {
        $key = self::PREFIX . $scopeKey;
        //Need more data?
        $data = [
            'comment' => self::purifyComment($comment),
            'user' => $user,
            'time' => time()
            ];
        $dataString = json_encode($data);
        return $this->getRedis()->lpush($key, $dataString);
    }

    /**
     * Register a scope 
     *
     * register new scope to use comments. if already registered then 
     * simply validate new parameter, they are readonly. 
     *
     * @param string $scope      scope name
     * @param string $route      Agavi route name for this scope
     * @param array  $parameters Parameters used to generate scope route
     * 
     * @return string generated key base on scope
     */
    public function registerScope($scope, $route, array $parameters = array()) 
    {
        $data = array (
            'scope' => strtolower($scope),
            'route' => $route,
            'parameters' => $parameters
            );

        $dataString = json_encode($data);
        $scopeKey = strtolower(md5($dataString));

        $key = self::PREFIX . self::SCOPE_DATA_PREFIX . $scopeKey;
        $savedData = $this->getRedis()->get($key);
        if (!$savedData) {
            //OK first time, save this scope 
            $this->getRedis()->set($key, $dataString);
        }

        return $scopeKey;
    }

    /**
     * Get scope data by key name
     *
     * @param string $scopeKey scope key (md5 data)
     *
     * @return array or null on no key found
     */
    public function getScopeData($scopeKey)
    {
        $key = self::PREFIX . self::SCOPE_DATA_PREFIX . $scopeKey;
        $data = $this->getRedis()->get($key);
        if (!$data) {
            return null;
        }
        $dataArray = json_decode($data, true);
        return $dataArray;
    }

    /**
     * Remove tags
     *
     * I think we need to create a seperate model for this tasks
     *
     * @param string $comment user input
     *
     * @return string
     */
    public static function purifyComment($comment)
    {
        return strip_tags($comment);
    }
}
