<?PHP




namespace Dino\General
{
    use Dino\Errors\ArgTypeError;
    use Dino\Errors\EmptyArgError;
    use Dino\Errors\KeyNotFoundError;
    
    
    class Config
    {
        private
        static
        $_data
        = array();
        
        
        public
        static
        function
        set(
            $path,
            $value   = null,
            &$source = false)
        {
            if (is_array($path)
             && is_null($value)
             && empty(self::$_data)) {
                
                $path
                = array_change_key_case(
                    $path,
                    CASE_LOWER);
                
                self::$_data
                = $path;
                
                return;
            }
            
            if (!is_string($path)) {
                throw
                new ArgTypeError(
                        $path,
                        'path:string');
            }
            
            if (empty($path)) {
                throw
                new emptyArgError('path');
            }
            
            $path
            = strtolower($path);
            
            $path
            = explode('.', $path);
            
            $name
            = array_shift($path);
            
            if (is_array($value)) {
                $value
                = array_change_key_case(
                    $value,
                    CASE_LOWER);
            }
            
            if (!empty($path)) {
                $path
                = array_reverse($path);
                
                foreach ($path as $key) {
                    $value
                    = array($key => $value);
                }
            }
            
            if (!is_array($source)) {
                $source
                =& self::$_data;
            }
            
            if (!isset($source[$name])
             || !is_array($source[$name])
             || !is_array($value)) {
                
                $source[$name]
                = $value;
            }
            else {
                foreach ($value as $k => $v) {
                    self::set($k, $v, $source[$name]);
                }
            }
        }
        
        
        
        public
        static
        function
        check(
            $path,
            &$value  = false)
        {
            if (!is_string($path)) {
                throw
                new ArgTypeError(
                        $path,
                        'path:string');
            }
            
            if (empty($path)) {
                throw
                new EmptyArgError(
                        'path');
            }
            
            $path
            = strtolower($path);
            
            $path
            = explode('.', $path);
            
            $name
            = array_shift($path);
            
            if (!isset(self::$_data[$name])) {
                return false;
            }
            
            $value
            = self::$_data[$name];
            
            if (!empty($path)) {
                foreach ($path as $name) {
                    if (!is_array($value)) {
                        $value = false;
                        return false;
                    }
                    
                    $value
                    = array_change_key_case(
                        $value,
                        CASE_LOWER);
                    
                    if (!isset($value[$name])) {
                        $value = false;
                        return false;
                    }
                    
                    $value
                    = $value[$name];
                }
            }
            
            return true;
        }
        
        
        
        public
        static
        function
        get($path)
        {
            if(self::check($path, $value)) {
                return $value;
            }
            
            throw
            new KeyNotFoundError(
                    'config',
                    $path);
        }
    }
}
