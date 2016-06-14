<?php
	
	error_reporting (E_ALL);

	//include '../lib/debug/debug.php';

	const X = 0;
    
    const BLACK = "\33[0;30m";
    const GRAY = "\33[1;30m";
    const MAROON = "\33[0;31m";
    const RED = "\33[1;31m";
    const GREEN = "\33[0;32m";
    const LIME = "\33[1;32m";
    const YELLOW = "\33[1;33m";
    const BROWN = "\33[0;33m";
    const NAVY = "\33[0;34m";
    const BLUE = "\33[1;34m";
    const PURPLE = "\33[0;35m";
    const PINK = "\33[1;35m";
    const CYAN = "\33[0;36m";
    const AQUA = "\33[1;36m";
    const SILVER = "\33[0;37m";
    const WHITE = "\33[1;37m";

    function color ($text, $color)
    {
    	return $color.$text."\033[0;39m";
    }

    echo color ('BLACK', BLACK);
    echo color ('GRAY', GRAY);
    echo color ('MAROON', MAROON);
    echo color ('RED', RED);
    echo color ('GREEN', GREEN);
    echo color ('LIME', LIME);
    echo color ('YELLOW', YELLOW);
    echo color ('BROWN', BROWN);    
    echo color ('NAVY', NAVY);
    echo color ('BLUE', BLUE);
    echo color ('PURPLE', PURPLE);
    echo color ('PINK', PINK);
    echo color ('CYAN', CYAN);
    echo color ('AQUA', AQUA);
    echo color ('SILVER', SILVER);
    echo color ('WHITE', WHITE);
    echo "\n";

	class date
	{
		public $time;
		public $year;
		public $month;
		public $day;
		public $week;
		public function __construct ($date=null)
		{
			if ($date!==null)
			{
				$this->time = strtotime ($date);
			}
			else
			{
				$this->time = time();
			}
			$this->year = intval(date("Y",$this->time));
			$this->month = intval(date("n",$this->time));
			$this->day = intval(date("j",$this->time));
			$this->week = intval(date("N",$this->time));
		}
		public function name ()
		{
			return date("D", $this->time);
		}
		public static function now ()
		{
			return date ("Y-m-d");
		}
		public function set ($day)
		{
			$this->time = strtotime ($this->year.'-'.$this->month.'-'.$day);
			$this->year = intval(date("Y",$this->time));
			$this->month = intval(date("n",$this->time));
			$this->day = intval(date("j",$this->time));
			$this->week = intval(date("N",$this->time));
		}
	}

	class amount
	{
		public $currency;
		public $value;
		public function __construct ($currency, $value)
		{
			$this->currency = $currency;
			$this->value = $value;
		}
		public function string ()
		{
			return $this->currency." ".$this->value;
		}
		public function convert ($currency)
		{
			return $this;
		}
	}

	class expense
	{
		public $amount;
		public $title;
		public $year;
		public $month;
		public $day;
		public $week;
		public function __construct ($currency, $value, $title, $week=[], $day=X, $month=X, $year=X)
		{

			$this->amount = new amount ($currency, $value);
			$this->title = $title;
			$this->year = $year;
			$this->month = $month;
			$this->day = $day;
			$this->week = $week;
		}
		public function active ($date=null)
		{
			if (!is_object($date) || $date===null)
			{
				$date = new date ($date);
			}
			if ($this->year!==X && $date->year!=$this->year)
			{
				return false;
			}
			if ($this->month!==X && $date->month!=$this->month)
			{
				return false;
			}
			if ($this->day!==X && $date->day!=$this->day)
			{
				return false;
			}
			if ($this->week!==X && is_array($this->week) && count($this->week) && !in_array($date->week,$this->week))
			{
				return false;
			}			
			return true;
		}
		public function get ($date=null)
		{	
			if ($this->active($date))
			{
				return new amount ($this->currency, $this->value);
			}
			return null;
		} 
	}

	function add ($currency, $value, $title, $week=[], $day=X, $month=X, $year=X)
	{
		global $items;
		$items[] = new expense ($currency, $value, $title, $week, $day, $month, $year);
	}

	function salary ($currency, $value)
	{
		global $salary;
		$salary = new amount ($currency, $value);
	}

	function balance ($currency, $value)
	{
		global $balance;
		$balance = new amount ($currency, $value);
	}

	$items = array ();
	$salary = null;
	$balance = null;

	echo "\n";

	if (file_exists(__DIR__.'/test.php'))
	{
		include __DIR__.'/test.php';	
	}
	
	if (file_exists(__DIR__.'/user.php'))
	{
		include __DIR__.'/user.php';	
	}

	if (!is_object($balance))
	{
		echo "balance not defined\n";
		exit;
	}
	
	if (!is_array($items) || !$items)
	{
		echo "expenses not defined\n";
		exit;
	}

	//init calendar control
	$calendar = [1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''];
	$date = new date ();
	$date->set (1);
	if ($date->week>1)
	{
		for ($day=1; $day<$date->week; $day++)
		{
			$calendar[$day] = "            \t";
		}
	}

	//calculate month average
	$date = new date ();
	$average = 0;
	for ($day=1; $day<=intval(date("t",time())); $day++)
	{
		$sum = 0;
		$date->set ($day);
		foreach ($items as $item)
		{
			if ($item->active($date))
			{
				$sum += $item->amount->convert($balance->currency)->value;
			}
		}
		$average = ($average+$sum)/2;
	}
	$average = round ($average,2);

	//draw calendar
	$now = new date ();
	$date = new date ();
	$total = 0;
	$need = 0;
	for ($day=1; $day<=intval(date("t",time())); $day++)
	{
		$sum = 0;
		$date->set ($day);
		foreach ($items as $item)
		{
			if ($item->active($date))
			{
				$sum += $item->amount->convert($balance->currency)->value;
			}
		}
		$total += $sum;
		if ($date->day>$now->day)
		{
			$need += $sum;
		}
		$calendar[$date->week] .= color(str_pad($day,2," ",STR_PAD_LEFT),MAROON)
							   ." ".color(strtoupper($date->name()),GREEN)
							   ." ".color(str_pad($sum,5," ",STR_PAD_LEFT)."\t",$sum>$average?RED:($sum>($average/2)?SILVER:GRAY));
	}

	foreach ($calendar as $item)
	{
		echo $item."\n";
	}

	echo "\n";
	$sum = 0;
	echo "Today\n";
	echo "-------------\n";
	foreach ($items as $item)
	{
		if ($item->active())
		{
			$sum += $item->amount->convert($balance->currency)->value;
			echo $item->title." ".$item->amount->currency." ".$item->amount->value."\n";
		}
	}
	echo "-------------\n";
	echo $balance->currency." ".$sum;
	echo "\n";
	echo "\n";

	echo "Average: ".$balance->currency." ".$average."\n";
	echo "Total: ".$balance->currency." ".$total."\n";
	echo "Current: ".$balance->currency." ".$balance->value."\n";
	echo "Need: ".$balance->currency." ".$need."\n";
	echo "Profit: ".$balance->currency." ".($balance->value-$need)."\n";


?>