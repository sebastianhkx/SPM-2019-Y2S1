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

Drop bid testcases: (do while logged into ben.ng.2009 account) *can only do during active bidding rounds
	Testcase #1 (fails, bid not found, course+section combination invalid)
		do nothing, show error message

	Testcase #2 (success, bid is removed, 11e$ refunded)
		course: IS100
		section: S1



Bootstrap testcases:
	#testcases should test for all validation requirements stated in wiki
	SampleData(0).zip - Fails as student.csv is missing
	SampleData(1).zip - tests for student.csv 
	SampleData(2).zip - tests for course.csv 
	SampleData(3).zip - tests for section.csv 
	SampleData(4).zip - tests for prerequisite.csv
	SampleData(5).zip - tests for course_completed.csv
	SampleData(6).zip - tests for bid.csv (to be done after add bid datavalidation done in iter2)

SampleData(1).zip errors
	row 2: 'blank password'
	row 3: 'invalid userid'
	row 4: 'invalid password'
	row 5: 'invalid e-dollar', 'invalid name'
	rows successfully loaded for student.csv: 22

SampleData(2).zip errors
	row 2: 'invalid exam date', 'invalid exam start','invalid exam end'
	row 3: 'invalid title', 'invalid description'
	row 4: 'invalid description'
	row 6: 'invalid exam date'
	row 15: 'blank course'
	rows successfully loaded for course.csv: 19

SampleData(3).zip errors
	row 2: 'invalid course'
	row 3: 'invalid section' 
	row 5: 'invalid start'
	row 6: 'invalid instructor' 
	row 7: 'invalid venue'
	row 8: 'invalid start'
	row 9:'invalid size'
	rows successfully loaded for section.csv: 28

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