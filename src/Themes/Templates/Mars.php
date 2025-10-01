<?php
/**
* The Mars Templates Engine
* @package Mars
*/

namespace Mars\Themes\Templates;

use Mars\App;
use Mars\App\Kernel;
use Mars\App\Handlers;

/**
 * The Mars Templates Engine
 */
class Mars implements TemplateInterface
{
    use Kernel;
    
    /**
     * @var array $supported_structures The list of supported parsers
     */
    protected array $supported_parsers = [
        'templates' => \Mars\Themes\Templates\Mars\TemplatesParser::class,
        'include' => \Mars\Themes\Templates\Mars\IncludeParser::class,
        'variables_raw' => \Mars\Themes\Templates\Mars\VariablesRawParser::class,
        'variables' => \Mars\Themes\Templates\Mars\VariablesParser::class,
        'if' => \Mars\Themes\Templates\Mars\IfParser::class,
        'foreach' => \Mars\Themes\Templates\Mars\ForeachParser::class
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
     * @see \Mars\Templates\TemplateInterface::parse()
     * {@inheritdoc}
     */
    public function parse(string $content, array $params) : string
    {
        foreach ($this->parsers as $parser) {
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
