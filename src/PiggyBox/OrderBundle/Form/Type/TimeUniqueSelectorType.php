<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Form\DataTransformer\DateTimeToArrayTransformer;

class TimeUniqueSelectorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $parts  = array('hour', 'minute');
        $format = 'H:i';
        if ($options['with_seconds']) {
            $format  = 'H:i:s';
            $parts[] = 'second';
        }

        if ('single_text' === $options['widget']) {
            $builder->addViewTransformer(new DateTimeToStringTransformer($options['model_timezone'], $options['view_timezone'], $format));
        } else {
            $timeOptions = $hourOptions = $minuteOptions = $secondOptions = array(
                'error_bubbling' => true,
            );

            if ('choice' === $options['widget']) {
                $times = $hours = $minutes = array();

                foreach ($options['hours'] as $hour) {
                    $hours[$hour] = str_pad($hour, 2, '0', STR_PAD_LEFT);
                }

                foreach ($options['minutes'] as $minute) {
                    $minutes[$minute] = str_pad($minute, 2, '0', STR_PAD_LEFT);
                }

                // Only pass a subset of the options to children
				$timeOptions['choices'] = $this->listUniqueHours();		
                $hourOptions['choices'] = $hours;
                $hourOptions['empty_value'] = $options['empty_value']['hour'];
                $minuteOptions['choices'] = $minutes;
                $minuteOptions['empty_value'] = $options['empty_value']['minute'];

                if ($options['with_seconds']) {
                    $seconds = array();

                    foreach ($options['seconds'] as $second) {
                        $seconds[$second] = str_pad($second, 2, '0', STR_PAD_LEFT);
                    }

                    $secondOptions['choices'] = $seconds;
                    $secondOptions['empty_value'] = $options['empty_value']['second'];
                }

                // Append generic carry-along options
                foreach (array('required', 'translation_domain') as $passOpt) {
                    $hourOptions[$passOpt] = $minuteOptions[$passOpt] = $options[$passOpt];
                    if ($options['with_seconds']) {
                        $secondOptions[$passOpt] = $options[$passOpt];
                    }
                }
            }

            $builder
                ->add('time', $options['widget'], $timeOptions)
            ;

            if ($options['with_seconds']) {
                $builder->add('second', $options['widget'], $secondOptions);
            }

             $builder->addViewTransformer(new DateTimeToArrayTransformer(
				$options['model_timezone'], $options['view_timezone'], array('time')
                ));
			
        }

        if ('string' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToStringTransformer($options['model_timezone'], $options['model_timezone'], 'H:i:s')
            ));
        } elseif ('timestamp' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToTimestampTransformer($options['model_timezone'], $options['model_timezone'])
            ));
        } elseif ('array' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToArrayTransformer($options['model_timezone'], $options['model_timezone'], $parts)
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'widget'       => $options['widget'],
            'with_seconds' => $options['with_seconds'],
        ));

        if ('single_text' === $options['widget']) {
            $view->vars['type'] = 'time';
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
                    array('hour' => $default, 'minute' => $default, 'second' => $default),
                    $emptyValue
                );
            }

            return array(
                'hour' => $emptyValue,
                'minute' => $emptyValue,
                'second' => $emptyValue
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
            'hours'          => range(0, 23),
            'minutes'        => range(0, 59),
            'seconds'        => range(0, 59),
            'widget'         => 'choice',
            'input'          => 'datetime',
            'with_seconds'   => false,
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
        ));

        $resolver->setNormalizers(array(
            'empty_value' => $emptyValueNormalizer,
        ));

        $resolver->setAllowedValues(array(
            'input' => array(
                'datetime',
                'string',
                'timestamp',
                'array',
            ),
            'widget' => array(
                'single_text',
                'text',
                'choice',
            ),
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
        return 'time_unique_selector';
    }

	private function listUniqueHours(){
        $result = array();
		
		$midnight = new \DateTime('00:00');
		$result[$midnight->format('H:i')] = $midnight->format('H:i'); 
		$midnight->modify('+30 minutes');

		while($midnight->format('H:i') !== '00:00') {
			$result[$midnight->format('H:i')] = $midnight->format('H:i'); 
			$midnight->modify('+30 minutes');
		}

		return $result;
	}
	
}
