<?php
/**
* The Mars Templates Engine
* @package Mars
*/

namespace Mars\Templates;

use Mars\App;
use Mars\App\InstanceTrait;
use Mars\Handlers;

/**
 * The Mars Templates Engine
 */
class Mars implements DriverInterface
{
    use InstanceTrait;
    
    /**
     * @var array $supported_structures The list of supported parsers
     */
    protected array $supported_parsers = [
        'templates' => \Mars\Templates\Mars\TemplatesParser::class,
        'include' => \Mars\Templates\Mars\IncludeParser::class,
        'variables_raw' => \Mars\Templates\Mars\VariablesRawParser::class,
        'variables' => \Mars\Templates\Mars\VariablesParser::class,
        'if' => \Mars\Templates\Mars\IfParser::class,
        'foreach' => \Mars\Templates\Mars\ForeachParser::class
    ];

    /**
     * @var Handlers $handlers The parsers object
     */
    public protected(set) Handlers $parsers {
        get {
            if (isset($this->parsers)) {
                return $this->parsers;
            }

            $this->parsers = new Handlers($this->supported_parsers, null, $this->app);

            return $this->parsers;
        }
    }

    /**
     * Builds the Mars Template object
     * @param App $app The app object
     */
    public function __construct(App $app)
    {
        ini_set('pcre.backtrack_limit', 10000000);

        $this->app = $app;
    }

    /**
     * @see \Mars\Templates\DriverInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params) : string
    {
        $parsers = $this->parsers->getAll();
        foreach ($parsers as $parser) {
            $content = $parser->parse($content, $params);
        }

        return $content;
    }

    /**
     * Returns a parser
     * @param string $name The name of the parser
     * @return object The handler
     */
    public function getParser(string $name) : object
    {
        return $this->parsers->get($name);
    }
}
