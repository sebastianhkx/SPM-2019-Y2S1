##
use g6t6;

create table bid_result
(
userid varchar(50) not null,
amount int not null,
course varchar(10),
section varchar(3) not null,
outcome varchar(10) not null,
round int not null
);


create table course_enrolled
(
userid varchar(50) not null,
course varchar(10),
section varchar(3) not null,
day int(1) not null,
start time not null,
end time not null,
instructor varchar(50) not null,
venue varchar(50) not null
);



