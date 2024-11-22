CREATE DATABASE TouristAgency;
use TouristAgency;
create table countries( id int not null auto_increment primary key, country varchar(64) unique) default charset='utf8';
create table cities( id int not null auto_increment primary key, city varchar(64), countryid int, foreign key(countryid) references countries(id) on delete cascade, unique index ucity(city, countryid)) default charset='utf8';
create table hotels( id int not null auto_increment primary key, hotel varchar(64), cityid int, foreign key(cityid) references cities(id) on delete cascade, countryid int, foreign key(countryid) references countries(id) on delete cascade, stars int, cost int, info varchar(2048))default charset='utf8';
create table images( id int not null auto_increment primary key, imagepath varchar(255), hotelid int,  foreign key(hotelid) references hotels(id) on delete cascade) default charset='utf8';
create table roles( id int not null auto_increment primary key, role varchar(32))default charset='utf8';
create table users( id int not null auto_increment primary key, login varchar(32) unique, pass varchar(128), email varchar(128), roleid int,  foreign key(roleid) references roles(id) on delete cascade, avatar mediumblob )default charset='utf8';
