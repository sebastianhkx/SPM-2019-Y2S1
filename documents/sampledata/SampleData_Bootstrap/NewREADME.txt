Student.csv:
-row2 : 'blank password';
-row3 : 'invalid userid' [exceed 128 characters];
-row4 : 'invalid password' [exceed 128 characters];
-row5 : 'invalid name'[exceed 100 characters] , 'invalid e-dollar'[value with more than 2 decimals];
-row6 : 'invalid e-dollar'[negative value];
-row28 : 'duplicate userid' [two same userid with row7];
-row20 : 'blank userid','blank name','blank school','blank edollar';
rows loaded: student.csv :21

Course.csv:
SampleData(2).zip errors
-row 2: 'invalid exam date', 'invalid exam start','invalid exam end'[incorrect format];
-row 3: 'invalid title', 'invalid description'[exceed 100 characters];
-row 4: 'invalid description'[exceed 100 characters];
-row 5: 'invalid exam end' [exam start time is later than end time]
-row 6: 'invalid exam date'[incorrect format];
-row 15: 'blank course';
-row 16: 'blank school','blank exam date','blank end time';
-row 17: 'invalid exam end' [exam start time is later than end time]
rows successfully loaded for course.csv: 16

Section.csv:
SampleData(3).zip errors
-row 2: 'invalid course'[course is not found in course csv];
-row 3: 'invalid section' [section is not start with 'S'];
-row 5: 'invalid start'[Not a time value];
-row 6: 'invalid instructor' [exceed 100 characters];
-row 7: 'invalid venue'[exceed 100 characters];
-row 8: 'invalid start'[start time is later than end time];
-row 9:'invalid size'[negative value];
-row 10: 'invalid day'[incorrect format];
-row 16: 'blank course','blank section','blank instructor';
rows successfully loaded for section.csv: 26

Prerequisite.csv:
SampleData(4).zip errors
-row 2: 'prerequisite field blank';
-row 3: 'invalid course'[course not found in course csv];
-row 4: 'invalid prerequisite' [course not found in course csv];
-row 7: 'invalid course', 'invalid prerequisite';
-row 8: 'course field blank';
rows successfully loaded for prerequisite.csv: 3

course_completed.csv:
SampleData(5).zip error
-row 2: 'invalid course'[course not found in course csv];
-row 3: 'blank course';
-row 4: 'invalid course completed'[the pre-requisite course has yet to be attempted];
-row 6: 'invalid userid'[userid(case-insensitive) not found in student csv];
-row 7: 'invalid userid'[userid not found in student csv];
-row 8: 'blank userid';
rows successfully loaded for course_completed.csv: 1

bid.csv:
SampleData(6).zip error:
-row 5: 'blank userid';
-row 6: 'blank code','blank section';
-row 7: 'invalid amount'[amount less than 10];
-row 8: 'invalid amount'[negative amount];
-row 23: 'invalid section'[not found in section.csv];
-row 24:'invalid userid'[not found in student.csv];
row successfully loaded for bid.csv:  17

bid.csv:
SampleData(7).zip error:
-row 31: 'section limit reached'[same user has more than 5bids];
-row 2: 'course completed';
-row 4: 'class tiemtable clash' [clash with row 3];-check
-row 10: 'exam timetable clash' [clash with row 9];-check
-row 16: 'incomplete prerequisite';
-row 18: 'not enough e-dollar';
-row 32: 'incomplete prerequisite';
row successfully loaded for bid.csv:24


