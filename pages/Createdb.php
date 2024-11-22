<?php
	include_once ("Functions.php");
    $mysqli = connect();
    $ct1 = "create table countries( id int not null auto_increment primary key, country varchar(64) unique) default charset='utf8'";
    $ct2 = "create table cities( id int not null auto_increment primary key, city varchar(64), countryid int, foreign key(countryid) references countries(id) on delete cascade, unique index ucity(city, countryid)) default charset='utf8'";
    $ct3 = "create table hotels( id int not null auto_increment primary key, hotel varchar(64), cityid int, foreign key(cityid) references cities(id) on delete cascade, countryid int, foreign key(countryid) references countries(id) on delete cascade, stars int, cost int, info varchar(2048))default charset='utf8'";
    $ct4 = "create table images( id int not null auto_increment primary key, imagepath varchar(255), hotelid int,  foreign key(hotelid) references hotels(id) on delete cascade) default charset='utf8'";
    $ct5 = "create table roles( id int not null auto_increment primary key, role varchar(32))default charset='utf8'";
    $ct6 = "create table users( id int not null auto_increment primary key, login varchar(32) unique, pass varchar(128), email varchar(128), roleid int,  foreign key(roleid) references roles(id) on delete cascade, avatar mediumblob )default charset='utf8'";
    
    if (!$mysqli->query($ct1)) {
		printf("Errorcode 1: %d\n", $mysqli->errno);
		exit();
	}

    if (!$mysqli->query($ct2)) {
		printf("Errorcode 2: %d\n", $mysqli->errno);
		exit();
	}
	
    if (!$mysqli->query($ct3)) {
		printf("Errorcode 3: %d\n", $mysqli->errno);
		exit();
	}
	
    if (!$mysqli->query($ct4)) {
		printf("Errorcode 4: %d\n", $mysqli->errno);
		exit();
	} 
	
    if (!$mysqli->query($ct5)) {
		printf("Errorcode 5: %d\n", $mysqli->errno);
		exit();
	}
	
    if (!$mysqli->query($ct6)) {
		printf("Errorcode 6: %d\n", $mysqli->errno);
		exit();
	}
	printf("Tables created!");
?>