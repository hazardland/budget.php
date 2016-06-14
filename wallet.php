<?php

	error_reporting (E_ALL);

	//include './lib/debug/debug.php';
	include './lib/expense.php';
	include './lib/color.php';

	const X = 0;

	$user = new \expense\user ();

	function add ($currency, $value, $category, $title=null, $week=[], $day=X, $month=X, $year=X)
	{
		global $user;
		$user->expense (new \expense\expense ($currency, $value, $category, $title, $week, $day, $month, $year));
	}

	function currency ($currency)
	{
		global $user;
		$user->currency = $currency;
		$user->create ();
	}

	function salary ($value)
	{
		global $user;
		$user->salary ($value);
	}

	function balance ($value)
	{
		global $user;
		$user->balance ($value);
	}

	$user = new \expense\user ();

	echo "\n";

	if (file_exists(__DIR__.'/user.php'))
	{
		include __DIR__.'/user.php';
	}

	if (!is_object($user))
	{
		echo color("user not defined\n", RED);
		exit;
	}

	if (!$user->currency)
	{
		echo color("currency not defined\n", RED);
		exit;
	}

	if (!is_array($user->expenses) || !count($user->expenses))
	{
		echo color("expenses not defined\n", RED);
		exit;
	}

	//init calendar control
	$calendar = [1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''];
	$date = new \expense\date ();
	$date->set (1);
	if ($date->week>1)
	{
		for ($day=1; $day<$date->week; $day++)
		{
			$calendar[$day] = "            \t";
		}
	}

	//calculate month average
	$date = new \expense\date ();
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
	$now = new \expense\date ();
	$date = new \expense\date ();
	$total = 0;
	$need = 0;
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
	arsort ($categories);
	foreach ($categories as $name => $value)
	{
		echo color($name,CYAN)." ".color($value,RED)."  ";
	}

	echo "\n\n";

	echo "Average ".color($average,RED)."\n";
	echo "Total ".color($total,CYAN)."\n";
	echo "Current ".color($user->balance->value,BROWN)."\n";
	echo "Need ".color($need,RED)."\n";
	echo "Planned ".color(($user->salary->value-$total),RED)."\n";
	echo "Profit ".color(($user->balance->value-$need),GREEN)."\n";

	echo "\n";
	$sum = 0;
	echo "Today\n";
	echo "-------------\n";
	$date = new \expense\date();
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
			    .$item->amount->value."\n";
		}
	}
	echo "-------------\n";
	echo $user->currency." ".$sum;
	echo "\n";
	echo "\n";


?>