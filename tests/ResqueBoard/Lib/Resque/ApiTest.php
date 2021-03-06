<?php
/**
 * Api Test file
 *
 * Test the Resque API class
 *
 * PHP version 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author        Wan Qi Chen <kami@kamisama.me>
 * @copyright     Copyright 2013, Wan Qi Chen <kami@kamisama.me>
 * @link          http://resqueboard.kamisama.me
 * @since         2.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace ResqueBoard\Lib\Resque;

/**
 * ApiTest Class
 *
 * Test the Resque API class
 *
 * @author Wan Qi Chen <kami@kamisama.me>
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mock = $this->getMock('ResqueBoard\Lib\Resque\Api', array('sendSignal'));
        $this->mock->expects($this->any())->method('sendSignal')->will($this->returnValue(true));

        $this->validWorkerId = 'hostname:9999999:queue';
        $this->invalidWorkerId = 'hostname:_78:queue';

        $this->getProcessIdReflection = new \ReflectionMethod('ResqueBoard\Lib\Resque\Api', 'getProcessId');
        $this->getProcessIdReflection->setAccessible(true);

        $this->sendSignalReflection = new \ReflectionMethod('ResqueBoard\Lib\Resque\Api', 'sendSignal');
        $this->sendSignalReflection->setAccessible(true);
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::stop
     */
    public function testStopWorker()
    {
        $this->assertTrue($this->mock->stop($this->validWorkerId));
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::pause
     */
    public function testPauseWorker()
    {
        $this->assertTrue($this->mock->pause($this->validWorkerId));
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::resume
     */
    public function testResumeWorker()
    {
        $this->assertTrue($this->mock->resume($this->validWorkerId));
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::stop
     * @expectedException   ResqueBoard\Lib\Resque\InvalidWorkerNameException
     */
    public function testStopWorkerWithInvalidWorkerId()
    {
        $this->mock->stop($this->invalidWorkerId);
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::pause
     * @expectedException   ResqueBoard\Lib\Resque\InvalidWorkerNameException
     */
    public function testPauseWorkerWithInvalidWorkerId()
    {
        $this->mock->pause($this->invalidWorkerId);
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::resume
     * @expectedException   ResqueBoard\Lib\Resque\InvalidWorkerNameException
     */
    public function testResumeWorkerWithInvalidWorkerId()
    {
        $this->mock->resume($this->invalidWorkerId);
    }


    /**
     * @covers ResqueBoard\Lib\Resque\Api::getProcessId
     */
    public function testGetProcessId()
    {
        $this->assertEquals('125', $this->getProcessIdReflection->invoke(new Api(), 'hostname:125:queue'));
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::getProcessId
     * @expectedException   ResqueBoard\Lib\Resque\InvalidWorkerNameException
     */
    public function testGetProcessIdWithInvalidWorkerIdThatHasANumericButNegativeProcessId()
    {
        $this->getProcessIdReflection->invoke(new Api(), 'hostname:-125');
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::getProcessId
     * @expectedException   ResqueBoard\Lib\Resque\InvalidWorkerNameException
     */
    public function testGetProcessIdWithInvalidWorkerIdThatHasMoreTokensThanExpected()
    {
        $this->getProcessIdReflection->invoke(new Api(), 'hostname:125:as:25');
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::getProcessId
     * @expectedException   ResqueBoard\Lib\Resque\InvalidWorkerNameException
     */
    public function testGetProcessIdWithInvalidWorkerIdThatHasLessTokensThanExpected()
    {
        $this->getProcessIdReflection->invoke(new Api(), 'hostname:125');
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::sendSignal
     */
    public function testsendSignalKillANonExistentProcess()
    {
        $this->assertRegExp('/kill/', $this->sendSignalReflection->invoke(new Api(), '9999999', 'SIGTERM'));
    }

    /**
     * @covers ResqueBoard\Lib\Resque\Api::sendSignal
     */
    public function testsendSignalKillAProcessWithWrongPermission()
    {
        $this->assertRegExp('/kill/', $this->sendSignalReflection->invoke(new Api(), '1', 'SIGTERM'));
    }
}
