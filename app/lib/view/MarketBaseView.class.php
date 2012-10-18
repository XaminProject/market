<?php
/**
 * The base view from which all project views inherit.
 *
 * PHP version 5.3
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
 * Base view class
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class MarketBaseView extends AgaviView
{
    /**
     * slot layout name
     */
    const SLOT_LAYOUT_NAME = 'slot';
    /**
     * namespace to save one-shot messages
     */
    const ONE_SHOT_NAMESPACE = 'one.shot.message';
    /**
     * Handles output types that are not handled elsewhere in the view. The
     * default behavior is to simply throw an exception.
     *
     * @param AgaviRequestDataHolder $rd The request data associated with
     *                                    this execution.
     *
     * @return void
     * @throws AgaviViewException if the output type is not handled.
     */
    public final function execute(AgaviRequestDataHolder $rd)
    {
        throw new AgaviViewException(
            sprintf(
                'The view "%1$s" does not implement an "execute%3$s()" method to serve '.
                'the output type "%2$s", and the base view "%4$s" does not implement an '.
                '"execute%3$s()" method to handle this situation.',
                get_class($this),
                $this->container->getOutputType()->getName(),
                ucfirst(strtolower($this->container->getOutputType()->getName())),
                get_class()
            )
        );
    }

    /**
     * Prepares the HTML output type.
     *
     * @param AgaviRequestDataHolder $rd         The request data associated with this execution.
     * @param string                 $layoutName The layout to load.
     *
     * @return void
     */
    public function setupHtml(AgaviRequestDataHolder $rd, $layoutName = null)
    {
        if ($layoutName === null && $this->getContainer()->getParameter('is_slot', false)) {
            // it is a slot, so we do not load the default layout, but a different one
            // otherwise, we could end up with an infinite loop
            $layoutName = self::SLOT_LAYOUT_NAME;
        }

        // now load the layout
        // this method returns an array containing the parameters that were declared on the layout (not on a layer!) in output_types.xml
        // you could use this, for instance, to automatically set a bunch of CSS or Javascript includes based on layout parameters -->
        $this->loadLayout($layoutName);
    }

    /**
     * Register comment slot
     *
     * @param string $scope    comment scope, action name is best option
     * @param string $redirect url that we should redirect user to, after saving comment
     * @param int    $page     page number 
     *
     * @return void
     */
    public function registerCommentSlot($scope, $redirect=null, $page = 1)
    {
        $this->getLayer('content')->setSlot(
            'comments',
            $this->createSlotContainer(
                'Comments',
                'Index',
                array(
                    'scope' => $scope,
                    'redirect' => is_null($redirect)?$this->getContext()->getRouting()->gen(null):$redirect,
                    'page' => $page
                ),
                'html',
                'read'
            )
        );
    }
    
    /**
     * Register paginator slot
     *
     * @param string $class   Paginator class
     * @param int    $total   total items count
     * @param int    $current current page number
     * @param string $param   Parameter name for generate route
     * @param string $route   Agavi route name
     * @param int    $perpage Item per page 0 for config value
     * @param string $slot    Slot name
     * 
     * @return void
     */
    public function registerPaginatorSlot($class, $total, $current, $param, $route = null, $perpage = 0, $slot = 'paginator')
    {
        $parameters = array(
            'perpage' => $perpage ? $perpage : AgaviConfig::get('xamin.per_page', 10), 
            'total' => $total, 
            'current' => $current, 
            'route' => $route, 
            'param' => $param,
            'class' => $class
            );

		$this->getLayer('content')->setSlot($slot, $this->createSlotContainer('Widgets', 'Paginator', $parameters));
    }

    /**
     * Helper function to store a message in session for use in next time
     *
     * @param string $message message string
     * @param string $class   class string, like error message info etc...
     * @param string $prefix  prefix
     *
     * @return void
     */
    public function setOneShotMessage($message, $class = 'message', $prefix = '')
    {
        $this->getContext()->getStorage()->write($prefix . self::ONE_SHOT_NAMESPACE, array('class' => $class, 'message' => $message));
    }

    /**
     * Helper function to get last stored one-shot message and also set them as attribute
     *
     * @param string $prefix prefix
     *
     * @return array
     */
    public function getOneShotMessage($prefix = '')
    {
        $result = $this->getContext()->getStorage()->remove($prefix . self::ONE_SHOT_NAMESPACE);
        if ($result) {
            if (isset($result['class'])) {
                $this->setAttribute($prefix . 'osm_class', $result['class']);
            }
            if (isset($result['message'])) {
                $this->setAttribute($prefix . 'osm_message', $result['message']);
            }
        }
        return $result;
    }

    /**
     * Default execute json method
     *
     * @param AgaviRequestDataHolder $rd Request data
     *
     * @return string
     */
    public function executeJson(AgaviRequestDataHolder $rd)
    {
        //In this case we need to serve the attributes in json
        $attributes = $this->getAttributes();
        foreach ($attributes as $key => &$value ) {
            if (is_object($value)) {
                if (is_callable(array($value, '__toString'))) {
                    $value = $value->__toString();
                }                    
            }
        }
        return json_encode($attributes);
    }
}
