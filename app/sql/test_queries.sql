use g6t6;

select * from admin;
select * from student;
select * from course_completed;

select * from bid;
select * from bid_result;

select * from prerequisite;

select * from course;
select * from course_completed;
select * from course_enrolled;

select * from section;
select * from round_status;

##
DROP TABLE if exists admin;
drop table course_enrolled;


UPDATE student SET edollar=200 WHERE userid='amy.ng.2009';

SELECT * FROM bid WHERE userid='calvin.ng.2009';

