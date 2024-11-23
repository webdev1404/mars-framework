<?php
/**
* The Proxy LazyLoad Trait
* @package Mars
*/

namespace Mars\LazyLoad;

use Mars\App;

/**
* The Proxy LazyLoad Trait
 * Trait which lazy loads properties
 */
trait ProxyTrait
{
    /**
     * The closures used to lazy load the dynamic objects
     * @var array $lazyload_closures
     */
    protected static array $lazyload_closures = [];

    /**
     * Lazy loads the classes by using the proxy method
     * @param array $callables The callables which return the objects to lazy load
     * @param App $app The App object
     */
    protected function lazyLoadByProxy(array $callables, App $app)
    {
        foreach ($callables as $name => $callable) {            
            if (property_exists($this, $name)) {
                //if the property actually exists, use the callable and newLazyProxy
                $rp = new \ReflectionProperty($this, $name);
                $class = '\\' . $rp->getType()->getName();
    
                $reflector = new \ReflectionClass($class);
                $this->$name = $reflector->newLazyProxy(function($ghost) use ($app, $callable){
                    return $callable($app);
                });
            } else {
                //if the property doesn't exist, store the callable in the closures array and load it with __get
                static::$lazyload_closures[$name] = $callable;  
            }
        }
    }

    /**
     * Magic method to get the dynamic lazy loaded properties
     * @param string $name The name of the property
     * @return mixed The property value
     */
    public function __get(string $name) : mixed
    {
        if (!isset(static::$lazyload_closures[$name])) {
            throw new \Exception("The property {$name} is not lazy loaded");      
        }

        $closure = static::$lazyload_closures[$name];        
        $this->$name = $closure($this);

        return $this->$name;
    }
}