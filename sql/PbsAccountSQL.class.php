<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';
class PbsAccountSQL {
	public static $SQL_SELECT_BY_USERID = '
			SELECT t1.account_id,
				t1.account_name,
				t2.account_type_name,
				format(t1.account_sum, 2) account_sum,
				t1.account_desc,
				t2.account_type_flag
			FROM pbs_account t1,
				pbs_account_type t2
			WHERE t1.account_type_id = t2.account_type_id
				AND t1.user_id = ${userId}
				<IF_accountTypeFlag_IS_NOTNULL>
				AND t2.account_type_flag = ${accountTypeFlag}
				</IF>
			ORDER BY convert(t1.account_name using gbk)collate gbk_chinese_ci;';
	
	public static $SQL_CHECK_ACCOUNTNAME = '
			SELECT count(0) check_count
			FROM pbs_account
			WHERE account_id <> ${accountId}
				AND account_name = ${accountName}
				AND user_id = ${userId};';
	
	public static $SQL_INSERT = '
			INSERT INTO pbs_account(
				account_name, 
				account_desc,
				account_sum, 
				account_type_id, 
				user_id, 
				account_flag, 
				create_time) 
			VALUES (
				${accountName},
				${accountDesc},
				${accountSum},
				${accountType},
				${userId},
				${accountFlag},
				now());';
	
	public static $SQL_UPDATE = '
			UPDATE pbs_account
			set	account_name = ${accountName},
				account_desc = ${accountDesc},
				account_sum = ${accountSum},
				account_type_id = ${accountType},
				user_id = ${userId},
				<IF_accountFlag_IS_NOTNULL>
				account_flag = ${accountFlag}
				</IF>
			WHERE account_id = ${accountId};';
	
	public static $SQL_SELECT_BY_ACCOUNTID = '
			SELECT *
			FROM pbs_account
			where account_id = ${accountId};';
	
	public static $SQL_DELETE = '
			DELETE FROM pbs_account
			WHERE account_id = ${accountId};';
	
	public static $SQL_COUNT_BY_USERID = '
			SELECT count(0) account_count
			FROM pbs_account a
				INNER JOIN pbs_account_type b on a.account_type_id = b.account_type_id 
			WHERE a.user_id = ${userId}
				AND b.account_type_flag = ${accountTypeFlag};';
	
	public static $SQL_SELECT_MINID_BY_USERID = '
			SELECT min(account_id) min_id
			FROM pbs_account
			WHERE user_id = ${userId};';
	
	public static $SQL_SELECT_SIMPLE_BY_USERID = '
			SELECT t1.account_id,
				t1.account_name,
				t2.account_type_flag
			FROM pbs_account t1,
				pbs_account_type t2
			WHERE t1.account_type_id = t2.account_type_id
				AND t1.user_id = ${userId}
				<IF_accountTypeFlag_IS_NOTNULL>
				AND t2.account_type_flag = ${accountTypeFlag}
				</IF>
				AND t1.account_flag = 0
			ORDER BY convert(t1.account_name using gbk)collate gbk_chinese_ci;';
}
?>
