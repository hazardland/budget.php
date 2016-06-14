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
	balance (668);

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
	add ('GEL', 5, 'Taxi', 'Morning taxi', [1,2,3,4,5]);
	add ('GEL', 0.8, 'Transport', 'Back to home', [1,2,3,4,5]);
	add ('GEL', 9.75, 'Food', 'Food', [1,2,3,4,5]);
	add ('GEL', 3, 'Smoke', 'Kent');

	//evening
	add ('GEL', 3.6, 'Beer', null, [2,4,6]);

	//friday
	add ('GEL', 15, 'Friday', null, [5]);
	add ('GEL', 5, 'Friday', 'Drunk Taxi', [5]);

	//weekend
	add ('GEL', 20, 'Food', 'Pizza', [7]);

	//monthly expenses
	add ('GEL', 14, 'It', 'Github', X, 5);

	//medic
	add ('GEL', 55, 'Medic', 'Eye doctor', X, 30);


?>