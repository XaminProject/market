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
     * Get comments for $scope
     *
     * @param string $scope the scope we've stored comments in
     * @param int    $page  page number 
     * @param int    $count per page item
     *
     * @return array comments array
     */
    public function getComments($scope, $page = 0, $count = 10)
    {
        $key = self::PREFIX . $scope;
        if (!$this->getRedis()->sismember('Comments', $scope)) {
            // make it an empty list, it will be needed for writing comment
            $this->getRedis()->sadd('Comments', $scope);
        }
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

    /**
     * returns true if the scope is valid
     *
     * @param string $scope the scope to be checked
     *
     * @return boolean
     */
    public function isValidScope($scope)
    {
        return $this->getRedis()->sIsMember('Comments', $scope);
    }
}
