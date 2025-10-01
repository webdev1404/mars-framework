<?php
/**
* The Reflection Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Closure;
use ReflectionFunctionAbstract;
use ReflectionFunction;
use ReflectionMethod;

/**
 * The Reflection Class
 * Reflects on classes, methods, and properties
 */
class Reflection
{
    use Kernel;

    /**
     * Matches the given params to the method parameters, based on their type hints
     * @param ReflectionFunctionAbstract|Closure|array $method The method reflection
     * @param array $params The params to match
     * @return array The matched params
     */
    public function getParams(ReflectionFunctionAbstract|Closure|array $method, array $params) : array
    {
        if (!$params) {
            return [];
        }

        $method_obj = null;
        if ($method instanceof Closure) {
            $method_obj = new ReflectionFunction($method);
        } elseif (is_array($method)) {
            $method_obj = new ReflectionMethod($method[0], $method[1]);
        } else {
            $method_obj = $method;
        }

        $matched_params = [];
        $rparams = $method_obj->getParameters();
        foreach ($rparams as $i => $rparam) {
            $param = $params[$i] ?? null;

            if ($rparam->hasType()) {
                $type = (string)$rparam->getType();
                if (in_array($type, ['int', 'float', 'string', 'boolean'])) {
                    settype($param, $type);
                }
            }

            $matched_params[$i] = $param;
        }

        return $matched_params;
    }
}
