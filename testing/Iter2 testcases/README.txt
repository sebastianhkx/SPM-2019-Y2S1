Add bid testcases: (do while logged into ben.ng.2009 account, has $189.00 after bootstrap, doesn't have to be done in order, can rebootstrap to refresh his edollar balance) *can only do during active bidding rounds
	Testcase #1 (success, new bid)
		course: IS200
		section: S1
		bid: $10.10

	Testcase #2 (success, new bid)
		course: IS105
		section: S2
		bid: $20.00

	Testcase #3 (success, updated with lower bid) --> ensure that bid amount is properly refunded and updated)
		course: IS100
		section: S1
		bid: $10.00

	Testcase #4 (success, updated with new section) --> same as testcase #3
		course: IS100
		section: S2
		bid: $10.00

	Testcase #4.5 (success, updated with new section, ensure edollar is deducted) --> same as testcase #3
		course: IS100
		section: S1
		bid: $12.00

	Testcase #5 (fails, below $10.00)
		course: IS200
		section: S1
		bid: $8.90

	Testcase #6 (fails, bid amount higher than balance)
		course: IS104
		section: S1
		bid: $250.00

	Testcase #7 (fails, prerequisite not done)
		course: IS209
		section: S1
		bid: $10.00

	Testcase #8 (fails, section number wrong)
		course: IS104
		section: S9
		bid: $10.00

	Testcase #9 (fails, float number has more than 2 decimal place)
		course: IS104
		section: S1
		bid: $10.11111

	Testcase #10 (fails, not own school course, only for round 1)
		course: ECON001
		section: S1
		bid: $12.00

	Testcase #11 (fails, student bid for more than 5 courses)
		check whats the current bid, and see if it returns error once it is pass 5

	Testcase #12 (success, new bid, student has completed pre-req)
		course: IS109
		section: S2
		bid: $10.00

	Testcase #13: (fails, student has already completed this course)
		course: IS102
		section: S1
		bid: $10.00

	Testcase #14: (fails, class timetable clash)
		course: IS106
		section: S1
		bid: $10.00

	Testcase #15: (fails, exam timetable clash)
		# to-do: need to add a new row in course table for an exam time clash, sampledata dont have
		course:
		section:
		bid: