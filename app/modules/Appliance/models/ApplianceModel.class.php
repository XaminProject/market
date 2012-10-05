<?php

/**
 * appliance model
 *
 * php version 5.2
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */

/**
 * appliance model
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_ApplianceModel extends MarketApplianceBaseModel
{
    /**
     * @var array will be used as redis cache
     */
    private static $_cache = [];

    /**
     * Installer to appliances prefix
     */
    const INSTALLER_TO_APPLIANCES_PREFIX = 'installer_to_appliances:';

    /**
     * returns all tags
     *
     * @param int $offset the start offset
     * @param int $limit  the limit of items
     *
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @return array tags
     */
    public function tags($offset=0, $limit=10)
    {
        $tmp = $this->getRedis()->zRevRangeByScore("tags", "+inf", "-inf", array($offset, $limit));
        $ro = $this->getContext()->getRouting();
        $tags = [];
        foreach ($tmp as $name => $count) {
            $tags[] = [
                'count' => $count,
                'name' => $name,
                'url' => $ro->gen('tags.tag', ['name' => $name])
            ];
        }
        return $tags;
    }

    /**
     * counts all tags
     *
     * @return int
     */
    public function countTags()
    {
        return (int) $this->getRedis()->zCard('tags');
    }

    /**
     * get appliances by tag
     *
     * @param string $tag    the tag
     * @param int    $offset the start offset
     * @param int    $limit  the number of items to fetch
     *
     * @return array
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     */
    public function appliancesByTag($tag, $offset=0, $limit=10)
    {
        $tmp = $this->getRedis()->zRevRangeByScore(
            "tag:{$tag}",
            "+inf",
            "-inf",
            [ 'limit' => [$offset, $limit] ]
        );
        // we gonna put appliances in this array
        $result = ['result' => []];
        $result['total'] = $this->getRedis()->zCard("tag:{$tag}");
        // get all appliances that has this tag
        foreach ($tmp as $name) {
            $result['result'][] = $this->getAppliance($name);
        }
        return $result;
    }

    /**
     * searches for appliances using solr
     *
     * @param string $queryString query string
     * @param string $start       the start offset
     * @param string $length      the number of items should be returned
     *
     * @return array an array of matched appliances
     */
    public function query($queryString, $start=0, $length=10)
    {
        $solr = $this->getSolr();
        $query = new SolrQuery();
        $query->setQuery($queryString)
            ->setStart($start)
            ->setRows($length)
            ->addField('name')
            ->addField('version')
            ->addParam('group', 'true')
            ->addParam('group.field', 'name');
        $queryResponse = $solr->query($query);
        $response = $queryResponse->getResponse();
        $result = ['total' => $response->grouped->name->matches, 'result' => []];
        foreach ($response->grouped->name->groups as $group) {
            $name = $group->doclist->docs[0]->name;
            $latest = $group->doclist->docs[0]->version;
            foreach ($group->doclist->docs as $key => $doc) {
                if ($key === 0) {
                    continue;
                }
                if (version_compare($doc->version, $latest, '>=')) {
                    $latest = $doc->version;
                }
            }
            $result['result'][] = $this->getAppliance($name, $latest);
        }
        return $result;
    }

    /**
     * fetches json object of an appliance
     *
     * @param string $name    name of appliance
     * @param string $version version of appliance
     *
     * @return array json decoded array of appliance
     */
    public function getAppliance($name, $version=null)
    {
        $index = 0;
        if (!is_null($version)) {
            $index = $this->getRedis()->get("appliance_version_to_index:{$name}:{$version}");
            if ($index === false) {
                return null;
            }
            $length = $this->getRedis()->llen("Appliance:{$name}");
            $index = $length - $index - 1;
        }
        $appliance = json_decode($this->getRedis()->lindex("Appliance:{$name}", $index), true);
        $appliance['link'] = $this->getContext()->getRouting()->gen(
            'appliance.info',
            array(
                'name' => $appliance['name'],
                'version' => $appliance['version']
            )
        );
        $appliance['install'] = $this->getContext()->getRouting()->gen(
            'appliance.install',
            array(
                'name' => $appliance['name'],
                'version' => $appliance['version']
            )
        );
        $appliance['remove'] = $this->getContext()->getRouting()->gen(
            'appliance.remove',
            array(
                'name' => $appliance['name'],
                'version' => $appliance['version']
            )
        );
        $appliance['isInstalled'] = false;
        $appliance['rate'] = $this->getUserRateOfAppliance($appliance['name']);
        $user = $this->getContext()->getUser();
        if ($user->isAuthenticated()) {
            $jid = $user->getAttribute('jid');
            $key = self::INSTALLER_TO_APPLIANCES_PREFIX . $jid;
            if (!isset(self::$_cache[$key])) {
                self::$_cache[$key] = $this->getRedis()->sMembers($key);
            }
            if (in_array("{$appliance['name']}:{$appliance['version']}", self::$_cache[$key])) {
                $appliance['isInstalled'] = true;
            }
        }
        return $appliance;
    }

    /**
     * Get appliance for a user
     *
     * @param string $jid the jid that we gonna check its appliances
     *
     * @return array list of appliance 
     */
    public function getUserAppliances($jid)
    {
        $key = self::INSTALLER_TO_APPLIANCES_PREFIX . $jid;
        if (!isset(self::$_cache[$key])) {
            self::$_cache[$key] = $this->getRedis()->sMembers($key);
        }
        foreach (self::$_cache[$key] as &$item) {
            list($name, $version) = explode(':', $item);
            $item = $this->getAppliance($name, $version);
        }
        return $appliances;
    }

    /**
     * inserts install information into client queue
     *
     * @param string $jid     the jid of archipel that we're going to install appliance on
     * @param string $name    the name of appliance
     * @param string $version the version of appliance
     *
     * @return void
     */
    public function install($jid, $name, $version)
    {
        $this->addPeaceAction('install', $jid, $name, $version);
    }

    /**
     * removes given appliance from archipel that is running behind jid
     *
     * @param string $jid     jid archipel that is using to connect to market
     * @param string $name    name of appliance
     * @param string $version version of appliance
     *
     * @return void
     */
    public function remove($jid, $name, $version)
    {
        $this->addPeaceAction('remove', $jid, $name, $version);
    }

    /**
     * inserts an action into peace-daemon's queue list
     *
     * @param string $action  the action that peace daemon should run
     * @param string $jid     the jid of archipel that we're going to install appliance on
     * @param string $name    the name of appliance
     * @param string $version the version of appliance
     *
     * @return void
     */
    protected function addPeaceAction($action, $jid, $name, $version)
    {
        $this->getRedis()->lpush(
            'peace:daemon',
            json_encode(
                array(
                    'action' => $action,
                    'jid' => $jid,
                    'name' => $name,
                    'version' => $version
                    )
            )
        );
    }

    /**
     * increases rating of an appliance by given number
     *
     * @param string $name the name of appliance
     * @param int    $by   the number that rating should be increased by
     *
     * @return int the new rate value
     */
    public function increaseRatingBy($name, $by)
    {
        // increase the rating of appliance
        $rate = $this->getRedis()->zIncrBy("ratings", $by, $name);
        // we sort appliances of a tag by rating, so update 'em too
        $appliance = $this->getAppliance($name);
        foreach ($appliance['tags'] as $tag) {
            $this->getRedis()->zIncrBy("tag:{$tag}", $by, $name);
        }
        return $rate;
    }

    /**
     * returns current rating that a user has given to an appliance
     *
     * @param string $name the name of appliance
     *
     * @return int 0 will be returned if user has not rated the appliance yet, otherwise
     * the rate will be returned
     */
    public function getUserRateOfAppliance($name)
    {
        $us = $this->getContext()->getUser();
        if ($us->isAuthenticated()) {
            $ratings = $us->getAttribute('ratings', null, []);
            if (isset($ratings[$name])) {
                return $ratings[$name];
            }
        }
        return 0;
    }

    /**
     * sets rate that user has given to an appliance
     *
     * @param string $name the name of appliance
     * @param int    $rate the rate that user wants to give to appliance
     *
     * @return void
     */
    public function setUserRateOfAppliance($name, $rate)
    {
        $us = $this->getContext()->getUser();
        if ($us->isAuthenticated()) {
            $ratings = $us->getAttribute('ratings', null, []);
            $ratings[$name] = $rate;
            $us->setAttribute('ratings', $ratings);
        }
    }
}
