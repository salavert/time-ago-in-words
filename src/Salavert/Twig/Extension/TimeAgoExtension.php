<?php

namespace Salavert\Twig\Extension;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Translation\TranslatorInterface;

class TimeAgoExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor method
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('distance_of_time_in_words', [$this, 'distanceOfTimeInWordsFilter']),
            new \Twig_SimpleFilter('time_ago_in_words', [$this, 'timeAgoInWordsFilter']),
        ];
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
    public function timeAgoInWordsFilter($from_time, $include_seconds = false, $include_months = false)
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
     * a created user and their last login:
     * {{ user.createdAt|distance_of_time_in_words(user.lastLoginAt) }} returns "less than a minute".
     *
     * Set include_seconds to true if you want more detailed approximations if distance < 1 minute
     * Set include_months to true if you want approximations in months if days > 30
     *
     * @param string|\DateTimeInterface $from_time
     * @param string|\DateTimeInterface $to_time
     * @param bool             $include_seconds True to return distance in seconds when it's lower than a minute.
     * @param bool             $include_months
     *
     * @return string
     */
    public function distanceOfTimeInWordsFilter(
        $from_time,
        $to_time = null,
        $include_seconds = false,
        $include_months = false
    ) {
        $datetime_transformer = new DateTimeToStringTransformer(null, null, 'Y-m-d H:i:s');
        $timestamp_transformer = new DateTimeToTimestampTransformer();

        // Transform “from” to timestamp
        if ($from_time instanceof \DateTimeInterface) {
            $from_time = $timestamp_transformer->transform($from_time);
        } elseif (!is_numeric($from_time)) {
            $from_time = $datetime_transformer->reverseTransform($from_time);
            $from_time = $timestamp_transformer->transform($from_time);
        }

        $to_time = empty($to_time) ? new \DateTime('now') : $to_time;

        // Transform “to” to timestamp
        if ($to_time instanceof \DateTimeInterface) {
            $to_time = $timestamp_transformer->transform($to_time);
        } elseif (!is_numeric($to_time)) {
            $to_time = $datetime_transformer->reverseTransform($to_time);
            $to_time = $timestamp_transformer->transform($to_time);
        }

        $future = ($to_time < $from_time);

        $distance_in_minutes = round((abs($to_time - $from_time))/60);
        $distance_in_seconds = round(abs($to_time - $from_time));

        if ($future) {
            return $this->future($distance_in_minutes, $include_seconds, $distance_in_seconds);
        }

        if ($distance_in_minutes <= 1) {
            if ($include_seconds) {
                if ($distance_in_seconds < 5) {
                    return $this->translator->trans('less than %seconds seconds ago', ['%seconds' => 5]);
                }
                if ($distance_in_seconds < 10) {
                    return $this->translator->trans('less than %seconds seconds ago', ['%seconds' => 10]);
                }
                if ($distance_in_seconds < 20) {
                    return $this->translator->trans('less than %seconds seconds ago', ['%seconds' => 20]);
                }
                if ($distance_in_seconds < 40) {
                    return $this->translator->trans('half a minute ago');
                }
                if ($distance_in_seconds < 60) {
                    return $this->translator->trans('less than a minute ago');
                }

                return $this->translator->trans('1 minute ago');
            }

            return ($distance_in_minutes == 0)
                ? $this->translator->trans('less than a minute ago')
                : $this->translator->trans('1 minute ago')
            ;
        }

        if ($distance_in_minutes <= 45) {
            return $this->translator->trans('%minutes minutes ago', ['%minutes' => $distance_in_minutes]);
        }

        if ($distance_in_minutes <= 90) {
            return $this->translator->trans('about 1 hour ago');
        }

        if ($distance_in_minutes <= 1440) {
            return $this->translator->trans(
                'about %hours hours ago',
                ['%hours' => round($distance_in_minutes/60)]
            );
        }

        if ($distance_in_minutes <= 2880) {
            return $this->translator->trans('1 day ago');
        }

        $distance_in_days = round($distance_in_minutes/1440);

        if (!$include_months || $distance_in_days <= 30) {
            return $this->translator->trans('%days days ago', ['%days' => round($distance_in_days)]);
        }

        if ($distance_in_days < 345) {
            return $this->translator->transchoice(
                '{1} 1 month ago |]1,Inf[ %months months ago',
                round($distance_in_days/30),
                ['%months' => round($distance_in_days/30)]
            );
        }

        return $this->translator->transchoice(
            '{1} 1 year ago |]1,Inf[ %years years ago',
            round($distance_in_days/365),
            ['%years' => round($distance_in_days/365)]
        );
    }

    /**
     * @param int  $distance_in_minutes
     * @param bool $include_seconds     True to return distance in seconds when it's lower than a minute.
     * @param bool $include_months
     *
     * @return mixed
     */
    private function future($distance_in_minutes, $include_seconds, $distance_in_seconds)
    {
        if ($distance_in_minutes <= 1) {
            if ($include_seconds) {
                if ($distance_in_seconds < 5) {
                    return $this->translator->trans('in less than %seconds seconds', ['%seconds' => 5]);
                }

                if ($distance_in_seconds < 10) {
                    return $this->translator->trans('in less than %seconds seconds', ['%seconds' => 10]);
                }

                if ($distance_in_seconds < 20) {
                    return $this->translator->trans('in less than %seconds seconds', ['%seconds' => 20]);
                }

                if ($distance_in_seconds < 40) {
                    return $this->translator->trans('in half a minute');
                }

                if ($distance_in_seconds < 60) {
                    return $this->translator->trans('in less than a minute');
                }

                return $this->translator->trans('in 1 minute');
            }

            return ($distance_in_minutes === 0)
                ? $this->translator->trans('in less than a minute')
                : $this->translator->trans('in 1 minute')
            ;
        }

        if ($distance_in_minutes <= 45) {
            return $this->translator->trans('in %minutes minutes', ['%minutes' => $distance_in_minutes]);
        }

        if ($distance_in_minutes <= 90) {
            return $this->translator->trans('in about 1 hour');
        }

        if ($distance_in_minutes <= 1440) {
            return $this->translator->trans('in about %hours hours', ['%hours' => round($distance_in_minutes/60)]);
        }

        if ($distance_in_minutes <= 2880) {
            return $this->translator->trans('in 1 day');
        }

        return $this->translator->trans('in %days days', ['%days' => round($distance_in_minutes/1440)]);
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
