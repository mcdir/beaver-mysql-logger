<?php

namespace KZ;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $layout = new View('layout');

        $view = new View('/path/', [
            'templatesPath' => 'bbb',
            'extension' => '.ccc',
            'layout' => $layout,
            'varNameForContent' => 'bigContent'
        ]);

        $this->assertEquals('/path', $view->getTemplatesPath());
        $this->assertEquals('.ccc', $view->getExtension());
        $this->assertEquals($view->getLayout(), $layout);
        $this->assertEquals('bigContent', $view->getVarNameForContent());
    }

    public function testAbsolutePath()
    {
        $view = new View('/path/');

        $this->assertEquals('/path/sub/path.php', $view->getAbsoluteTemplatePath('sub/path'));
        $this->assertEquals('/absolute/path.php', $view->getAbsoluteTemplatePath('/absolute/path'));
        $this->assertEquals('C:\\path\\sub\\test.php', $view->getAbsoluteTemplatePath('C:\\path\\sub\\test'));
    }

    public function testAssignData()
    {
        $view = new View('/path/');

        $this->assertEquals([], $view->getData());

        $data = [
            'a' => 'b',
            'c' => 'd'
        ];

        $view->assignData($data, $view->getData());
    }

    public function testSetDataViaMethods()
    {
        $view = new View('/path/');

        $this->assertFalse($view->isSetDataViaMethods());
        $view->templatesPath = 'aaaa';

        $this->assertEquals('/path', $view->getTemplatesPath());
        $this->assertEquals($view->getData(), [
            'templatesPath' => 'aaaa'
        ]);
    }

    public function testOffsetSet()
    {
        $view = new View('/path/');
        $this->setExpectedException('OutOfBoundsException', 'Incorrect key name: "this".');
        $view['this'] = 'a';

        $this->setExpectedException('OutOfBoundsException', 'Incorrect key name: "this".');
        $view->this = 'a';
    }

    public function testRenderPartial()
    {
        $this->createTmpTpl(__DIR__ . '/tpl.php', '<html><?=$a?>,<?=intval(method_exists($this, "renderPartial"))?>');

        $view = new View(__DIR__);
        $result = $view->renderPartial('tpl', [
            'a' => 'b'
        ]);

        $this->assertEquals('<html>b,1', $result);
    }

    public function testRenderNoLayout()
    {
        $this->createTmpTpl(__DIR__ . '/tpl.php', '<html><?=$a?>,<?=intval(method_exists($this, "renderPartial"))?>');

        $view = new View(__DIR__);
        $result = $view->render('tpl', [
            'a' => 'b'
        ]);

        $this->assertEquals('<html>b,1', $result);

        $this->unlinkTpl([__DIR__ . '/tpl.php']);
    }

    public function testRenderWithLayout()
    {
        $this->createTmpTpl(__DIR__ . '/layout.php', '<html><?=$content?></html>');
        $this->createTmpTpl(__DIR__ . '/view.php', '<b><?=$test?></b>');

        $layout = new View(__DIR__);
        $layout->setLocalPath('layout');

        $view = new View(__DIR__);
        $view->setLayout($layout);

        $this->assertEquals('<html><b>123</b></html>', $view->render('view', [
            'test' => '123'
        ]));

        $this->unlinkTpl([__DIR__ . '/layout.php', __DIR__ . '/view.php']);
    }

    public function testGetHelperKitClass()
    {
        $view = new View(__DIR__);
        $this->assertEquals('\KZ\view\HelperKit', $view->getHelperKitClass());

        $helperKit = $this->getHelperKit();
        $view->setConfig([
            'helperKit' => [
                'class' => get_class($helperKit)
            ]
        ]);

        $this->assertEquals(get_class($helperKit), $view->getHelperKitClass());
    }

    public function testHelperKitException()
    {
        $this->setExpectedException('RuntimeException', 'Class "bbb" does not exist.');

        $view = new View(__DIR__, [
            'helperKit' => [
                'class' => 'bbb'
            ]
        ]);
        $view->getHelperKit();
    }

    public function testSameInstanceHelperKit()
    {
        $view = new View(__DIR__);
        $helper = $view->getHelperKit();

        $view2 = new View(__DIR__);

        $this->assertTrue($helper == $view2->getHelperKit());
    }

    protected function unlinkTpl($path)
    {
        if (!is_array($path))
            $path = [$path];

        foreach ($path as $item)
            if (file_exists($item))
                unlink($item);
    }

    protected function createTmpTpl($path, $content)
    {
        $this->unlinkTpl($path);

        file_put_contents($path, $content);
    }

    /**
     * @return \KZ\view\HelperKit
     */
    protected function getHelperKit()
    {
        return $this->getMock('\KZ\view\HelperKit');
    }
} 
