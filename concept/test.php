<?php


	class client
	{
		public $platform;
		public $browser;
		public $version;
		public function __construct()
		{
			$result = parse_user_agent ();
			$this->platform = $result['platform'];
			$this->browser = $result['browser'];
			$this->version = $result['version'];
		}
	}

	$client = new client ();

	echo "alert('".$client->platform." (".$client->browser." ".$client_>version.")')";
?>