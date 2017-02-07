<?php
class TwigCombineQueryarray extends Twig_Extension {
 
    public function getName() {
        return 'TwigCombineQueryarray';
    }
 
    public function getFunctions() {
        return array(
            'combine_queryarray'  => new Twig_Function_Method($this, 'combine'),
        );
    }
     
    public function combine($array) {
        $string = "";
        if(is_array($array)){
            foreach ($array as $key => $value) {
                if($string=="")
                    $string = $key."=".$value;
                else
                     $string = $string . "&" . $key . "=" . $value;
            }
        }
        return $string;
    }
        
    
}