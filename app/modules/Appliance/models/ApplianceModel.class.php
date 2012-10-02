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
     * initializes model
     *
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @param $context AgaviContext the context we're in
     * @param $parameters array init parameters
     */
    public function initialize(AgaviContext $context, array $parameters = array())
    {
        parent::initialize($context, $parameters);
        $this->redis = $this->getContext()->getDatabaseManager()->getDatabase()->getConnection();
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
        return $this->redis->sMembers("tags");
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
        foreach($this->redis->sMembers("tag:{$tag}") as $id)
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
}

?>