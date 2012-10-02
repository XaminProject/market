<?php

/**
 * appliance model
 *
 * php version 5.2
 *
 * @category     Xamin
 * @package      Market
 * @author       Behrooz Shabani <everplays@gmail.com>
 * @copyright    2012 (c) ParsPooyesh Co
 * @license      Custom <http://xamin.ir>
 * @version      GIT: $
 * @link         http://xamin.ir
 */

/**
 * appliance model
 *
 * @category     Xamin
 * @package      Market
 * @author       Behrooz Shabani <everplays@gmail.com>
 * @copyright    2012 (c) ParsPooyesh Co
 */
class Appliance_ApplianceModel extends MarketApplianceBaseModel
{
    /**
     * @var Redis redis database connection
     */
    protected $redis = null;

    /**
     * @var SolrClient solr client
     */
    protected $solr = null;

    /**
     * returns an instance of Redis
     *
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @return Redis
     */
    public function getRedis()
    {
        if (is_null($this->redis))
            $this->redis = $this->getContext()->getDatabaseManager()->getDatabase()->getConnection();
        return $this->redis;
    }

    /**
     * returns an instance of Redis
     *
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @return Redis
     */
    public function getSolr()
    {
        if (is_null($this->solr))
            $this->solr = $this->getContext()->getDatabaseManager()->getDatabase('solr')->getConnection();
        return $this->solr;
    }

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
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @param $tag string the tag
     * @param array
     */
    public function appliancesByTag($tag)
    {
        // we gonna put appliances in this array
        $appliances = [];
        // get all appliances that has this tag
        foreach($this->getRedis()->sMembers("tag:{$tag}") as $id)
        {
            // the id is like: "name:version"
            list($name, $version) = explode(':', $id, 2);
            // replace version if there's newer version available
            if(isset($appliances[$name]))
            {
                if(version_compare($version, $appliances[$name], '>='))
                    $appliances[$name] = $version;
            }
            // hmm, we don't have this appliance yet, let's add it
            else
                $appliances[$name] = $version;
        }
        return $appliances;
    }

    /**
     * searches for appliances using solr
     *
     * @param $query string query string
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
            ->addField('version');
        // TODO: we need to set group=true&group.field=name to avoid
        // duplicate appliance in result
        $queryResponse = $solr->query($query);
        return $queryResponse->getResponse()->response;
    }
}

?>