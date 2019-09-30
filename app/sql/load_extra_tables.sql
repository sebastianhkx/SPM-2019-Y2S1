##
use g6t6;

create table round_status
(
round_num int not null,
status varchar(50) not null,
CONSTRAINT round_status_pk primary key(round_num)
);

create table bid_result
(
userid varchar(50) not null,
amount float not null,
course varchar(10),
section varchar(3) not null,
result varchar(10) not null,
round_num int not null,
CONSTRAINT bid_result_fk1 foreign key(round_num) references round_status(round_num)
);


create table course_enrolled
(
userid varchar(50) not null,
course varchar(10) not null,
section varchar(3) not null,
day int(1) not null,
start time not null,
end time not null,

exam_date date not null,
exam_start time not null,
exam_end time  not null
);


insert into round_status values(1, 'started');
insert into round_status values(2, 'pending');


