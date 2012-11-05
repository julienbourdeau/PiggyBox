<?php

namespace PiggyBox\OrderBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\BaseDateTimeTransformer;

/**
 * Transforms between a normalized time and a localized time string/array.
 */
class DateTimeToArrayTransformer extends BaseDateTimeTransformer
{
    private $pad;

    private $fields;

    /**
     * Constructor.
     *
     * @param string  $inputTimezone  The input timezone
     * @param string  $outputTimezone The output timezone
     * @param array   $fields         The date fields
     * @param Boolean $pad            Whether to use padding
     *
     * @throws UnexpectedTypeException if a timezone is not a string
     */
    public function __construct($inputTimezone = null, $outputTimezone = null, array $fields = null, $pad = false)
    {
        parent::__construct($inputTimezone, $outputTimezone);

        if (null === $fields) {
            $fields = array('year', 'month', 'day', 'hour', 'minute', 'second');
        }

        $this->fields = $fields;
        $this->pad = (Boolean) $pad;
    }

    /**
     * Transforms a normalized date into a localized date.
     *
     * @param DateTime $dateTime Normalized date.
     *
     * @return array Localized date.
     *
     * @throws UnexpectedTypeException       if the given value is not an instance of \DateTime
     * @throws TransformationFailedException if the output timezone is not supported
     */
    public function transform($dateTime)
    {
        if (null === $dateTime) {
            return array_intersect_key(array(
                'date'    => '',
                'time'    => '',
                'year'    => '',
                'month'   => '',
                'day'     => '',
                'hour'    => '',
                'minute'  => '',
                'second'  => '',
            ), array_flip($this->fields));
        }

        if (!$dateTime instanceof \DateTime) {
            throw new UnexpectedTypeException($dateTime, '\DateTime');
        }

        $dateTime = clone $dateTime;
        if ($this->inputTimezone !== $this->outputTimezone) {
            try {
                $dateTime->setTimezone(new \DateTimeZone($this->outputTimezone));
            } catch (\Exception $e) {
                throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $result = array_intersect_key(array(
            'date'	  => $this->twig_localized_date_filter($dateTime,'full','none','en_EN'),
            'time'	  => $dateTime->format('H:i'),
            'year'    => $dateTime->format('Y'),
            'month'   => $dateTime->format('m'),
            'day'     => $dateTime->format('d'),
            'hour'    => $dateTime->format('H'),
            'minute'  => $dateTime->format('i'),
            'second'  => $dateTime->format('s'),
        ), array_flip($this->fields));

        if (!$this->pad) {
            foreach ($result as &$entry) {
                // remove leading zeros
                $entry = (string) (int) $entry;
            }
        }

        return $result;
    }

    /**
     * Transforms a localized date into a normalized date.
     *
     * @param array $value Localized date
     *
     * @return DateTime Normalized date
     *
     * @throws UnexpectedTypeException       if the given value is not an array
     * @throws TransformationFailedException if the value could not be transformed
     * @throws TransformationFailedException if the input timezone is not supported
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if ('' === implode('', $value)) {
            return null;
        }

        $emptyFields = array();

        foreach ($this->fields as $field) {
            if (!isset($value[$field])) {
                $emptyFields[] = $field;
            }
        }

        if (count($emptyFields) > 0) {
            throw new TransformationFailedException(
                sprintf('The fields "%s" should not be empty', implode('", "', $emptyFields)
            ));
        }

        if (isset($value['month']) && !ctype_digit($value['month']) && !is_int($value['month'])) {
            throw new TransformationFailedException('This month is invalid');
        }

        if (isset($value['day']) && !ctype_digit($value['day']) && !is_int($value['day'])) {
            throw new TransformationFailedException('This day is invalid');
        }

        if (isset($value['year']) && !ctype_digit($value['year']) && !is_int($value['year'])) {
            throw new TransformationFailedException('This year is invalid');
        }

        if (!empty($value['month']) && !empty($value['day']) && !empty($value['year']) && !empty($value['time']) && !empty($value['date']) && false === checkdate($value['month'], $value['day'], $value['year'])) {
            throw new TransformationFailedException('This is an invalid date');
        }

        try {

            if (empty($value['date'])) {
                $dateTime = new \DateTime($value['time']);
            }
            if (empty($value['time'])) {
                $dateTime = new \DateTime($value['date']);
            }

            if ($this->inputTimezone !== $this->outputTimezone) {
                $dateTime->setTimezone(new \DateTimeZone($this->inputTimezone));
            }
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $dateTime;
    }

    private function twig_localized_date_filter($date, $dateFormat = 'medium', $timeFormat = 'medium', $locale = null)
    {
        $formatValues = array(
            'none'   => \IntlDateFormatter::NONE,
            'short'  => \IntlDateFormatter::SHORT,
            'medium' => \IntlDateFormatter::MEDIUM,
            'long'   => \IntlDateFormatter::LONG,
            'full'   => \IntlDateFormatter::FULL,
        );

        $formatter = \IntlDateFormatter::create(
            $locale !== null ? $locale : \Locale::getDefault(),
            $formatValues[$dateFormat],
            $formatValues[$timeFormat],
            date_default_timezone_get()
        );

        if (!$date instanceof \DateTime) {
            if (ctype_digit((string) $date)) {
                $date = new \DateTime('@'.$date);
                $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
            } else {
                $date = new \DateTime($date);
            }
        }

        return $formatter->format($date->getTimestamp());
    }

}
