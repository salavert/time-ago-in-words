<?php

namespace Salavert\Tests;

date_default_timezone_set('Europe/Madrid');

use Salavert\Twig\Extension\TimeAgoExtension;
use Symfony\Component\Translation\IdentityTranslator;

class TranslationsTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_INCLUDE_SECONDS = false;
    const DEFAULT_INCLUDE_MONTHS = false;

    const WITH_INCLUDE_SECONDS = true;
    const WITH_INCLUDE_MONTHS = true;

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var TimeAgoExtension */
    private $extension;

    public function setUp() {
        $this->extension = new TimeAgoExtension(new IdentityTranslator);
    }

    public function testExtensionName() {
        $this->assertEquals("time_ago_extension", $this->extension->getName());
    }

    /**
     * Launching tests twice: dates as strings and as DateTime objects.
     * Both arguments are accepted.
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     * @param bool $includeSeconds
     * @param bool $includeMonths
     */
    private function assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation, $includeSeconds=self::DEFAULT_INCLUDE_SECONDS, $includeMonths=self::DEFAULT_INCLUDE_MONTHS)
    {
        $fromDateTime = \DateTime::createFromFormat(self::DATE_FORMAT, $fromTime, new \DateTimeZone('GMT'));
        $toDateTime = \DateTime::createFromFormat(self::DATE_FORMAT, $toTime, new \DateTimeZone('GMT'));

        $translationFromDateStrings = $this->extension->distanceOfTimeInWordsFilter($fromTime, $toTime, $includeSeconds, $includeMonths);
        $translationFromDateTimes = $this->extension->distanceOfTimeInWordsFilter($fromDateTime, $toDateTime, $includeSeconds, $includeMonths);

        $this->assertEquals($expectedTranslation, $translationFromDateStrings);
        $this->assertEquals($expectedTranslation, $translationFromDateTimes);
    }

    /**
     * @dataProvider dataFromSecondsToOneMinuteWithIncludeSeconds
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     */
    public function testFromSecondsToOneMinuteWithIncludeSeconds($fromTime, $toTime, $expectedTranslation)
    {
        $this->assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation, self::WITH_INCLUDE_SECONDS);
    }

    /**
     * @dataProvider dataFromSecondsToOneMinuteWithoutIncludeSeconds
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     */
    public function testFromSecondsToOneMinuteWithoutIncludeSeconds($fromTime, $toTime, $expectedTranslation)
    {
        $this->assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation);
    }

    /**
     * @dataProvider dataFromMinutesToAboutOneHour
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     */
    public function testFromMinutesToAboutOneHour($fromTime, $toTime, $expectedTranslation)
    {
        $this->assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation);
    }

    /**
     * @dataProvider dataFromHoursToOneDay
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     */
    public function testFromHoursToOneDay($fromTime, $toTime, $expectedTranslation)
    {
        $this->assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation);
    }

    /**
     * @dataProvider dataFromDaysToOneYearWithoutIncludeMonths
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     */
    public function testFromDaysToOneYearWithoutIncludeMonths($fromTime, $toTime, $expectedTranslation)
    {
        $this->assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation);
    }

    /**
     * @dataProvider dataFromDaysToOneYearWithIncludeMonths
     *
     * @param $fromTime
     * @param $toTime
     * @param $expectedTranslation
     */
    public function testFromDaysToOneYearWithIncludeMonths($fromTime, $toTime, $expectedTranslation)
    {
        $this->assertDistanceOfTimeExpectation($fromTime, $toTime, $expectedTranslation, self::DEFAULT_INCLUDE_SECONDS, self::WITH_INCLUDE_MONTHS);
    }

    public function dataFromSecondsToOneMinuteWithIncludeSeconds()
    {
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

    public function dataFromSecondsToOneMinuteWithoutIncludeSeconds()
    {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 00:00:01", "less than a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:29", "less than a minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:00:30", "1 minute ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:01:29", "1 minute ago"),
        );
    }

    public function dataFromMinutesToAboutOneHour()
    {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 00:01:30", "2 minutes ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:02:29", "2 minutes ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:45:29", "45 minutes ago"),
            array("2015-07-01 00:00:00", "2015-07-01 00:45:30", "about 1 hour ago"),
            array("2015-07-01 00:00:00", "2015-07-01 01:30:29", "about 1 hour ago"),
        );
    }

    public function dataFromHoursToOneDay()
    {
        return array(
            array("2015-07-01 00:00:00", "2015-07-01 01:30:30", "about 2 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-01 02:00:29", "about 2 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-01 02:30:30", "about 3 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-01 23:30:29", "about 24 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-02 00:00:29", "about 24 hours ago"),
            array("2015-07-01 00:00:00", "2015-07-02 00:00:30", "1 day ago"),
        );
    }

    public function dataFromDaysToOneYearWithoutIncludeMonths()
    {
        return array(
            array("2015-07-01 00:00:00", "2015-07-03 00:00:29", "1 day ago"),
            array("2015-07-01 00:00:00", "2015-07-03 00:00:30", "2 days ago"),
            array("2015-07-01 00:00:00", "2015-07-16 00:00:29", "15 days ago"),
            array("2015-07-01 00:00:00", "2015-07-31 00:00:29", "30 days ago"),
            array("2015-07-01 00:00:00", "2015-08-01 00:00:00", "31 days ago"),
            # Switching from month 11 to 12
            array("2015-07-01 00:00:00", "2016-6-09 11:59:29", "344 days ago"),
            array("2015-07-01 00:00:00", "2016-6-09 11:59:30", "345 days ago"),
            # Reaching a full year
            array("2015-07-01 00:00:00", "2016-6-29 11:59:29", "364 days ago"),
            array("2015-07-01 00:00:00", "2016-6-29 11:59:30", "365 days ago"),
            # Exceeding a year by a month or so
            array("2015-07-01 00:00:00", "2016-8-04 00:00:00", "400 days ago"),
        );
    }

    public function dataFromDaysToOneYearWithIncludeMonths()
    {
        return array(
            array("2015-07-01 00:00:00", "2015-07-03 00:00:29", "1 day ago"),
            array("2015-07-01 00:00:00", "2015-07-03 00:00:30", "2 days ago"),
            array("2015-07-01 00:00:00", "2015-07-16 00:00:29", "15 days ago"),
            array("2015-07-01 00:00:00", "2015-07-31 00:00:29", "30 days ago"),
            array("2015-07-01 00:00:00", "2015-08-01 00:00:00", "1 month ago"),
            # Switching from month 11 to 12
            array("2015-07-01 00:00:00", "2016-6-09 11:59:29", "11 months ago"),
            array("2015-07-01 00:00:00", "2016-6-09 11:59:30", "1 year ago"), # Instead of 12 months ago
            # Reaching a full year
            array("2015-07-01 00:00:00", "2016-6-29 11:59:29", "1 year ago"), # Instead of 12 months ago
            # Exact moment we round distance of time and reach 365 days
            array("2015-07-01 00:00:00", "2016-6-29 11:59:30", "1 year ago"),
            # 400 days
            array("2015-07-01 00:00:00", "2016-8-04 00:00:00", "1 year ago"),
            array("2015-07-01 00:00:00", "2018-8-04 00:00:00", "3 years ago"),
        );
    }
}