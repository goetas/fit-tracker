<?php

namespace AppBundle\Controller;

trait NamedFormTrait
{
    /**
     * @param string $name
     * @param mixed $type
     * @param mixed $data
     * @param array $options
     * @return \Symfony\Component\Form\Form
     */
    protected function createNamedForm($name, $type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')
            ->createNamedBuilder($name, $type, $data, $options)
            ->getForm();
    }

}
