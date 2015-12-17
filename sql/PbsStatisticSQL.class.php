<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

class PbsStatisticSQL {

	public static $SQL_STATISTIC_INTRO = '
			/*收入*/
			SELECT @v_bill_in := SUM(bill_sum)
			FROM pbs_bill a
			WHERE user_id = ${userId}
				AND EXISTS (
					SELECT 1
					FROM pbs_bill_type b
					WHERE b.bill_type_id = a.bill_type_id
						AND b.bill_type_flag = 1)
				<IF_startDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) >= TO_DAYS(${startDate})
				</IF>
				<IF_endDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) < TO_DAYS(${endDate}) + 1
				</IF>;
			
			/*支出*/
			SELECT @v_bill_out := SUM(bill_sum)
			FROM pbs_bill a
			WHERE user_id = ${userId}
				AND EXISTS (
					SELECT 1
					FROM pbs_bill_type b
					WHERE b.bill_type_id = a.bill_type_id
						AND b.bill_type_flag = 0)
				<IF_startDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) >= TO_DAYS(${startDate})
				</IF>
				<IF_endDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) < TO_DAYS(${endDate}) + 1
				</IF>;
			
			SET @v_bill_in := IF(@v_bill_in IS NULL, 0.00, @v_bill_in);
			SET @v_bill_out := IF(@v_bill_out IS NULL, 0.00, @v_bill_out);
			
			SELECT format(@v_bill_in, 2) bill_in,
				format(@v_bill_out, 2) bill_out,
				format((@v_bill_in - @v_bill_out), 2) bill_revenue;';

	public static $SQL_STATISTIC_ASSET = '
			SELECT sum(account_sum) asset_total_sum
			FROM pbs_account a
			WHERE user_id = ${userId}
				AND EXISTS (
					SELECT 1
					FROM pbs_account_type b
					WHERE a.account_type_id = b.account_type_id
						AND b.account_type_flag in (1, 3));
			
			SELECT a.account_type_id,
				b.account_type_name,
				FORMAT(sum(a.account_sum), 2) account_all_sum
			FROM pbs_account a
				INNER JOIN pbs_account_type b ON a.account_type_id = b.account_type_id	
			WHERE a.user_id = ${userId}
				AND b.account_type_flag in (1, 3)
			GROUP BY a.account_type_id
			ORDER BY b.account_type_flag,
				CONVERT(b.account_type_name USING gbk)COLLATE gbk_chinese_ci;';
	
	public static $SQL_STATISTIC_DEBT = '
			SELECT SUM(account_sum) debt_total_sum
			FROM pbs_account a
			WHERE user_id = ${userId}
				AND EXISTS (
					SELECT 1
					FROM pbs_account_type b
					WHERE a.account_type_id = b.account_type_id
						AND b.account_type_flag = 2);
			
			SELECT a.account_type_id,
				b.account_type_name,
				FORMAT(SUM(a.account_sum), 2) account_all_sum
			FROM pbs_account a
				INNER JOIN pbs_account_type b ON a.account_type_id = b.account_type_id
			WHERE a.user_id = ${userId}
				AND b.account_type_flag = 2
			GROUP BY account_type_id
			ORDER BY CONVERT(b.account_type_name USING gbk)COLLATE gbk_chinese_ci;';
	
	public static $SQL_STATISTIC_BILL = '
			SELECT b.bill_type_id,
				bt.bill_type_name,
				sum(b.bill_sum) bill_sum,
				FORMAT(SUM(b.bill_sum), 2) bill_sum_f
			FROM pbs_bill b,
				pbs_bill_type bt
			WHERE b.user_id = ${userId}
				AND b.bill_type_id = bt.bill_type_id
				AND bt.bill_type_flag = ${billTypeFlag}
				<IF_startDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) >= TO_DAYS(${startDate})
				</IF>
				<IF_endDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) <= TO_DAYS(${endDate}) + 1
				</IF>
			GROUP BY b.bill_type_id
			ORDER BY CONVERT(bt.bill_type_name USING gbk)COLLATE gbk_chinese_ci;';
	
	public static $SQL_STATISTIC_TREND_YEAR = '
			SET @v_year_start := CONCAT(${year}, \'-01-01\');
			SET @v_year_end := CONCAT(${year}, \'-12-31\');
			
			SELECT MONTH(bill_time) month,
				SUM(bill_sum) total_sum
			FROM pbs_bill a
			WHERE a.user_id = ${userId}
				AND TO_DAYS(bill_time) >= TO_DAYS(@v_year_start)
				AND TO_DAYS(bill_time) <= TO_DAYS(@v_year_end)
				<IF_billTypeFlag_IS_NOTNULL>
				AND EXISTS(
					SELECT 1
					FROM pbs_bill_type b
					WHERE a.bill_type_id = b.bill_type_id
						AND b.bill_type_flag = ${billTypeFlag})
				</IF>
				<IF_billTypeId_IS_NOTNULL>
				AND a.bill_type_id = ${billTypeId}
				</IF>
				<IF_accountId_IS_NOTNULL>
				AND account_id = ${accountId}
				</IF>
			GROUP BY MONTH(bill_time)
			ORDER BY MONTH(bill_time);';
	
	public static $SQL_STATISTIC_TREND_MONTH = '
			SELECT day(bill_time) day,
				SUM(bill_sum) total_sum
			FROM pbs_bill a
			WHERE user_id = ${userId}
				AND MONTH(bill_time) = ${month}
				AND YEAR(bill_time) = ${year}
				<IF_billTypeFlag_IS_NOTNULL>
				AND EXISTS(
					SELECT 1
					FROM pbs_bill_type b
					WHERE a.bill_type_id = b.bill_type_id
						AND b.bill_type_flag = ${billTypeFlag})
				</IF>
				<IF_billTypeId_IS_NOTNULL>
				AND a.bill_type_id = ${billTypeId}
				</IF>
				<IF_accountId_IS_NOTNULL>
				AND account_id = ${accountId}
				</IF>
			GROUP BY DAY(bill_time)
			ORDER BY DAY(bill_time);';
}
?>
