<?php
/**
 * Action description
 * 
 * PHP version 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubifd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Widgets_PaginatorAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubifd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Widgets_PaginatorAction extends MarketWidgetsBaseAction
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
		$validParams = array('perpage', 'total', 'current', 'route', 'base', 'param','class');
		foreach ($validParams as $param) {
            $$param = $this->getContainer()->getArguments()->getParameter($param);
        }
        if (!is_array($base)) {
            $base = json_decode($base);
            if (!is_array($base)) {
                $base = array();
            }
        }
        $pager = $this->_buildPaginatorArray($perpage, $total, $current, $route, $param, $base, $class);
        $this->setAttribute('nopager', $pager == array());
        $this->setAttribute('pager', $pager);
        $this->setAttribute('class', $class);
        return 'Success';
    }

    /**
     * secure action?
     *
     * @return boolean
     */
    public function isSecure()
    {
        return false;
    }

    /**
     * Where the magic happen
     *
     * Very bad code and poor choice of variable name. but I write this years ago and 
     * don't want to mess with that again.
     *
     * @param int    $perpage   item per page
     * @param int    $total     total items count
     * @param int    $current   current page
     * @param string $route     Route name to generate links
     * @param string $param     Request parameter to use as page number
     * @param array  $baseParam base parameter to generate current route
     * 
     * @return void
     * @author fzerorubigd <fzerorubigd@gmail.com>
     */
    private function _buildPaginatorArray($perpage, $total, $current, $route, $param, array $baseParam = array())
    {
        $tm = $this->getContext()->getTranslationManager();
        $ro = $this->getContext()->getRouting();
        if ($perpage < 1) {
            $perpage = 1;
        }

        $nb = ceil($total / $perpage);
        $cp = $current;

        $result = array();
        $sep = false;
        $c = false;
        if ($cp == 1 || $nb <= 1) {
            $c = 'disabled';
        }
        $baseParam[$param] = $cp - 1;
        $result[] = array(
            'idx' => $tm->_("Previus &larr;"),
            'start' => ($cp - 1) * $perpage,
            'limit' => $perpage,
            'cls' => $c,
            'link' => ($c == 'disabled') ? '#' : $ro->gen($route, $baseParam),
            );
        for ($i = 0; $i < $nb; $i++) {
            $cls = false;
            if ($cp == $i + 1) {
                $cls = 'active';
            }
            if ( $i == 0 || $i == 1 || $i == $nb - 1 || $i == $nb - 2 
                || ($cp - $i <= 6 && $cp - $i >= 0) || ( $i - $cp < 5 && $i - $cp >= 0)
            ) {
                if ($sep) {
                    $result[] = array(
                        'idx' => "...",
                        'start' => 0,
                        'limit' => $perpage,
                        'cls' => 'disabled',
                        'link' => '#',
                        );
                }
                $sep = false;
                $baseParam[$param] = $i + 1;
                $result[] = array(
                    'idx' => $i + 1,
                    'start' => $i * $perpage,
                    'limit' => $perpage,
                    'cls' => $cls,
                    'link' => ($cls == 'disabled') ? '#' : $ro->gen($route, $baseParam),
                    );
            } else {
                $sep = true;
            }
        }

        $c = false;
        if ($cp == $nb || $nb <= 1) {
            $c = 'disabled';
        }
        $baseParam[$param] = $cp + 1;
        $result[] = array(
            'idx' => $tm->_("&rarr; Next"),
            'start' => ($cp + 1) * $perpage,
            'limit' => $perpage,
            'cls' => $c,
            'link' => ($c == 'disabled') ? '#' : $ro->gen($route, $baseParam),
            );
        
        if (count($result) == 3 ) {
            $result = array();
        }
        return $result;
    }
}
