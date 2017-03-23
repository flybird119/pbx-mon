
-- cdr database table
-- author: typefo
-- e-mail: typefo@qq.com

DROP DATABASE IF EXISTS `pbxmon`;

CREATE DATABASE `pbxmon`;

USE pbxmon;

CREATE TABLE `cdr` (
       `id` bigint primary key auto_increment not null,
       `caller` varchar(32) not null,
       `called` varchar(32) not null,
       `duration` int not null,
       `file` varchar(128) not null,
       `create_time` datetime not null
);

CREATE TABLE `account` (
       `username` varchar(32) not null,
       `password` varchar(64) not null,
       `email` varchar(64) not null,
       `last_ip` varchar(128) not null,
       `last_time` datetime not null,
       `create_time` datetime not null
);

INSERT INTO `account` VALUES('admin', '94a2282805744c634a13b65e6b44cd5b82d66bff', 'admin@example.com', '127.0.0.1', '1970-01-01 08:00:00', '1970-01-01 08:00:00');

CREATE TABLE `internal` (
       `id` int primary key auto_increment not null,
       `name` varchar(64) not null,
       `ip` varchar(16) not null,
       `port` int not null,
       `description` varchar(64) not null
);


