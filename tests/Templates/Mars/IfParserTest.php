<?php

use Mars\Templates\Mars\IfParser;

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class IfParserTest extends Base
{
    public function testParse()
    {
        $parser = new IfParser($this->app);

        $this->assertSame($parser->parse('{% if ($cond) %}'), '<?php if($cond){ ?>');
        $this->assertSame($parser->parse('{% if ($foo && $bar) %}'), '<?php if($foo && $bar){ ?>');
        $this->assertSame($parser->parse('{% if ($foo && trim($bar)) %}'), '<?php if($foo && trim($bar)){ ?>');

        $this->assertSame($parser->parse('{% elseif ($cond) %}'), '<?php } elseif($cond){ ?>');

        $this->assertSame($parser->parse('{% else %}'), '<?php } else { ?>');

        $this->assertSame($parser->parse('{% endif %}'), '<?php } ?>');
    }
}
