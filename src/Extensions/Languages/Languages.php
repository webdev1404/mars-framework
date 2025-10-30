<?php
/**
* The Languages Class
* @package Mars
*/

namespace Mars\Extensions\Languages;

use Mars\Extensions\Extensions;

/**
 * The Languages Class
 */
class Languages extends Extensions
{
    /**
     * @internal
     */
    protected static ?array $list_enabled = null;

    /**
     * @internal
     */
    protected static ?array $list_all = null;

    /**
     * @internal
     */
    protected static string $list_config_file = 'languages.php';

    /**
     * @internal
     */
    protected static string $instance_class = Language::class;

    /**
     * @see Extensions::addConfig()
     * {@inheritdoc}
     */
    protected function addConfig(string $name)
    {
        $extensions = $this->app->config->read(static::$list_config_file);

        $code = $this->getCode($name);
        $extensions[$code] = $name;
        $extensions = array_unique($extensions);

        $this->app->config->write(static::$list_config_file, $extensions);
    }

    /**
     * Returns the code of a language
     * @param string $name The language name
     * @return string The language code
     * @throws \Exception
     */
    protected function getCode($name) : string
    {
        $info = $this->getInfo($name);
        if (empty($info['code'])) {
            throw new \Exception("Language {$name} does not have a code defined in its info file.");
        }

        return $info['code'];
    }
}
