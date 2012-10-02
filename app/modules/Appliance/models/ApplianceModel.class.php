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
     * @param string $tag the tag
     *
     * @return array
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     */
    public function appliancesByTag($tag)
    {
        // we gonna put appliances in this array
        $appliances = [];
        // get all appliances that has this tag
        foreach ($this->redis->sMembers("tag:{$tag}") as $id) {
            // the id is like: "name:version"
            list($name, $version) = explode(':', $id, 2);
            // replace version if there's newer version available
            if (isset($appliances[$name])) {
                if (version_compare($version, $appliances[$name], '>=')) {
                    $appliances[$name] = $version;
                }
            } else {             // hmm, we don't have this appliance yet, let's add it
                $appliances[$name] = $version;
            }
        }
        return $appliances;
    }
}
