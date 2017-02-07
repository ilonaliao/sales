<?php
class TwigArrayUnset extends Twig_Extension {
 
    public function getName() {
        return 'TwigArrayUnset';
    }
 
    public function getFunctions() {
        return array(
            'array_unset'  => new Twig_Function_Method($this, 'arrayUnset'),
        );
    }
     
    /**
     * Delete a key of an array
     *
     * @param array  $array Source array
     * @param string $key   The key to remove
     * @return array
     */
    public function arrayUnset($array, $key) {
        unset($array[$key]);
        return $array;
    }
}