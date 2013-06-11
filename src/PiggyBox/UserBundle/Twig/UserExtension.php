<?php

namespace PiggyBox\UserBundle\Twig;

class UserExtension extends \Twig_Extension
{

    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'file_exists' => new \Twig_Function_Function('file_exists'),
        );
    }
    /*
    public function fileExistsFunction($filename)
    {
        return file_exists($filename);
    }
*/
    public function getName()
    {
        return 'twig_extension';
    }
}
