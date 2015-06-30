<?php

namespace Salavert\Twig\Extension;

use Symfony\Component\Translation\IdentityTranslator;

class TimeAgoExtension extends \Twig_Extension
{
    protected $translator;

    /**
     * Constructor method
     *
     * @param IdentityTranslator $translator
     */
    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('distance_of_time_in_words', array($this, 'distanceOfTimeInWordsFilter')),
            new \Twig_SimpleFilter('time_ago_in_words', array($this, 'timeAgoInWordsFilter')),
        );
    }

    /**
     * Like distance_of_time_in_words, but where to_time is fixed to timestamp()
     *
     * @param $from_time String or DateTime
     * @param bool $include_seconds
     * @param bool $include_months
     *
     * @return mixed
     */
    function timeAgoInWordsFilter($from_time, $include_seconds = false, $include_months = false)
    {
        return $this->distanceOfTimeInWordsFilter($from_time, new \DateTime('now'), $include_seconds, $include_months);
    }

    /**
     * Reports the approximate distance in time between two times given in seconds
     * or in a valid ISO string like.
     * For example, if the distance is 47 minutes, it'll return
     * "about 1 hour". See the source for the complete wording list.
     *
     * Integers are interpreted as seconds. So, by example to check the distance of time between
     * a created user an it's last login:
     * {{ user.createdAt|distance_of_time_in_words(user.lastLoginAt) }} returns "less than a minute".
     *
     * Set include_seconds to true if you want more detailed approximations if distance < 1 minute
     * Set include_months to true if you want approximations in months if days > 30
     *
     * @param $from_time String or DateTime
     * @param $to_time String or DateTime
     * @param bool $include_seconds True to return distance in seconds when it's lower than a minute.
     * @param bool $include_months
     *
     * @return mixed
     */
    public function distanceOfTimeInWordsFilter($from_time, $to_time = null, $include_seconds = false, $include_months = false)
    {
        $datetime_transformer = new \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer(null, null, 'Y-m-d H:i:s');
        $timestamp_transformer = new \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer();

        # Transforming to Timestamp
        if (!($from_time instanceof \DateTime) && !is_numeric($from_time)) {
            $from_time = $datetime_transformer->reverseTransform($from_time);
            $from_time = $timestamp_transformer->transform($from_time);
        } elseif($from_time instanceof \DateTime) {
            $from_time = $timestamp_transformer->transform($from_time);
        }

        $to_time = empty($to_time) ? new \DateTime('now') : $to_time;

        # Transforming to Timestamp
        if (!($to_time instanceof \DateTime) && !is_numeric($to_time)) {
            $to_time = $datetime_transformer->reverseTransform($to_time);
            $to_time = $timestamp_transformer->transform($to_time);
        } elseif($to_time instanceof \DateTime) {
            $to_time = $timestamp_transformer->transform($to_time);
        }

        $future = ($to_time < $from_time) ? true : false;

        $distance_in_minutes = round((abs($to_time - $from_time))/60);
        $distance_in_seconds = round(abs($to_time - $from_time));

        if($future){
            return $this->future($distance_in_minutes,$include_seconds,$distance_in_seconds);
        }

        if ($distance_in_minutes <= 1){
            if ($include_seconds){
                if ($distance_in_seconds < 5){
                    return $this->translator->trans('less than %seconds seconds ago', array('%seconds' => 5));
                }
                elseif($distance_in_seconds < 10){
                    return $this->translator->trans('less than %seconds seconds ago', array('%seconds' => 10));
                }
                elseif($distance_in_seconds < 20){
                    return $this->translator->trans('less than %seconds seconds ago', array('%seconds' => 20));
                }
                elseif($distance_in_seconds < 40){
                    return $this->translator->trans('half a minute ago');
                }
                elseif($distance_in_seconds < 60){
                    return $this->translator->trans('less than a minute ago');
                }
                else {
                    return $this->translator->trans('1 minute ago');
                }
            }
            return ($distance_in_minutes==0) ? $this->translator->trans('less than a minute ago', array()) : $this->translator->trans('1 minute ago', array());
        }
        elseif ($distance_in_minutes <= 45){
            return $this->translator->trans('%minutes minutes ago', array('%minutes' => $distance_in_minutes));
        }
        elseif ($distance_in_minutes <= 90){
            return $this->translator->trans('about 1 hour ago');
        }
        elseif ($distance_in_minutes <= 1440){
            return $this->translator->trans('about %hours hours ago', array('%hours' => round($distance_in_minutes/60)));
        }
        elseif ($distance_in_minutes <= 2880){
            return $this->translator->trans('1 day ago');
        }
        else{
            $distance_in_days = round($distance_in_minutes/1440);
            if (!$include_months || $distance_in_days <= 30) {
                return $this->translator->trans('%days days ago', array('%days' => round($distance_in_days)));
            }
            elseif ($distance_in_days < 345) {
                return $this->translator->transchoice('{1} 1 month ago |]1,Inf[ %months months ago', round($distance_in_days/30), array('%months' => round($distance_in_days/30)));
            }
            else {
                return $this->translator->transchoice('{1} 1 year ago |]1,Inf[ %years years ago', round($distance_in_days/365), array('%years' => round($distance_in_days/365)));
            }
        }
    }

    private function future($distance_in_minutes,$include_seconds,$distance_in_seconds){
        if ($distance_in_minutes <= 1){
            if ($include_seconds){
                if ($distance_in_seconds < 5){
                    return $this->translator->trans('in less than %seconds seconds', array('%seconds' => 5));
                }
                elseif($distance_in_seconds < 10){
                    return $this->translator->trans('in less than %seconds seconds', array('%seconds' => 10));
                }
                elseif($distance_in_seconds < 20){
                    return $this->translator->trans('in less than %seconds seconds', array('%seconds' => 20));
                }
                elseif($distance_in_seconds < 40){
                    return $this->translator->trans('in half a minute');
                }
                elseif($distance_in_seconds < 60){
                    return $this->translator->trans('in less than a minute');
                }
                else {
                    return $this->translator->trans('in 1 minute');
                }
            }
            return ($distance_in_minutes===0) ? $this->translator->trans('in less than a minute', array()) : $this->translator->trans('in 1 minute', array());
        }
        elseif ($distance_in_minutes <= 45){
            return $this->translator->trans('in %minutes minutes', array('%minutes' => $distance_in_minutes));
        }
        elseif ($distance_in_minutes <= 90){
            return $this->translator->trans('in about 1 hour');
        }
        elseif ($distance_in_minutes <= 1440){
            return $this->translator->trans('in about %hours hours', array('%hours' => round($distance_in_minutes/60)));
        }
        elseif ($distance_in_minutes <= 2880){
            return $this->translator->trans('in 1 day');
        }
        else{
            return $this->translator->trans('in %days days', array('%days' => round($distance_in_minutes/1440)));
        }

    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'time_ago_extension';
    }


}
