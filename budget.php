<?php

	date_default_timezone_set('Asia/Tbilisi');

	error_reporting (E_ALL);

	include './lib/console/console.php';
	include './lib/debug/debug.php';
	include './lib/budget/budget.php';

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
	$date = reset($range);
	if ($date->week>1)
	{
		for ($day=1; $day<$date->week; $day++)
		{
			$calendar[$day] = "           ";
		}
	}

	//calculate month average
	$average = 0;
	foreach ($range as $date)
	{
		$sum = 0;
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
	$sums = new \budget\sum();
	$sums->total = 0;
	$sums->need = 0;
	$sums->used = 0;
	$categories = array ();
	foreach ($range as $date)
	{
		$sum = 0;
		foreach ($user->expenses as $item)
		{
			if ($item->active($date))
			{
				if (!isset($categories[$item->category]))
				{
					$categories[$item->category] = new \budget\sum($item->category);
				}
				$categories[$item->category]->total->add ($item->amount->convert($user->currency)->value);
				if ($date->time<$now->time)
				{
					$categories[$item->category]->used->add ($item->amount->convert($user->currency)->value);
				}
				if ($date->time>=$now->time)
				{
					$categories[$item->category]->need->add ($item->amount->convert($user->currency)->value);
				}
				$sum += $item->amount->convert($user->currency)->value;
			}
		}
		$sums->total += $sum;
		if ($date->time<$now->time)
		{
			$sums->used += $sum;
		}
		if ($date->time>=$now->time)
		{
			$sums->need += $sum;
		}
		$calendar[$date->week] .= color(str_pad($date->day,2," ",STR_PAD_LEFT),($date->time==$now->time)?YELLOW:MAROON)
							   ." ".color(strtoupper($date->name()[0]),($date->time==$now->time)?YELLOW:GREEN)
							   ." ".color(str_pad($sum,4," ",STR_PAD_LEFT)."  ",$sum>$average?RED:($sum>($average/2)?SILVER:GRAY));
	}

	echo "\n";
	foreach ($calendar as $item)
	{
		echo $item."\n";
	}

	echo "\n";
	$data = array ();
	//arsort ($categories);
	$data['name'][] = '';
	$data['total'][] = 'Total';
	$data['need'][] = 'Need';
	foreach ($categories as $item)
	{
		$data['name'][] = color ($item->name,CYAN);
		$data['total'][] = color($item->total->count."x", YELLOW)." ".color($item->total->value,RED);
		$data['need'][] = $item->need->count?color($item->need->count."x", YELLOW)." ".color($item->need->value,RED):'';
	}
	echo table ($data);
	echo "\n";


	$data = array ();
	$data[] = color("Budget",SILVER)." ".color($user->budget->value,BLUE);
	$data[] = color("Planned",SILVER)." ".color($sums->total,BLUE);
	$data[] = color("Current",SILVER)." ".color($user->balance->value,RED);
	$data[] = "Used ".color($user->budget->value-$user->balance->value,NAVY);
	$data[] = "Saved ".color($sums->used-($user->budget->value-$user->balance->value),NAVY);
	$data[] = "Need ".color($sums->need,RED);
	$data[] = color("Free",YELLOW)." ".color(round($user->balance->value-$sums->need,2),GREEN);
	echo table ($data);


	echo "\n";


	echo "\n";
	$sum = 0;
	$date = \budget\date::now ($now->day);
	echo color("For ".$date->name(true)." \n",GRAY);
	echo color("-------------\n",GRAY);
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
			echo color($item->title." "
			    .$item->amount->currency." "
			    .$item->amount->value." | ", GRAY);
		}
	}
	echo color("\n-------------\n",GRAY);
	echo color($user->currency." ".$sum,GRAY);
	echo "\n";

?>