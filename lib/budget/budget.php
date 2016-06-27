<?php

	namespace budget;

	const X = null;

	class user
	{
		public $currency;
		public $budget;
		public $balance;
		public $expenses = array ();
		public function __construct ($currency=null)
		{
			$this->currency = $currency;
			$this->budget = new amount ($currency);
			$this->balance = new amount ($currency);
		}
		public function create ()
		{
			$this->budget->currency = $this->currency;
			$this->balance->currency = $this->currency;
		}
		public function expense (expense $expense)
		{
			$this->expenses[] = $expense;
		}
		public function balance ($value)
		{
			$this->balance->value = $value;
		}
		public function budget ($value)
		{
			$this->budget->value = $value;
		}
	}

	class date
	{
		public $time;
		public $year;
		public $month;
		public $day;
		public $week;
		public function __construct ($year, $month, $day)
		{
			$this->time = strtotime ($year.'-'.$month.'-'.$day);
			$this->year = intval(date("Y",$this->time));
			$this->month = intval(date("n",$this->time));
			$this->day = intval(date("j",$this->time));
			$this->week = intval(date("N",$this->time));
		}
		public function name ($full=false)
		{
			return date($full?"l":"D", $this->time);
		}
		public static function day ()
		{
			return intval(date("d"));
		}
		public static function now ($day=null)
		{
			return new self (intval(date("Y")), intval(date("m")), $day===null?intval(date("d")):$day);
		}
		public function set ($day)
		{
			$this->time = strtotime ($this->year.'-'.$this->month.'-'.$day);
			$this->year = intval(date("Y",$this->time));
			$this->month = intval(date("n",$this->time));
			$this->day = intval(date("j",$this->time));
			$this->week = intval(date("N",$this->time));
		}
		public static function range ($from, $to)
		{
			$result = array ();
			$from = strtotime ($from);
			$to = strtotime ($to);
			for ($time=$from;$time<=$to;$time=$time+24*60*60)
			{
				$date = new self (intval(date("Y",$time)),intval(date("n",$time)),intval(date("j",$time)));
				$result[$date->year.'-'.$date->month.'-'.$date->day] = $date;
			}
			return $result;
		}
	}

	class amount
	{
		public $currency;
		public $value;
		public function __construct ($currency, $value=0)
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
		public function __construct ($currency, $value, $category, $title=null, $week=[], $day=[], $month=[], $year=[])
		{

			$this->amount = new amount ($currency, $value);
			$this->category = $category;
			if ($title===null)
			{
				$this->title = $category;
			}
			else
			{
				$this->title = $title;
			}
			$this->year = $year;
			$this->month = $month;
			$this->day = $day;
			$this->week = $week;
		}
		public function active ($date)
		{
			if ($date===null || !is_object($date))
			{
				return false;
			}
			if (!is_object($date))
			{
				$date = new date ($date);
			}
			//if ($this->year!==X && $date->year!=$this->year)
			if ($this->year!==null && is_array($this->year) && count($this->year) && !in_array($date->year,$this->year))
			{
				return false;
			}
			//if ($this->month!==X && $date->month!=$this->month)
			if ($this->month!==null && is_array($this->month) && count($this->month) && !in_array($date->month,$this->month))
			{
				return false;
			}
			//if ($this->day!==X && $date->day!=$this->day)
			if ($this->day!==null && is_array($this->day) && count($this->day) && !in_array($date->day,$this->day))
			{
				return false;
			}
			if ($this->week!==null && is_array($this->week) && count($this->week) && !in_array($date->week,$this->week))
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

	class entry
	{
		public $value;
		public $count;
		public function __construct ()
		{
			$this->value = 0;
			$this->count = 0;
		}
		public function add ($value)
		{
			$this->value += $value;
			$this->count ++;
		}
	}

	class sum
	{
		public $name;
		public $total;
		public $used;
		public $need;
		public function __construct ($name=null)
		{
			$this->name = $name;
			$this->total = new entry();
			$this->used = new entry();
			$this->need = new entry();
		}
	}


?>