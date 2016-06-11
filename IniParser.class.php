<?php

/**
 * Name: IniParser 1.0
 * Author: Eduardo Arruda
 * Website: http://www.webgit.com.br/
 * Creation date: 11/06/2016
 *
 * IMPORTANT: 
 * - Remember to change the default dir on the constructor.
 */

class IniParser {

    private $dir;
    private $method;
    private $path;

    /**
     * <b>Class Constructor</b>
     *
     * @var BOOLEAN $return_object (default: false) - Return method (false = array, true = object)
     * @return n/a
     */
    
    public function __construct($return_object = false)
    {   
        $this->dir = "accounts\\";
        $this->method = $return_object;
    }

    /**
     * <b>File Finder</b>
     * 
     * Check if the file exists
     *
     * @var STRING $file_name - The name of the .ini file (ex. PlayerName)
     * @return boolean
     */

    public function file_find($file_name)
    {
        $this->path = "{$this->dir}{$file_name}.ini";
        if (file_exists($this->path) && !is_dir($this->path))
       	{
            return true;
        }
    }

    /**
     * <b>File Getter</b>
     * 
     * Return an array or object with all the data in the .ini file.
     *
     * @var STRING $file_name - The name of the .ini file (ex. PlayerName)
     * @return array or object
     */

    public function file_get($file_name)
    {
        $this->path = "{$this->dir}{$file_name}.ini";
        if ($this->file_find($file_name))
        {
        	$res = array();
            $parse_arr = parse_ini_file($this->path, false);
            foreach($parse_arr as $key => $val)
            {
            	$res[$key] = is_numeric($val) ? intval($val) : $val;
            }
            return ($this->method) ? (object) $res : $res;
        }
    }

    /**
     * <b>File Delete</b>
     * 
     * Delete the specified file.
     *
     * @var STRING $file_name - The name of the .ini file (ex. PlayerName)
     * @return boolean
     */

    public function file_delete($file_name)
    {
        $this->path = "{$this->dir}{$file_name}.ini";
        if ($this->file_find($file_name))
        {
            if (unlink($this->path))
            {
                return true;
            }
        }
    }

    /**
     * <b>File Update</b>
     * 
     * Update the specified .ini file with parameters in the array.
     *
     * @var ARRAY $array - Array with the data to update (ex. array('money' => 300))
     * @var STRING $file_name - The name of the .ini file (ex. PlayerName)
     * @return n/a
     */

    public function file_update($array, $file_name)
    {
    	$this_ac = $this->file_get($file_name, true);
    	if($this_ac)
    	{
    		$res = array();
	        $keys = array();

	        foreach($array as $key => $val)
	        {
				$res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
				$keys[] = $key;
	        }
	        $keys_string  = implode($keys, '/');
			foreach($this_ac as $a_key => $a_val)
	    	{
	    		if(strpos($keys_string, $a_key) === false)
	    		{
	    			$res[] = "$a_key = " . (is_numeric($a_val) ? $a_val : '"' . $a_val . '"');
	    		}
	    	}
	        $this->safefilerewrite($file_name, implode("\r\n", $res));
    	}
    }

    /**
     * <b>File Create</b>
     * 
     * Create an .ini file in the specified path with array data.
     *
     * @var ARRAY $array - Array with the data to insert (ex. array('money' => 300))
     * @var STRING $file_name - The name of the .ini file (ex. PlayerName)
     * @return n/a
     */

    public function file_create($array, $file_name)
    {
        $res = array();
        foreach($array as $key => $val)
        {
            $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
        }
        $this->safefilerewrite($file_name, implode("\r\n", $res));
    }

    /**
     * <b>File Saver</b>
     * 
     * Private function who save changes in the .ini file.
     *
     * @var STRING $file_name - The name of the .ini file (ex. PlayerName)
     * @var STRING $dataToSave - String of the implode of the arrayData.
     * @return n/a
     */

    private function safefilerewrite($file_name, $dataToSave)
    {
        $this->path = "{$this->dir}{$file_name}.ini";
        if ($fp = fopen($this->path, 'w'))
        {
            $startTime = microtime(TRUE);
            do
            {
                $canWrite = flock($fp, LOCK_EX);
                if (!$canWrite) usleep(round(rand(0, 100) * 1000));
            }
            while ((!$canWrite) and ((microtime(TRUE) - $startTime) < 5));
            if ($canWrite)
            {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }
    }
}