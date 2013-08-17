<?php

class Reportdata extends Model {
    
    function __construct($serial='')
    {
        parent::__construct('id','reportdata'); //primary key, tablename
		$this->rs['id'] = '';
		$this->rs['serial_number'] = ''; $this->rt['serial_number'] = 'VARCHAR(255) UNIQUE';
		$this->rs['console_user'] = '';
		$this->rs['long_username'] = '';
		$this->rs['remote_ip'] = '';
		$this->rs['timestamp'] = time();
				
		// Create table if it does not exist
        $this->create_table();
        
        if($serial)
        {
            $this->retrieve_one('serial_number=?', $serial);
            $this->serial_number = $serial;
        }
        
        return $this;
    }

	function process($plist)
	{
		require_once(APP_PATH . 'lib/CFPropertyList/CFPropertyList.php');
		$parser = new CFPropertyList();
		$parser->parse($plist, CFPropertyList::FORMAT_XML);
		$mylist = $parser->toArray();
		
		$this->merge($mylist)->register()->save();
	}

	/**
	 * Register IP and time
	 *
	 * @return object this
	 * @author AvB
	 **/
	function register()
	{
		// Test for proxy
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$this->remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else
		{
			$this->remote_ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$this->timestamp = time();

		return $this;
	}
}
