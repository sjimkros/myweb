-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-12-09 07:53:13
-- 服务器版本： 5.7.10
-- PHP Version: 5.5.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myweb`
--
USE myweb;

-- --------------------------------------------------------

--
-- 表的结构 `myweb_user`
--

CREATE TABLE `myweb_user` (
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `user_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `user_password` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `user_nickname` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '昵称',
  `user_email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '邮箱',
  `user_face` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '头像',
  `user_sex` tinyint(1) DEFAULT NULL COMMENT '性别 0-男 1-女',
  `user_birthday` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '生日',
  `user_role` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '用户权限',
  `register_time` timestamp NULL DEFAULT NULL COMMENT '注册时间',
  `last_logon` timestamp NULL DEFAULT NULL COMMENT '上次登录时间',
  `user_config` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '配置信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表';

--
-- 转存表中的数据 `myweb_user`
--

INSERT INTO `myweb_user` (`user_id`, `user_name`, `user_password`, `user_nickname`, `user_email`, `user_face`, `user_sex`, `user_birthday`, `user_role`, `register_time`, `last_logon`, `user_config`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '系统管理员', NULL, '/common/image/face/face_5.png', NULL, NULL, NULL, '2009-12-31 16:00:00', '2012-02-23 13:36:31', ''),
(2, 'sjimkros', '2af9b1ba42dc5eb01743e6b3759b6e4b', 'sjimkros', NULL, '/common/image/face/face_8.png', 0, '19870421', NULL, '2009-12-31 16:00:00', '2010-08-26 12:59:22', '');

-- --------------------------------------------------------

--
-- 表的结构 `pbs_account`
--

CREATE TABLE `pbs_account` (
  `account_id` int(10) UNSIGNED NOT NULL COMMENT '账户id',
  `account_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '账户名称',
  `account_desc` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '账户描述',
  `account_sum` decimal(12,2) NOT NULL COMMENT '账户余额',
  `account_type_id` int(10) UNSIGNED NOT NULL COMMENT '账户类型id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `account_flag` int(2) NOT NULL COMMENT '状态，0-正常 1-冻结',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `pbs_account`
--

INSERT INTO `pbs_account` (`account_id`, `account_name`, `account_desc`, `account_sum`, `account_type_id`, `user_id`, `account_flag`, `create_time`) VALUES
(1, '钱包', NULL, '2500.00', 1, 1, 0, '2015-09-21 10:09:34'),
(3, '欠款', NULL, '50.00', 7, 1, 0, '2015-09-21 10:09:46'),
(4, '债权', NULL, '50.00', 6, 1, 0, '2015-10-03 14:08:02'),
(7, 'test_01', NULL, '-150.00', 2, 1, 0, '2015-09-22 03:23:01'),
(8, '信用卡', NULL, '-10.00', 5, 1, 0, '2015-09-28 10:43:42'),
(9, '钱包', NULL, '65096.65', 1, 2, 0, '2015-12-02 14:59:24'),
(10, '欠钱', NULL, '0.00', 6, 2, 0, '2015-12-02 14:59:34'),
(11, '债权', NULL, '0.00', 7, 2, 0, '2015-12-02 14:59:34');

-- --------------------------------------------------------

--
-- 表的结构 `pbs_account_type`
--

CREATE TABLE `pbs_account_type` (
  `account_type_id` int(10) UNSIGNED NOT NULL COMMENT '账户类型id',
  `account_type_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '账户类型名称',
  `account_type_desc` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '账户类型描述',
  `account_type_flag` int(2) NOT NULL COMMENT '标记，1-正常 2-债务 3-债权',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT '用户id',
  `system_flag` int(2) NOT NULL COMMENT '系统标识，1-保留 0-用户自建'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `pbs_account_type`
--

INSERT INTO `pbs_account_type` (`account_type_id`, `account_type_name`, `account_type_desc`, `account_type_flag`, `user_id`, `system_flag`) VALUES
(1, '现金', NULL, 1, NULL, 1),
(2, '储蓄', NULL, 1, NULL, 1),
(3, '其他资金', NULL, 1, NULL, 1),
(4, '固定资产', NULL, 1, NULL, 1),
(5, '信用卡', NULL, 1, NULL, 1),
(6, '债权', NULL, 3, NULL, 1),
(7, '债务', NULL, 2, NULL, 1),
(8, '股票', NULL, 1, NULL, 1),
(9, '基金', NULL, 1, NULL, 1),
(10, '其他投资', NULL, 1, NULL, 1);

-- --------------------------------------------------------

--
-- 表的结构 `pbs_bill`
--

CREATE TABLE `pbs_bill` (
  `bill_id` int(10) UNSIGNED NOT NULL COMMENT '收支id',
  `bill_sum` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '收支金额',
  `bill_desc` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收支描述',
  `bill_type_id` int(10) UNSIGNED NOT NULL COMMENT '收支类别',
  `account_id` int(10) UNSIGNED NOT NULL COMMENT '账户id',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `bill_related` int(10) UNSIGNED DEFAULT NULL COMMENT '关联id',
  `bill_repay` int(2) DEFAULT NULL COMMENT '还款标记，0-未还，1-已还',
  `bill_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '收支时间',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `pbs_bill`
--

INSERT INTO `pbs_bill` (`bill_id`, `bill_sum`, `bill_desc`, `bill_type_id`, `account_id`, `user_id`, `bill_related`, `bill_repay`, `bill_time`, `update_time`) VALUES
(1, '50.00', NULL, 1, 7, 1, NULL, NULL, '2015-09-23 16:00:00', '2015-09-29 06:39:26'),
(2, '50.00', NULL, 22, 7, 1, NULL, NULL, '2015-09-23 16:00:00', '2015-09-29 06:28:41'),
(21, '15.00', NULL, 1, 1, 1, NULL, NULL, '2015-09-27 16:00:00', NULL),
(56, '10.00', NULL, 1, 1, 1, NULL, NULL, '2015-09-27 16:00:00', NULL),
(57, '10.00', NULL, 1, 1, 1, NULL, NULL, '2015-09-27 16:00:00', NULL),
(60, '15.00', NULL, 1, 1, 1, NULL, NULL, '2015-09-27 16:00:00', NULL),
(61, '23.00', NULL, 1, 1, 1, NULL, NULL, '2015-09-27 16:00:00', NULL),
(63, '20.00', NULL, 2, 1, 1, NULL, NULL, '2015-09-27 16:00:00', NULL),
(64, '100.00', NULL, 28, 1, 1, NULL, NULL, '2015-09-28 16:00:00', NULL),
(78, '20.00', NULL, 2, 1, 1, NULL, NULL, '2015-09-28 16:00:00', NULL),
(90, '10.00', '饮料', 2, 1, 1, NULL, NULL, '2015-09-28 16:00:00', '2015-09-29 13:23:49'),
(101, '20.00', NULL, 10, 1, 1, NULL, NULL, '2015-09-29 16:00:00', NULL),
(102, '200.00', NULL, 9, 1, 1, NULL, NULL, '2015-09-29 16:00:00', NULL),
(103, '100.00', '说明测试', 32, 1, 1, 3, 0, '2015-09-29 16:00:00', NULL),
(104, '100.00', NULL, 34, 7, 1, 4, 0, '2015-09-29 16:00:00', NULL),
(105, '50.00', NULL, 32, 1, 1, 3, 1, '2015-09-29 16:00:00', NULL),
(106, '150.00', NULL, 2, 1, 1, NULL, NULL, '2015-09-30 16:00:00', NULL),
(107, '50.00', NULL, 4, 1, 1, NULL, NULL, '2015-09-30 16:00:00', '2015-10-03 00:37:29'),
(108, '50.00', NULL, 33, 1, 1, 103, NULL, '2015-10-02 16:00:00', NULL),
(110, '500.00', NULL, 22, 1, 1, NULL, NULL, '2015-10-02 16:00:00', NULL),
(111, '10.00', NULL, 10, 8, 1, NULL, NULL, '2015-10-02 16:00:00', '2015-10-03 05:20:03'),
(113, '50.00', NULL, 33, 1, 1, 105, NULL, '2015-10-02 16:00:00', NULL),
(117, '50.00', NULL, 35, 1, 1, 104, NULL, '2015-10-02 16:00:00', NULL),
(118, '4393.30', NULL, 22, 9, 2, NULL, NULL, '2015-07-14 16:00:00', NULL),
(119, '2457.09', NULL, 22, 9, 2, NULL, NULL, '2015-08-13 16:00:00', NULL),
(120, '8025.62', '二季度业绩薪资', 23, 9, 2, NULL, NULL, '2015-08-13 16:00:00', '2015-12-02 15:10:23'),
(121, '3406.72', NULL, 22, 9, 2, NULL, NULL, '2015-09-14 16:00:00', NULL),
(122, '7835.21', '二季度业绩薪资', 23, 9, 2, NULL, NULL, '2015-09-21 16:00:00', NULL),
(123, '3641.95', NULL, 22, 9, 2, NULL, NULL, '2015-10-14 16:00:00', NULL),
(124, '7780.03', '二季度业绩薪资', 23, 9, 2, NULL, NULL, '2015-10-20 16:00:00', '2015-12-02 15:14:55'),
(125, '3406.72', NULL, 22, 9, 2, NULL, NULL, '2015-11-14 16:00:00', NULL),
(126, '16533.01', '三季度业绩薪资', 23, 9, 2, NULL, NULL, '2015-11-19 16:00:00', '2015-12-02 15:15:04'),
(127, '3680.00', NULL, 37, 9, 2, NULL, NULL, '2015-11-19 16:00:00', NULL),
(128, '3680.00', NULL, 37, 9, 2, NULL, NULL, '2015-11-19 16:00:00', NULL),
(129, '257.00', NULL, 37, 9, 2, NULL, NULL, '2015-11-25 16:00:00', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `pbs_bill_type`
--

CREATE TABLE `pbs_bill_type` (
  `bill_type_id` int(10) UNSIGNED NOT NULL COMMENT '收支类型id',
  `bill_type_name` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '收支类型名称',
  `bill_type_desc` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '收支类型描述',
  `bill_type_flag` int(2) NOT NULL COMMENT '收支标记，1-收 0-支 3-转入 2-转出 4-还款 5-借入 6-借出 7-收款',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT '用户id',
  `system_flag` int(2) NOT NULL COMMENT '系统标识，1-保留 0-用户自建'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `pbs_bill_type`
--

INSERT INTO `pbs_bill_type` (`bill_type_id`, `bill_type_name`, `bill_type_desc`, `bill_type_flag`, `user_id`, `system_flag`) VALUES
(1, '日常用品', NULL, 0, NULL, 1),
(2, '餐饮饮食', NULL, 0, NULL, 1),
(3, '服装装扮', NULL, 0, NULL, 1),
(4, '通讯宽带', NULL, 0, NULL, 1),
(5, '水电煤', NULL, 0, NULL, 1),
(6, '医疗保健', NULL, 0, NULL, 1),
(7, '油盐酱醋', NULL, 0, NULL, 1),
(8, '家电家具', NULL, 0, NULL, 1),
(9, '数码产品', NULL, 0, NULL, 1),
(10, '交通费', NULL, 0, NULL, 1),
(11, '娱乐活动', NULL, 0, NULL, 1),
(12, '房租物业', NULL, 0, NULL, 1),
(13, '孝敬长辈', NULL, 0, NULL, 1),
(14, '人际交情', NULL, 0, NULL, 1),
(15, '养车费', NULL, 0, NULL, 1),
(16, '运动健身', NULL, 0, NULL, 1),
(17, '美容护肤', NULL, 0, NULL, 1),
(18, '教育培训', NULL, 0, NULL, 1),
(19, '书报音像', NULL, 0, NULL, 1),
(20, '投资亏损', NULL, 0, NULL, 1),
(21, '其他支出', NULL, 0, NULL, 1),
(22, '工资', NULL, 1, NULL, 1),
(23, '奖金', NULL, 1, NULL, 1),
(24, '补贴津贴', NULL, 1, NULL, 1),
(25, '礼金收入', NULL, 1, NULL, 1),
(26, '投资收益', NULL, 1, NULL, 1),
(27, '初始存入', NULL, 1, NULL, 1),
(28, '利息收入', NULL, 1, NULL, 1),
(29, '其他收入', NULL, 1, NULL, 1),
(30, '转出', NULL, 2, NULL, 1),
(31, '转入', NULL, 3, NULL, 1),
(32, '借入/预收', NULL, 5, NULL, 1),
(33, '还款', NULL, 4, NULL, 1),
(34, '借出/垫付', NULL, 6, NULL, 1),
(35, '收款/报销', NULL, 7, NULL, 1),
(36, '理财', NULL, 1, 1, 0),
(37, '报销', NULL, 1, 2, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `myweb_user`
--
ALTER TABLE `myweb_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `INDEX_myweb_user_username` (`user_name`);

--
-- Indexes for table `pbs_account`
--
ALTER TABLE `pbs_account`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `index_account_name` (`account_name`),
  ADD KEY `index_user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `pbs_account_type`
--
ALTER TABLE `pbs_account_type`
  ADD PRIMARY KEY (`account_type_id`);

--
-- Indexes for table `pbs_bill`
--
ALTER TABLE `pbs_bill`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `pbs_bill_type`
--
ALTER TABLE `pbs_bill_type`
  ADD PRIMARY KEY (`bill_type_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `myweb_user`
--
ALTER TABLE `myweb_user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户id', AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `pbs_account`
--
ALTER TABLE `pbs_account`
  MODIFY `account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '账户id', AUTO_INCREMENT=12;
--
-- 使用表AUTO_INCREMENT `pbs_account_type`
--
ALTER TABLE `pbs_account_type`
  MODIFY `account_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '账户类型id', AUTO_INCREMENT=11;
--
-- 使用表AUTO_INCREMENT `pbs_bill`
--
ALTER TABLE `pbs_bill`
  MODIFY `bill_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '收支id', AUTO_INCREMENT=130;
--
-- 使用表AUTO_INCREMENT `pbs_bill_type`
--
ALTER TABLE `pbs_bill_type`
  MODIFY `bill_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '收支类型id', AUTO_INCREMENT=38;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
