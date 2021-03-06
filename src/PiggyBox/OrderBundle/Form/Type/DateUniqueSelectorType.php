<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use PiggyBox\OrderBundle\Form\DataTransformer\DateTimeToArrayTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class DateUniqueSelectorType extends AbstractType
{
    const DEFAULT_FORMAT = \IntlDateFormatter::FULL;

    const HTML5_FORMAT = 'yyyy-MM-dd';

    private static $acceptedFormats = array(
        \IntlDateFormatter::FULL,
        \IntlDateFormatter::LONG,
        \IntlDateFormatter::MEDIUM,
        \IntlDateFormatter::SHORT,
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateFormat = is_int($options['format']) ? $options['format'] : self::DEFAULT_FORMAT;
        $timeFormat = \IntlDateFormatter::NONE;
        $calendar = \IntlDateFormatter::GREGORIAN;
        $pattern = is_string($options['format']) ? $options['format'] : null;

        if (!in_array($dateFormat, self::$acceptedFormats, true)) {
            throw new InvalidOptionsException('The "format" option must be one of the IntlDateFormatter constants (FULL, LONG, MEDIUM, SHORT) or a string representing a custom format.');
        }

        if (null !== $pattern && (false === strpos($pattern, 'y') || false === strpos($pattern, 'M') || false === strpos($pattern, 'd'))) {
            throw new InvalidOptionsException(sprintf('The "format" option should contain the letters "y", "M" and "d". Its current value is "%s".', $pattern));
        }

        if ('single_text' === $options['widget']) {
            $builder->addViewTransformer(new DateTimeToLocalizedStringTransformer(
                $options['model_timezone'],
                $options['view_timezone'],
                $dateFormat,
                $timeFormat,
                $calendar,
                $pattern
            ));
        } else {
            $dateOption = $yearOptions = $monthOptions = $dayOptions = array(
                'error_bubbling' => true,
            );

            $formatter = new \IntlDateFormatter(
                \Locale::getDefault(),
                $dateFormat,
                $timeFormat,
                'UTC',
                $calendar,
                $pattern
            );
            $formatter->setLenient(false);

            if ('choice' === $options['widget']) {
                // Only pass a subset of the options to children
                $dateOption['choices'] = $this->listUniqueDate($options['number_of_days'], $options['closed_days'], $options['start_today']);
                //$dayOptions['choices'] = $this->formatTimestamps($formatter, '/d+/', $this->listDays($options['days']));
                //$dayOptions['empty_value'] = $options['empty_value']['day'];
            }

            // Append generic carry-along options
            foreach (array('required', 'translation_domain') as $passOpt) {
                $yearOptions[$passOpt] = $monthOptions[$passOpt] = $dayOptions[$passOpt] = $options[$passOpt];
            }

            $builder
                ->add('date', $options['widget'], $dateOption)
                ->addViewTransformer(new DateTimeToArrayTransformer(
                $options['model_timezone'], $options['view_timezone'], array('date')
                ))
                ->setAttribute('formatter', $formatter)
            ;
        }

        if ('string' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToStringTransformer($options['model_timezone'], $options['model_timezone'], 'Y-m-d')
            ));
        } elseif ('timestamp' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToTimestampTransformer($options['model_timezone'], $options['model_timezone'])
            ));
        } elseif ('array' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToArrayTransformer($options['model_timezone'], $options['model_timezone'], array('year', 'month', 'day'))
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['widget'] = $options['widget'];

        // Change the input to a HTML5 date input if
        //  * the widget is set to "single_text"
        //  * the format matches the one expected by HTML5
        if ('single_text' === $options['widget'] && self::HTML5_FORMAT === $options['format']) {
            $view->vars['type'] = 'date';
        }

        if ($form->getConfig()->hasAttribute('formatter')) {
            $pattern = $form->getConfig()->getAttribute('formatter')->getPattern();

            // set right order with respect to locale (e.g.: de_DE=dd.MM.yy; en_US=M/d/yy)
            // lookup various formats at http://userguide.icu-project.org/formatparse/datetime
            if (preg_match('/^([yMd]+).+([yMd]+).+([yMd]+)$/', $pattern)) {
                $pattern = preg_replace(array('/y+/', '/M+/', '/d+/'), array('{{ year }}', '{{ month }}', '{{ day }}'), $pattern);
            } else {
                // default fallback
                $pattern = '{{ year }}-{{ month }}-{{ day }}';
            }

            $view->vars['date_pattern'] = $pattern;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $compound = function (Options $options) {
            return $options['widget'] !== 'single_text';
        };

        $emptyValue = $emptyValueDefault = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $emptyValueNormalizer = function (Options $options, $emptyValue) use ($emptyValueDefault) {
            if (is_array($emptyValue)) {
                $default = $emptyValueDefault($options);

                return array_merge(
                    array('year' => $default, 'month' => $default, 'day' => $default),
                    $emptyValue
                );
            }

            return array(
                'year' => $emptyValue,
                'month' => $emptyValue,
                'day' => $emptyValue
            );
        };

        // BC until Symfony 2.3
        $modelTimezone = function (Options $options) {
            return $options['data_timezone'];
        };

        // BC until Symfony 2.3
        $viewTimezone = function (Options $options) {
            return $options['user_timezone'];
        };

        $resolver->setDefaults(array(
            'years'          => range(date('Y') - 5, date('Y') + 5),
            'months'         => range(1, 12),
            'days'           => range(1, 31),
            'widget'         => 'choice',
            'input'          => 'datetime',
            'format'         => self::HTML5_FORMAT,
            'model_timezone' => $modelTimezone,
            'view_timezone'  => $viewTimezone,
            // Deprecated timezone options
            'data_timezone'  => null,
            'user_timezone'  => null,
            'empty_value'    => $emptyValue,
            // Don't modify \DateTime classes by reference, we treat
            // them like immutable value objects
            'by_reference'   => false,
            'error_bubbling' => false,
            // If initialized with a \DateTime object, FormType initializes
            // this option to "\DateTime". Since the internal, normalized
            // representation is not \DateTime, but an array, we need to unset
            // this option.
            'data_class'     => null,
            'compound'       => $compound,
            'number_of_days' => 8,
            'closed_days'       => array(),
            'start_today'       => true,
        ));

        $resolver->setNormalizers(array(
            'empty_value' => $emptyValueNormalizer,
        ));

        $resolver->setAllowedValues(array(
            'input'     => array(
                'datetime',
                'string',
                'timestamp',
                'array',
            ),
            'widget'    => array(
                'single_text',
                'text',
                'choice',
            ),
        ));

        $resolver->setAllowedTypes(array(
            'format' => array('int', 'string'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date_unique_selector';
    }

    private function formatTimestamps(\IntlDateFormatter $formatter, $regex, array $timestamps)
    {
        $pattern = $formatter->getPattern();
        $timezone = $formatter->getTimezoneId();

        $formatter->setTimezoneId(\DateTimeZone::UTC);

        if (preg_match($regex, $pattern, $matches)) {
            $formatter->setPattern($matches[0]);

            foreach ($timestamps as $key => $timestamp) {
                $timestamps[$key] = $formatter->format($timestamp);
            }

            // I'd like to clone the formatter above, but then we get a
            // segmentation fault, so let's restore the old state instead
            $formatter->setPattern($pattern);
        }

        $formatter->setTimezoneId($timezone);

        return $timestamps;
    }

    private function listUniqueDate($number_of_days, $closed_days, $start_today)
    {
        $result = array();
        $weekBuffer = array();

        $today = new \DateTime('now');
        if ($start_today == false) {
            $today->modify('+1 day');
        }

        for ($i=0; $i < $number_of_days; $i++) {
            if (!in_array($today->format('N'), $closed_days)) {
                $weekBuffer[$this->twig_localized_date_filter($today,'full','none','en_EN')] = $this->twig_localized_date_filter($today,'full','none','fr_FR');
            }
            if ($today->format('N') == 7) {
                (empty($result)) ? ($result['Cette semaine'] = $weekBuffer): ($result['Semaine Num. '.$today->format('W')] = $weekBuffer);
                $weekBuffer = array();
            }
            $today->modify('+1 day');
        }

        return $result;
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
