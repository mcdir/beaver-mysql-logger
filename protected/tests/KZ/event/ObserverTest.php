<?php

namespace KZ\event;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    public function testBindEvents()
    {
        $events = [
            [
                'Test',
                'test',
                function () {
                    return 'a';
                }
            ],
            [
                'Test',
                'test',
                function () {
                    return 'b';
                }
            ],
        ];

        $observer = new Observer($events);
        foreach ($observer->getListeners('Test', 'test') as $key => $event) {
            $this->assertEquals(
                call_user_func($events[$key][2]),
                call_user_func($event)
            );
        }

        $observer->unbind();
        $this->assertEquals([], $observer->getListeners());
        $this->assertEquals(false, $observer->getListeners('Test'));
        $this->assertEquals(false, $observer->getListeners('Test', 'test'));
        $this->assertEquals(false, $observer->getListeners('Test', 'test', 0));

        $observer->bindEvents($events);
        $this->assertInternalType('callable', $observer->getListeners('Test', 'test', 0));
        $this->assertInternalType('callable', $observer->getListeners('Test', 'test', 1));
        $this->assertInternalType('array', $observer->getListeners('Test', 'test'));
        $this->assertInternalType('array', $observer->getListeners('Test'));

        foreach ($observer->getListeners('Test') as $key => $val) {
            $this->assertEquals('test', $key);
            foreach ($val as $eventKey => $event) {
                $this->assertEquals(
                    call_user_func($events[$eventKey][2]),
                    call_user_func($event)
                );
            }
        }
    }

    /**
     * Also we test "prepareClass" - events bound to stdClass, but triggered by Mock_*** class ($sender)
     */
    public function testTrigger()
    {
        $called = 0;
        $sender = $this->getMock('\StdClass');

        $observer = new Observer([
            [
                'stdClass',
                'test',
                function ($event) use ($sender, &$called) {
                    $this->assertInstanceOf('\KZ\event\interfaces\Event', $event);
                    $this->assertFalse($event->isDefaultPrevented());
                    $event->preventDefault();
                    $this->assertTrue($event->isDefaultPrevented());
                    $this->assertEquals($sender, $event->getSender());
                    $this->assertEquals('b', $event->getParam('a'));
                    $called++;
                }
            ],
            [
                'stdClass',
                'test',
                function ($event) use ($sender, &$called) {
                    $this->assertInstanceOf('\KZ\event\interfaces\Event', $event);
                    $this->assertTrue($event->isDefaultPrevented());
                    $this->assertEquals($sender, $event->getSender());
                    $called++;
                }
            ],
        ]);


        $event = $observer->trigger($sender, 'test', ['a' => 'b']);
        $this->assertInstanceOf('\KZ\event\interfaces\Event', $event);
        $this->assertTrue($event->isDefaultPrevented());
        $this->assertEquals(2, $called);
    }

    public function testBindByObject()
    {
        $stdMock = $this->getMock('\StdClass', ['test']);
        $stdMock
            ->expects($this->once())
            ->method('test');
        $callback = function () use ($stdMock) {
            $stdMock->test();
        };

        $obj = new \StdClass;
        $observer = new Observer([
            [$obj, 'test', $callback]
        ]);
        $observer->trigger($obj, 'test');
    }
}
