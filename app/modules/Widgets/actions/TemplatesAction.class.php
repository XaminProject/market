<?php
/**
 * Action description
 * 
 * PHP version 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Widgets_TemplatesAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Widgets_TemplatesAction extends MarketWidgetsBaseAction
{
    

    /**
     * Whether or not this action is "simple", i.e. doesn't use validation etc.
     *
     * @return     bool true, if this action should act in simple mode, or false.
     *
     */
    public function isSimple()
    {
        return true;
    }

    /**
     * Returns the default view if the action does not serve the request
     * method used.
     *
     * @return mixed <ul>
     *                <li>A string containing the view name associated
     *                   with this action; or</li>
     *                <li>An array with two indices: the parent module
     *                   of the view to be executed and the view to be
     *                   executed.</li>
     *               </ul>
     */
    public function getDefaultViewName()
    {
        //For now, just serve all mustache files in %core.app_dir%/templates/ClientSide
        //XXXX : Need' to add cache support!
        $files = glob(AgaviConfig::get('core.app_dir') . '/templates/ClientSide/*.handlebars');
        $data = array();
        foreach ($files as $file) {
            $t = str_replace(".handlebars", "", basename($file));
            $data[] = array ('name' => $t, 'content' => file_get_contents($file));
        }
        $this->setAttribute('data', $data);
        return 'Success';
    }

    /**
     * secure action?
     *
     * @return boolean
     */
    public function isSecure()
    {
        return true;
    }
}
