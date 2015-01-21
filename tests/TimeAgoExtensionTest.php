<?php

namespace Salavert\Tests;

use Salavert\Twig\Extension\TimeAgoExtension;
use Salavert\Tests\Mocks\IdentityTranslator;

class TimeAgoExtensionTest extends \PHPUnit_Framework_TestCase {
	
    public function setUp() {
        $this->translatorMock = $this->getMock('\IdentityTranslator', array('trans'));
    }

    /**
     * Reflection method to test private TimeAgoExtension methods
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name) {
        $class = new \ReflectionClass('Salavert\Twig\Extension\TimeAgoExtension');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testFutureDistanceLessThanOneMinute() {
        $this->translatorMock->expects($this->once())
            ->method('trans')
            ->with($this->equalTo('in 1 minute'));
        $extension = new TimeAgoExtension($this->translatorMock);
        $reflection = self::getMethod('future');
        $reflection->invokeArgs($extension, array(1, false, 0));
    }

    public function dataProviderLessThan46Minutes() {
        return array(
            array(2),
            array(45),
        );
    }

    /** @dataProvider dataProviderLessThan46Minutes */
    public function testFutureDistanceLessThan46Minutes($distance_in_minutes) {
        $this->translatorMock->expects($this->once())
            ->method('trans')
            ->with(
                $this->equalTo('in %minutes minutes'),
                $this->equalTo(array('%minutes' => $distance_in_minutes))
            );
        $extension = new TimeAgoExtension($this->translatorMock);
        $reflection = self::getMethod('future');
        $reflection->invokeArgs($extension, array($distance_in_minutes, false, 0));
    }

    public function dataProviderLessThanOneHour() {
        return array(
            array(46),
            array(60)
        );
    }

    /** @dataProvider dataProviderLessThanOneHour */
    public function testFutureDistanceInAboutOneHour($distance_in_minutes) {
        $this->translatorMock->expects($this->once())
            ->method('trans')
            ->with($this->equalTo('in about 1 hour'));
        $extension = new TimeAgoExtension($this->translatorMock);
        $reflection = self::getMethod('future');
        $reflection->invokeArgs($extension, array($distance_in_minutes,false,0));
    }

    public function dataProviderLessThanOneDay() {
        return array(
            array(1.5*60+1), # should be <60 min instead of <=90?
            array(24*60),
        );
    }

    /** @dataProvider dataProviderLessThanOneDay */
    public function testFutureDistanceInLessThanOneDay($distance_in_minutes) {
        $this->translatorMock->expects($this->once())
            ->method('trans')
            ->with($this->equalTo('in about %hours hours'));
        $extension = new TimeAgoExtension($this->translatorMock);
        $reflection = self::getMethod('future');
        $reflection->invokeArgs($extension, array($distance_in_minutes,false,0));
    }

    public function dataProviderInOneDay() {
        return array(
            array(24*60+1),
            array(2*24*60), # should be <1 or <1.5 day instead of <=2days?
        );
    }

    /** @dataProvider dataProviderInOneDay */
    public function testFutureDistanceInOneDay($distance_in_minutes) {
        $this->translatorMock->expects($this->once())
            ->method('trans')
            ->with($this->equalTo('in 1 day'));
        $extension = new TimeAgoExtension($this->translatorMock);
        $reflection = self::getMethod('future');
        $reflection->invokeArgs($extension, array($distance_in_minutes,false,0));
    }

    public function dataProviderMoreThanOneDay() {
        return array(
            array(2*24*60+1),
            array(999*24*60),
        );
    }

    /** @dataProvider dataProviderMoreThanOneDay */
    public function testFutureDistanceMoreThanOneDay($distance_in_minutes) {
        $this->translatorMock->expects($this->once())
            ->method('trans')
            ->with($this->equalTo('in %days days'));
        $extension = new TimeAgoExtension($this->translatorMock);
        $reflection = self::getMethod('future');
        $reflection->invokeArgs($extension, array($distance_in_minutes,false,0));
    }


}
