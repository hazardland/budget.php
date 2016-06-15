<?php

	namespace expense;

	const X = 0;

	class user
	{
		public $currency;
		public $salary;
		public $balance;
		public $expenses = array ();
		public function __construct ($currency=null)
		{
			$this->currency = $currency;
			$this->salary = new amount ($currency);
			$this->balance = new amount ($currency);
		}
		public function create ()
		{
			$this->salary->currency = $this->currency;
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
		public function salary ($value)
		{
			$this->salary->value = $value;
		}
	}

	class date
	{
		public $time;
		public $year;
		public $month;
		public $day;
		public $week;
		public function __construct ($date)
		{
			$this->time = strtotime ($date);
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
		public function __construct ($currency, $value, $category, $title=null, $week=[], $day=X, $month=X, $year=X)
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


?>