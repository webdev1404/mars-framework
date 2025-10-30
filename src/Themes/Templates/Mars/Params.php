<?php
/**
* The Params Class
* @package Mars
*/

namespace Mars\Themes\Templates\Mars;

use Mars\App\Kernel;

/**
 * The Params Class
 */
abstract class Params
{
    use Kernel;

    /**
     * Gets a variable's name
     * @param string $value The value
     * @param string $delim The delimiter
     * @return string The parsed value
     */
    protected function getName(string $value, string $delim) : string
    {
        $value = trim($value);

        if ($delim) {
            if ($delim === '"') {
                $value = str_replace("\\\"", "\"", $value);
            } else {
                $value = str_replace("\\'", "'", $value);
            }

            return str_replace('"', '\\"', $value);
        }

        return $value;
    }

    protected function getValue(string $value, string $delim) : string
    {
        $value = $this->getName($value, $delim);
     
        $add_brackets = $delim ? true : false;
        $vars = new VariablesParser($this->app);
        $value = $vars->replaceAll($value, $add_brackets);

        if ($delim) {
            $value = '"' . $value . '"';
        }

        return $value;
    }
}
