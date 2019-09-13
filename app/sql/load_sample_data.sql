
#CHANGE LOAD DIRECTORY TO YOUR OWN

create schema g6t6;
use g6t6;

create table student
(
userid varchar(50) not null,
password varchar(50) not null,
name varchar(50) not null,
school char(3) not null,
edollar int not null,
CONSTRAINT student_pk primary key(userid)
);

create table course
(
course varchar(10) not null,
school char(3) not null,
title varchar(50) not null,
description varchar(1000) not null,
exam_date date not null,
exam_start time not null,
exam_end time  not null,
CONSTRAINT course_pk primary key(course)
);

create table section
(
course varchar(10) not null,
section varchar(3) not null,
day int(1) not null,
start time not null,
end time not null,
instructor varchar(50) not null,
venue varchar(50) not null,
size int not null,
CONSTRAINT section_pk primary key(course, section),
CONSTRAINT section_fk1 foreign key(course) references course(course)
);

create table prerequisite
(
course varchar(10),
prerequisite varchar(10),
CONSTRAINT prequisite_pk primary key(course, prerequisite),
CONSTRAINT prequisite_fk1 foreign key(course) references course(course),
CONSTRAINT prerequisite_fk2 foreign key(prerequisite) references course(course)
);

create table bid
(
userid varchar(50) not null,
amount int not null,
course varchar(10) not null,
section varchar(3) not null,
CONSTRAINT bid_pk primary key(userid, course, section),
CONSTRAINT bid_fk1 foreign key(userid) references student(userid),
CONSTRAINT bid_fk2 foreign key(course, section) references section(course, section)
);

create table course_completed
(
userid varchar(50) not null,
code varchar(10) not null,
CONSTRAINT course_completed_pk primary key(userid, code),
CONSTRAINT course_completed_fk1 foreign key(userid) references student(userid),
CONSTRAINT course_completed_fk2 foreign key(code) references course(course)
);

LOAD DATA LOCAL INFILE "C:\\Users\\Maurice\\Desktop\\sampledata\\student.csv"
INTO TABLE student FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "C:\\Users\\Maurice\\Desktop\\sampledata\\course.csv"
INTO TABLE course FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "C:\\Users\\Maurice\\Desktop\\sampledata\\section.csv"
INTO TABLE section FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "C:\\Users\\Maurice\\Desktop\\sampledata\\prerequisite.csv"
INTO TABLE prerequisite FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "C:\\Users\\Maurice\\Desktop\\sampledata\\bid.csv"
INTO TABLE bid FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "C:\\Users\\Maurice\\Desktop\\sampledata\\course_completed.csv"
INTO TABLE course_completed FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES;
