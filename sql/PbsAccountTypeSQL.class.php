<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

class PbsAccountTypeSQL {
	
	public static $SQL_SELECT_SIMPLE_BY_USERID = '
		select account_type_id,
			account_type_name
		from pbs_account_type
		where (system_flag = 1
			or user_id = ${userId}) 
			<IF_accountTypeFlag_IS_NOTNULL>
			and account_type_flag = ${accountTypeFlag}
			</IF>';
}
?>
