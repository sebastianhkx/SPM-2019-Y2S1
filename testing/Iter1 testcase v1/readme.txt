SampleData.zip - contains sample data as found on wiki, to be used for testing login function and add bid function
SampleData(1) to SampleData(5) - to be used for testing bootstrap function

Login testcases:
	Testcase #1 (success)
		userid: ben.ng.2009
		password: qwerty129
	Testcase #1 (success)
		userid: amy.ng.2009
		password: qwerty128
	Testcase #2 (success)
		userid: gary.ng.2009
		password: qwerty134
	Testcase #3 (fails, userid is wrong)
		userid: GARY.ng.2009
		password: qwerty134
	Testcase #4 (fails, userid doesn't exist)
		userid: xavier.ong.2010
		password: qwerty123
	Testcase #5 (fails, password is wrong)
		userid: amy.ng.2009
		password: QWERTY123
	

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

Drop bid testcases: (do while logged into ben.ng.2009 account) *can only do during active bidding rounds
	Testcase #1 (fails, bid not found, course+section combination invalid)
		do nothing, show error message

	Testcase #2 (success, bid is removed, 11e$ refunded)
		course: IS100
		section: S1



Bootstrap testcases:
	#testcases should test for all validation requirements stated in wiki
	SampleData(0).zip - Fails as student.csv is missing
	SampleData(1).zip - tests for student.csv - #TODO UPDATE BELOW ON LINE WITH ERRORS AND ERROR MSGS
	SampleData(2).zip - tests for course.csv - #TODO UPDATE BELOW ON LINE WITH ERRORS AND ERROR MSGS
	SampleData(3).zip - tests for section.csv - #TODO UPDATE BELOW ON LINE WITH ERRORS AND ERROR MSGS
	SampleData(4).zip - tests for prerequisite.csv
	SampleData(5).zip - tests for course_completed.csv
	SampleData(6).zip - tests for bid.csv (to be done after add bid datavalidation done in iter2)

SampleData(1).zip errors
#to do
#student.csv
- line2, blank password
- line3, invalid userid
- line4, invalid password
- line5, invalid name&e-dollar
- 

SampleData(2).zip errors
#to do

SampleData(3).zip errors
#to do

SampleData(4).zip errors
	row 2: prerequisite field blank
	row 3: "invalid course"
	row 4: "invalid prerequisite"
	row 7: ["invalid course", "invalid prerequisite"]
	row 8: course field blank
	rows successfully loaded for prerequisite.csv: 3
		
SampleData(5).zip errors
	row 2: code field blank
	row 3: userid field blank
	row 4: prerequsite not completed
	row 6: invalid userid
	row 7: invalid course code
	row 8: prerequisite not completed (nested)
	row 9: prerequisite not completed
	rows successfully loaded for CourseCompleted.csv: 1
	
SampleData(6).zip errors
#to do