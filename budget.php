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
			$calendar[$day] = "              ";
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
							   ." ".color(strtoupper($date->name()),($date->time==$now->time)?YELLOW:GREEN)
							   ." ".color(str_pad($sum,5," ",STR_PAD_LEFT)."  ",$sum>$average?RED:($sum>($average/2)?SILVER:GRAY));
	}

	echo "\n";
	foreach ($calendar as $item)
	{
		echo $item."\n";
	}

	// echo "\n";
	// $data = array ();
	// //arsort ($categories);
	// $data[0][0] = '';
	// $data[0][1] = 'Total';
	// $data[0][2] = 'Need';
	// foreach ($categories as $item)
	// {
	// 	$row = array ();
	// 	$row[0] = color ($item->name,CYAN);
	// 	$row[1] = color($item->total->count."x", YELLOW)." ".color($item->total->value,RED);
	// 	$row[2] = $item->need->count?color($item->need->count."x", YELLOW)." ".color($item->need->value,RED):'';
	// 	$data[] = $row;
	// }
	// echo table ($data);
	// echo "\n";

	$data = array ();
	$data[0][0] = '';
	$data[1][0] = 'Total';
	$data[2][0] = 'Need';
	$row = 0;
	foreach ($categories as $item)
	{
		$row++;
		$data[0][$row] = color ($item->name,CYAN);
		$data[1][$row] = color($item->total->count."x", YELLOW)." ".color($item->total->value,RED);
		$data[2][$row] = $item->need->count?color($item->need->count."x", YELLOW)." ".color($item->need->value,RED):'';
	}
	echo table ($data);
	//echo "\n";
	// $data = "";
	// $data .= color("Budget",SILVER)." ".color($user->budget->value,BLUE)."\n";
	// $data .= color("Planned",SILVER)." ".color($sums->total,BLUE)."\n";
	// $data .= color("Current",SILVER)." ".color($user->balance->value,RED)."\n";
	// $data .= "Used ".color($user->budget->value-$user->balance->value,NAVY)."\n";
	// $data .= "Saved ".color($sums->used-($user->budget->value-$user->balance->value),NAVY)."\n";
	// $data .= "Need ".color($sums->need,RED)."\n";
	// $data .= color("Free",YELLOW)." ".color(round($user->balance->value-$sums->need,2),GREEN)."\n";
	// echo $data;

	$data = array ();
	$data[0][0] = color("Budget",SILVER);
	$data[1][0] = color($user->budget->value,BLUE); //Budget

	$data[0][1] = color("Planned",SILVER);
	$data[1][1] = color($sums->total,BLUE); //Planned

	$data[0][2] = color("Current",SILVER);
	$data[1][2] = color($user->balance->value,RED); //Current

	$data[0][3] = "Expence";
	$data[1][3] = color($sums->used,NAVY); //Used

	$data[0][4] = "Required";
	$data[1][4] = color($sums->need,RED); //Required


	$data[0][5] = "Spent";
	$data[1][5] = color($user->budget->value-$user->balance->value,NAVY); //Used

	$data[0][6] = color("Free",YELLOW);
	$data[1][6] = color(round($user->balance->value-$sums->need,2),GREEN); //Free

	if ($sums->used-($user->budget->value-$user->balance->value)>0)
	{
		$data[0][7] = "Saved";
	}
	else
	{
		$data[0][7] = "Overspend";
	}
	$data[1][7] = color($sums->used-($user->budget->value-$user->balance->value),NAVY); //Overspend/Saved


	echo table ($data);
	//echo "\n";


	//echo "\n";
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