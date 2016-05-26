<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('angularjs_class_array', array($this, 'filter')),
        );
    }

    public function filter($cls)
    {
        $cls = explode(" ", $cls);
        $cls = array_map(function($c){
            return "'$c'";
        }, $cls);
        return implode(", ", $cls);
    }

    public function getName()
    {
        return 'app_extension';
    }
}