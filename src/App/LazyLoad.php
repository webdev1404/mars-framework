<?php
/**
* The LazyLoad Trait
* @package Mars
*/

namespace Mars\App;

use Mars\App;

/**
 * Attribute to mark properties for lazy loading
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class LazyLoadProperty
{
}

/**
 * The LazyLoad Trait
 * Trait which offers lazy load capabilities.
 * Loads properties marked by the #[LazyLoadProperty] attribute using the ghost method
 */
trait LazyLoad
{
    /**
     * The attribute used to mark the properties to be lazy loaded
     * @var string $lazyload_attribute
     */
    protected static string $lazyload_attribute = 'Mars\App\LazyLoadProperty';

    /**
     * The list of properties to be lazy loaded, with this passed as the first param to the constructor
     * @var array $lazyload_add_this
     */
    //protected static array $lazyload_add_this = [];

    /**
     * The method to be called to lazy load the properties marked with the #[LazyLoadProperty] attribute
     */
    protected function lazyLoad(App $app)
    {
        $properties = $this->getLazyLoadProperties();

        $this->lazyLoadByGhost($properties, $app);
    }

    /**
     * Returns the properties to be lazy loaded
     * @return array
     */
    protected function getLazyLoadProperties() : array
    {
        $list = [];

        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();
        
        foreach ($properties as $property) {
            //the lazyload property must be uninitialized
            if ($property->isInitialized($this)) {
                continue;
            }

            $attributes = $property->getAttributes(static::$lazyload_attribute);
            if (empty($attributes)) {
                continue;
            }

            $list[$property->getName()] = '\\' . $property->getType()->getName();
        }

        return $list;
    }

    /**
     * Lazy loads the classes by using the ghost method
     * @param array $classes The classes to lazy load
     * @param App $app The App object
     */
    protected function lazyLoadByGhost(array $classes, App $app)
    {
        foreach ($classes as $name => $class) {
            $reflector = new \ReflectionClass($class);
            $this->$name = $reflector->newLazyGhost(function ($ghost) use ($class, $app) {
                $params = $this->getLazyLoadConstructorParams($class, $app);

                $ghost->__construct(...$params);
            });
        }
    }

    /**
     * Returns the constructor params for the lazy loaded class
     * @param string $class The class to lazy load
     * @param App $app The App object
     * @return array The constructor params
     */
    protected function getLazyLoadConstructorParams(string $class, App $app) : array
    {
        $class = ltrim($class, '\\');

        // @phpstan-ignore staticProperty.notFound
        if (!empty(static::$lazyload_add_this)) {
            if (isset(static::$lazyload_add_this[$class])) {
                return [$this, $app];
            }
        }

        return [$app];
    }
}
