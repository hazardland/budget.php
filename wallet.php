<?php

	error_reporting (E_ALL);

	include './lib/debug/debug.php';
	include './lib/budget.php';
	include './lib/color.php';

	const X = null;

	//define config functions
	function add ($currency, $value, $category, $title=null, $week=[], $day=[], $month=[], $year=[])
	{
		global $user;
		$user->expense (new \budget\expense ($currency, $value, $category, $title, $week, $day, $month, $year));
	}
	function currency ($currency)
	{
		global $user;
		$user->currency = $currency;
		$user->create ();
	}
	function budget ($value)
	{
		global $user;
		$user->budget ($value);
	}
	function balance ($value)
	{
		global $user;
		$user->balance ($value);
	}

	//create user
	$user = new \budget\user ();
	//include config
	if (file_exists(__DIR__.'/user.php'))
	{
		include __DIR__.'/user.php';
	}

	//check if user was unset in config
	if (!is_object($user))
	{
		echo color("user not defined\n", RED);
		exit;
	}

	//check if user defined currency
	if (!$user->currency)
	{
		echo color("currency not defined\n", RED);
		exit;
	}

	//check if user defined expences
	if (!is_array($user->expenses) || !count($user->expenses))
	{
		echo color("expenses not defined\n", RED);
		exit;
	}

	//parse date variable input
	$input = getopt ('d:', ['day:']);
	//debug ($input,'input');
	$day = null;
	if (isset($input['d']))
	{
		$day = $input['d'];
	}
	else if (isset($input['day']))
	{
		$day = $input['day'];
	}
	else if (!count($input) && isset($argv[1]))
	{
		$day = $argv[1];
		//debug ($argv);
	}
	if (isset($day))
	{
		if ($day[0]=='+' || $day[0]=='-')
		{
			$skip = $day[0]=='+'?(1):(-1);
			$day = intval(substr($day,1));
			if (!$day)
			{
				$day = 1;
			}
		}
		else
		{
			$day = intval($day);
		}
		if ($day==0)
		{
			$day = null;
		}
	}
	$now = \budget\date::now (!isset($skip)?$day:(\budget\date::day()+($skip*$day)));
	//debug ($now,'now');

	//init calendar control
	$calendar = [1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''];
	$date = \budget\date::now (1);
	$date->set (1);
	if ($date->week>1)
	{
		for ($day=1; $day<$date->week; $day++)
		{
			$calendar[$day] = "            \t";
		}
	}

	//calculate month average
	$date = \budget\date::now ();
	$average = 0;
	for ($day=1; $day<=intval(date("t",time())); $day++)
	{
		$sum = 0;
		$date->set ($day);
		foreach ($user->expenses as $item)
		{
			if ($item->active($date))
			{
				$sum += $item->amount->convert($user->currency)->value;
			}
		}
		$average = ($average+$sum)/2;
	}
	$average = round ($average,2);

	//draw calendar
	$date = \budget\date::now ();
	$total = 0;
	$need = 0;
	$used = 0;
	$categories = array ();
	for ($day=1; $day<=intval(date("t",time())); $day++)
	{
		$sum = 0;
		$date->set ($day);
		foreach ($user->expenses as $item)
		{
			if ($item->active($date))
			{
				if (!isset($categories[$item->category]))
				{
					$categories[$item->category] = 0;
				}
				$categories[$item->category] += $item->amount->convert($user->currency)->value;
				$sum += $item->amount->convert($user->currency)->value;
			}
		}
		$total += $sum;
		if ($date->day<$now->day)
		{
			$used += $sum;
		}
		if ($date->day>=$now->day)
		{
			$need += $sum;
		}
		$calendar[$date->week] .= color(str_pad($day,2," ",STR_PAD_LEFT),($date->day==$now->day)?YELLOW:MAROON)
							   ." ".color(strtoupper($date->name()),($date->day==$now->day)?YELLOW:GREEN)
							   ." ".color(str_pad($sum,5," ",STR_PAD_LEFT)."\t",$sum>$average?RED:($sum>($average/2)?SILVER:GRAY));
	}

	foreach ($calendar as $item)
	{
		echo $item."\n";
	}

	echo "\n";
	arsort ($categories);
	foreach ($categories as $name => $value)
	{
		echo color($name,CYAN)." ".color($value,RED)."  ";
	}

	echo "\n\n";

	//echo "Daily Average ".color($average,RED)."\n";
	//echo "Budget ".color($user->budget->value,GREEN)."\n";
	//echo "Balance ".color($user->balance->value,BROWN)."\n";
	echo "Total budged plan is ".color($total,CYAN).", from given date ".color($need,RED)."\n\n";
	//echo "Used ".color($used,RED)."\n";


	echo str_pad(" ",8)."Expected | Current\n";
	echo str_pad("Balance",8)." "
		 .color(str_pad($user->budget->value-$used,7," ",STR_PAD_LEFT),RED)." | "
		 .color(str_pad($user->balance->value,7," ",STR_PAD_LEFT),RED)."\n";
	echo str_pad("Expense",8)." "
		 .color(str_pad($used,7," ",STR_PAD_LEFT),RED)." | "
		 .color(str_pad($user->budget->value-$user->balance->value,7," ",STR_PAD_LEFT),RED)."\n";
	echo str_pad("Revenue",8)." "
		 .color(str_pad(round($user->budget->value-$total,2),7," ",STR_PAD_LEFT),RED)." | "
		 .color(str_pad(round($user->balance->value-$need,2),7," ",STR_PAD_LEFT),GREEN)."\n";


	echo "\n";
	$sum = 0;
	echo "Today\n";
	echo "-------------\n";
	$date = \budget\date::now ($now->day);
	if (isset($argv[1]) && intval($argv[1]))
	{
		$date->set ($argv[1]);
	}
	foreach ($user->expenses as $item)
	{
		if ($item->active($date))
		{
			//debug ($item,true);
			$sum += $item->amount->convert($user->currency)->value;
			echo $item->title." "
			    .$item->amount->currency." "
			    .$item->amount->value." | ";
		}
	}
	echo "\n-------------\n";
	echo $user->currency." ".$sum;
	echo "\n";
	echo "\n";


?>