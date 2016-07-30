<?php

	$range = \budget\date::range ("2016-07-07","2016-08-03");

	/*
		define your main currency
	*/
	currency ('GEL');

	/*
		Your monthly budget
	*/
	budget (1790);

	/*
		Your balance
	*/
	balance (1675);
	//balance (779.8);

	/*
		SCHEDULE
		function add (string $currency,
					  float $amount,
					  string $category,
					  string $title,
					  array $week=[],
					  int $day=X,
					  int $month=X,
					  int$year=X)
	*/


	//daily expenses
	//add ('GEL', 0.8, 'Marshrutka', 'Go to work', [1,2,3,4,5]);
	add ('GEL', 5, 'Taxi', 'Go to work', [1,2,3,4,5]);
	add ('GEL', 3, 'Smoke', 'Kent');
	add ('GEL', 12.6, 'Food', 'Wendy', [1,2,3,4,5]);
	add ('GEL', 2, 'Food', 'Evening food', [1,2,3,4,5]);
	add ('GEL', 0.8, 'Marshrutka', 'Back to home', [1,2,3,4,5]);

	//hygiene
	add ('GEL', 6.6, 'Hygiene', 'Deodorant', X, [20]);

	//evening
	add ('GEL', 3.6, 'Beer', null, [2,4,6]);

	//friday
	add ('GEL', 15, 'Friday', null, [5]);
	add ('GEL', 5, 'Friday', 'Drunk taxi', [5]);

	//weekend
	add ('GEL', 10, 'Food', 'Hangover', [6]);
	add ('GEL', 20, 'Food', 'Pizza', [7]);

	//monthly expenses
	add ('GEL', 14, 'Interent', 'Github', X, [5]);

	//medic
	add ('GEL', 55, 'Medic', 'Eye doctor', X, [9], [7]);

	//IT Upcoming
	add ('GEL', 30, 'Internet', 'Domain hiking.ge', X, [5], [7], [2016]);

	add ('GEL', 200, 'Misc', 'Ganbajeba', X, [19], [7]);



	//add ('GEL', 1700, 'Moped', null, X, [7], [7]);
	add ('GEL', 150, 'Mshobleb', null, X, [7], [7]);




?>