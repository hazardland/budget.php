<?php

	/*
		Your monthly salary
	*/
	salary ('GEL', 800);

	/*
		Your balance
	*/
	balance ('GEL', 668);
	
	/*
		SCHEDULE
		function add (string $currency, 
					  float $amount, 
					  string $title, 
					  array $week=[], 
					  int $day=X, 
					  int $month=X, 
					  int$year=X)
	*/


	//daily expenses
	add ('GEL', 5, 'Morning taxi', [1,2,3,4,5]);
	add ('GEL', 0.8, 'Back to home', [1,2,3,4,5]);
	add ('GEL', 9.75, 'Food', [1,2,3,4,5]);
	add ('GEL', 15, 'Drink', [5]);
	add ('GEL', 5, 'Drunk Taxi', [5]);
	add ('GEL', 3, 'Kent');
	add ('GEL', 20, 'Pizza', [7]);

	//monthly expenses
	add ('GEL', 14, 'Github', X, 5);

?>