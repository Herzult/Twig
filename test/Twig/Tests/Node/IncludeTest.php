<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/TestCase.php';

class Twig_Tests_Node_IncludeTest extends Twig_Tests_Node_TestCase
{
    /**
     * @covers Twig_Node_Include::__construct
     */
    public function testConstructor()
    {
        $expr = new Twig_Node_Expression_Constant('foo.twig', 0);
        $node = new Twig_Node_Include($expr, null, 0);

        $this->assertEquals(null, $node->getNode('variables'));
        $this->assertEquals($expr, $node->getNode('expr'));

        $vars = new Twig_Node_Expression_Array(array('foo' => new Twig_Node_Expression_Constant(true, 0)), 0);
        $node = new Twig_Node_Include($expr, $vars, 0);
        $this->assertEquals($vars, $node->getNode('variables'));
    }

    /**
     * @covers Twig_Node_Include::compile
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null)
    {
        parent::testCompile($node, $source, $environment);
    }

    public function getTests()
    {
        $tests = array();

        $expr = new Twig_Node_Expression_Constant('foo.twig', 0);
        $node = new Twig_Node_Include($expr, null, 0);
        $tests[] = array($node, '$this->env->loadTemplate("foo.twig")->display($context);');

        $expr = new Twig_Node_Expression_Conditional(
                        new Twig_Node_Expression_Constant(true, 0),
                        new Twig_Node_Expression_Constant('foo', 0),
                        new Twig_Node_Expression_Constant('foo', 0),
                        0
                    );
        $node = new Twig_Node_Include($expr, null, 0);
        $tests[] = array($node, <<<EOF
\$template = (true) ? ("foo") : ("foo");
if (!\$template instanceof Twig_Template) {
    \$template = \$this->env->loadTemplate(\$template);
}
\$template->display(\$context);
EOF
        );

        $expr = new Twig_Node_Expression_Constant('foo.twig', 0);
        $vars = new Twig_Node_Expression_Array(array('foo' => new Twig_Node_Expression_Constant(true, 0)), 0);
        $node = new Twig_Node_Include($expr, $vars, 0);
        $tests[] = array($node, '$this->env->loadTemplate("foo.twig")->display(array("foo" => true));');

        return $tests;
    }
}
