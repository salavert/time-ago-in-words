<?php

namespace Salavert\Tests;

use Salavert\Twig\Extension\TimeAgoExtension;
use Symfony\Component\Translation\IdentityTranslator;

class TranslationsTest extends \PHPUnit_Framework_TestCase
{
    const WITH_INCLUDE_SECONDS = true;
    const WITH_INCLUDE_MONTHS = true;

    /** @var TimeAgoExtension */
    private $extension;

    public function setUp() {
        #$this->translatorMock = $this->getMock('IdentityTranslator');
        $this->extension = new TimeAgoExtension(new IdentityTranslator);
    }

    public function testExtensionName() {
        $this->assertEquals("time_ago_extension", $this->extension->getName());
    }

    /** @dataProvider dataFromSecondsToOneMinuteWithIncludeSeconds */
    public function testFromSecondsToOneMinuteWithIncludeSeconds($fromTime, $toTime, $expectedTranslation) {
        $this->assertEquals($expectedTranslation, $this->extension->distanceOfTimeInWordsFilter($fromTime, $toTime, self::WITH_INCLUDE_SECONDS));
    }

    /** @dataProvider dataFromSecondsToOneMinuteWithoutIncludeSeconds */
    public function testFromSecondsToOneMinuteWithoutIncludeSeconds($fromTime, $toTime, $expectedTranslation) {
        $this->assertEquals($expectedTranslation, $this->extension->distanceOfTimeInWordsFilter($fromTime, $toTime));
    }

    /** @dataProvider dataFromMinutesToAboutOneHour */
    public function testFromMinutesToAboutOneHour($fromTime, $toTime, $expectedTranslation) {
        $this->assertEquals($expectedTranslation, $this->extension->distanceOfTimeInWordsFilter($fromTime, $toTime));
    }

    /** @dataProvider dataFromHoursToOneDay */
    public function testFromHoursToOneDay($fromTime, $toTime, $expectedTranslation) {
        $this->assertEquals($expectedTranslation, $this->extension->distanceOfTimeInWordsFilter($fromTime, $toTime));
    }

    /** @dataProvider dataFromDaysToOneMonth */
    public function testFromDaysToOneMonth($fromTime, $toTime, $expectedTranslation) {
        $this->assertEquals($expectedTranslation, $this->extension->distanceOfTimeInWordsFilter($fromTime, $toTime));
    }

    public function dataFromSecondsToOneMinuteWithIncludeSeconds() {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 00:00:04", "less than 5 seconds ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:05", "less than 10 seconds ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:09", "less than 10 seconds ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:10", "less than 20 seconds ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:20", "half a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:39", "half a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:40", "less than a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:59", "less than a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:01:00", "1 minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:01:29", "1 minute ago"),
        );
    }

    public function dataFromSecondsToOneMinuteWithoutIncludeSeconds() {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 00:00:01", "less than a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:29", "less than a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:30", "1 minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:01:29", "1 minute ago"),
        );
    }

    public function dataFromMinutesToAboutOneHour() {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 00:01:30", "2 minutes ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:02:29", "2 minutes ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:45:29", "45 minutes ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:45:30", "about 1 hour ago"),
            array("2015-07-01 00:00:00", "2015-07-01 01:30:29", "about 1 hour ago"),
        );
    }

    public function dataFromHoursToOneDay() {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 01:30:30", "about 2 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-01 02:00:29", "about 2 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-01 02:30:30", "about 3 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-01 23:30:29", "about 24 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-02 00:00:29", "about 24 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-02 00:00:30", "1 day ago"),
        );
    }

    public function dataFromDaysToOneMonth() {
        return array(
            array("2015-07-01 00:00:00", "2015-07-02 00:00:30", "1 day ago"),
        );
    }
}