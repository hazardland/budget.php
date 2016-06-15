<?php

	/*
		define your main currency
	*/
	currency ('GEL');

	/*
		Your monthly salary
	*/
	salary (800);

	/*
		Your balance
	*/
	balance (629);

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
	add ('GEL', 5, 'Taxi', 'Morning taxi', [1,2,3,4,5]);
	add ('GEL', 3, 'Smoke', 'Kent');
	add ('GEL', 12.6, 'Food', 'Wendy', [1,2,3,4,5]);
	add ('GEL', 2, 'Food', 'Evening Food', [1,2,3,4,5],[15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30],[6]);
	add ('GEL', 0.8, 'Marshrutka', 'Back to home', [1,2,3,4,5]);

	//evening
	add ('GEL', 3.6, 'Beer', null, [2,4,6]);

	//friday
	add ('GEL', 15, 'Friday', null, [5]);
	add ('GEL', 5, 'Friday', 'Drunk Taxi', [5]);

	//weekend
	add ('GEL', 10, 'Food', 'Hangover', [6]);
	add ('GEL', 20, 'Food', 'Pizza', X, [19,26]);

	//monthly expenses
	add ('GEL', 14, 'IT', 'Github', X, [5]);

	//medic
	add ('GEL', 55, 'Medic', 'Eye doctor', X, [26]);

	//IT Upcoming
	add ('GEL', 30, 'IT', 'Domain hiking.ge', X, [5], [7], [2016]);


?>