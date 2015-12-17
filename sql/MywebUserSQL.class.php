<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

class MywebUserSQL {

	public static $SQL_CHECK_PASSWORD = '
		select user_id
		from myweb_user
		where user_name = ${userName}
			and	user_password = ${password};';
	
	public static $SQL_SELECT_BY_USERID = '
		select *
		from myweb_user
		where user_id = ${userId};';
	
	public static $SQL_UPDATE_CONFIG_BY_USERID = '
		update myweb_user
		set user_config = ${userConfig}
		where user_id = ${userId};';
	
	public static $SQL_UPDATE_PASSWORD = '
		UPDATE myweb_user
		SET user_password = ${password}
		WHERE user_id = ${userId};';
}
?>
