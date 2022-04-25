<?php

namespace Buglerv\LaravelHelpers\Traits;

use RuntimeException;
use Illuminate\Support\Str;

trait ChainableMethods
{
    public function __call($method,$arguments)
    {
        $methods = explode('Or',$method);
        
        foreach($methods as $oneMethod)
        {
            if($result = $this->orMethod($oneMethod,$arguments))
            {
                return $result;
            }
        }
        
        return false;
    }
    
    protected function orMethod($method,$arguments)
    {
        $methods = explode('And',$method);
        
        foreach($methods as $oneMethod)
        {
            if(!$result = $this->andMethod($oneMethod,$arguments))
            {
                return false;
            }
        }
        
        return $result;
    }
    
    protected function andMethod($method,$arguments)
    {
        $method = Str::camel($method);

        if($method == 'false') return false;
        if($method == 'true') return true;
        
        if(method_exists($this,$method))
        {
            return $this->{$method}(...$arguments);
        }
        
        $class = get_class($this);
        
        throw(new RuntimeException("Method [{$class}:{$method}] does't exist."));
    }
}