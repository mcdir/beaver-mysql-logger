<?php

namespace KZ\view\helpers;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    public function testGetModelPrefix()
    {
        $html = new Html();
        $this->assertEquals('KZ_Model', $html->getModelPrefix('KZ\Model'));
    }

    public function testName()
    {
        $html = new Html();
        $this->assertEquals('KZ_Model[test]', $html->name('KZ\Model', 'test'));
        $this->assertEquals('KZ_Model[test][sub]', $html->name('KZ\Model', 'test[sub]'));
        $this->assertEquals('KZ_Model[test][sub][a]', $html->name('KZ\Model', 'test[sub][a]'));
        $this->assertEquals('KZ_Model[test][sub][0]', $html->name('KZ\Model', 'test[sub][0]'));
        $this->assertEquals('KZ_Model[test][0]', $html->name('KZ\Model', 'test[0]'));
    }

    public function testGetTagAttrs()
    {
        $html = new Html();
        $this->assertEquals('a="b" c="d"', $html->getTagAttrs(['a' => 'b', 'c' => 'd']));
        $this->assertEquals('a="&#039;b&gt;&lt;" c="d"', $html->getTagAttrs(['a' => '\'b><', 'c' => 'd']));
    }

    public function testId()
    {
        $html = new Html();
        $this->assertEquals('KZ_Model_test', $html->id('KZ\Model', 'test'));
        $this->assertEquals('KZ_Model_test_sub', $html->id('KZ\Model', 'test[sub]'));
        $this->assertEquals('KZ_Model_test_sub_a', $html->id('KZ\Model', 'test[sub][a]'));
        $this->assertEquals('KZ_Model_test_sub_0', $html->id('KZ\Model', 'test[sub][0]'));
        $this->assertEquals('KZ_Model_test_0', $html->id('KZ\Model', 'test[0]'));
    }

    /**
     * @param array $rules
     * @param array $methods
     * @param bool $rulesExpects
     * @return Model
     */
    protected function makeModelMockup(array $rules = null, array $methods = ['rules'], $rulesExpects = false)
    {
        if ($rulesExpects === false)
            $rulesExpects = $this->any();

        if (is_null($rules))
            $rules = [
                'name' => [],
                'surname' => []
            ];

        $model = $this->getMock('\KZ\Model', $methods);

        $model
            ->expects($rulesExpects)
            ->method('rules')
            ->will($this->returnValue($rules));

        foreach (array_keys($rules) as $attr)
            $model->{$attr} = null;

        return $model;
    }
}
