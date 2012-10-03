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
     * Installer to appliances prefix
     */
    const INSTALLER_TO_APPLIANCES_PREFIX = 'installer_to_appliances:';
    
    /**
     * returns all tags
     *
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @return array tags
     */
    public function tags()
    {
        return $this->getRedis()->sMembers("tags");
    }

    /**
     * get appliances by tag
     *
     * @param string $tag the tag
     *
     * @return array
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     */
    public function appliancesByTag($tag)
    {
        $tmp = [];
        // we gonna put appliances in this array
        $result = ['total' => 0, 'result' => []];
        // get all appliances that has this tag
        foreach ($this->getRedis()->sMembers("tag:{$tag}") as $id) {
            // the id is like: "name:version"
            list($name, $version) = explode(':', $id, 2);
            if (!isset($tmp[$name])) {
                $tmp[$name] = true;
                $result['total']++;
                $result['result'][] = $this->getAppliance($name);
            }
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
        return $appliance;
    }

    /**
     * Get appliance for a user
     *
     * @param string $username user name
     * @param string $hostname host name (jid attribute)
     *
     * @return array list of appliance 
     */
    public function getUserAppliances($username, $hostname) 
    {
        $key = self::INSTALLER_TO_APPLIANCES_PREFIX . strtolower($username) . '@' . $hostname;
        $appliances = $this->getRedis()->sMembers($key);
        foreach ($appliances as &$item) {
                list($name, $version) = explode(':', $item);
                $item = array (
                    'name' => $name, 
                    'version' => $version,
                    'link' => $this->getContext()->getRouting()->gen(
                        'appliance.info',
                        array(
                            'name' => $name,
                            'version' => $version
                            )
                    )
                );
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
}
