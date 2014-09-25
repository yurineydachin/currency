-- phpMyAdmin SQL Dump
-- version 4.2.8.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Сен 25 2014 г., 07:02
-- Версия сервера: 5.6.19
-- Версия PHP: 5.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- База данных: `currency`
--

-- --------------------------------------------------------

--
-- Структура таблицы `curr_aggregate_d1`
--

CREATE TABLE IF NOT EXISTS `curr_aggregate_d1` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `time_at` datetime NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL,
  `high` float NOT NULL,
  `low` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `curr_aggregate_h1`
--

CREATE TABLE IF NOT EXISTS `curr_aggregate_h1` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `time_at` datetime NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL,
  `high` float NOT NULL,
  `low` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `curr_aggregate_m1`
--

CREATE TABLE IF NOT EXISTS `curr_aggregate_m1` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `time_at` datetime NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL,
  `high` float NOT NULL,
  `low` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13004 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `curr_aggregate_m5`
--

CREATE TABLE IF NOT EXISTS `curr_aggregate_m5` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `time_at` datetime NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL,
  `high` float NOT NULL,
  `low` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3406 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `curr_aggregate_w1`
--

CREATE TABLE IF NOT EXISTS `curr_aggregate_w1` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `time_at` datetime NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL,
  `high` float NOT NULL,
  `low` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `curr_flow`
--

CREATE TABLE IF NOT EXISTS `curr_flow` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `added_at` datetime NOT NULL,
  `bid` float NOT NULL,
  `ask` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=352358 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `curr_flow_back`
--

CREATE TABLE IF NOT EXISTS `curr_flow_back` (
`id` int(11) NOT NULL,
  `tool` varchar(255) NOT NULL,
  `added_at` datetime NOT NULL,
  `bid` float NOT NULL,
  `ask` float NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=222381 DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `curr_aggregate_d1`
--
ALTER TABLE `curr_aggregate_d1`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `curr_aggregate_h1`
--
ALTER TABLE `curr_aggregate_h1`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `curr_aggregate_m1`
--
ALTER TABLE `curr_aggregate_m1`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `curr_aggregate_m5`
--
ALTER TABLE `curr_aggregate_m5`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `curr_aggregate_w1`
--
ALTER TABLE `curr_aggregate_w1`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `curr_flow`
--
ALTER TABLE `curr_flow`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `curr_flow_back`
--
ALTER TABLE `curr_flow_back`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `curr_aggregate_d1`
--
ALTER TABLE `curr_aggregate_d1`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=111;
--
-- AUTO_INCREMENT для таблицы `curr_aggregate_h1`
--
ALTER TABLE `curr_aggregate_h1`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=501;
--
-- AUTO_INCREMENT для таблицы `curr_aggregate_m1`
--
ALTER TABLE `curr_aggregate_m1`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13004;
--
-- AUTO_INCREMENT для таблицы `curr_aggregate_m5`
--
ALTER TABLE `curr_aggregate_m5`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3406;
--
-- AUTO_INCREMENT для таблицы `curr_aggregate_w1`
--
ALTER TABLE `curr_aggregate_w1`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `curr_flow`
--
ALTER TABLE `curr_flow`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=352358;
--
-- AUTO_INCREMENT для таблицы `curr_flow_back`
--
ALTER TABLE `curr_flow_back`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=222381;
