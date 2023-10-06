<?php

namespace Tamedevelopers\File\Traits;


trait CommonTrait{
    
    /**
     * Only ignore if validate metho, has been manually called
     *
     * @return mixed
     */
    public function ignoreIfValidatorHasBeenCalled()
    {
        if(!$this->isValidatedCalled){
            $this->validate();
        }
    }
    
    /**
     * Check if is local driver
     *
     * @return bool
     */
    public function isLocalDriver()
    {
        if(isset($this->config['driver']) && $this->config['driver'] === 'local'){
            return true;
        }

        return false;
    }

}