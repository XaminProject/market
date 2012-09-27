<?php

/**
 * The base model from which all project models inherit.
 */
class MarketBaseModel extends AgaviModel
{
    /**
     * Redis connection
     *
     * @var $redis Redis
     */
    protected $redis ;
    
    public function initialize(AgaviContext $context, array $parameters = array()) 
    {
        parent::initialize($context, $parameters);
        $this->redis = $this->getContext()->getDatabaseManager()->getDatabase()->getConnection();
    }

}
