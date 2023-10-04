<?php

namespace Tamedevelopers\File\Traits;

/**
 * @property mixed $config
 */
trait CommonTrait{
    
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