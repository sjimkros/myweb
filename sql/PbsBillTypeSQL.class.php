<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

class PbsBillTypeSQL {
	
	public static $SQL_SELECT_BY_USERID_BILLTYPEFLAG = '
			SELECT *
			FROM pbs_bill_type
			WHERE (system_flag = 1
				OR user_id = ${userId})
				<IF_billTypeFlag_IS_NOTNULL>
				AND bill_type_flag = ${billTypeFlag}
				</IF>
			ORDER BY system_flag desc,
				CONVERT(bill_type_name USING gbk)COLLATE gbk_chinese_ci;';

	public static $SQL_SELECT_BY_BILLTYPEID = '
			SELECT *
			from pbs_bill_type
			WHERE bill_type_id = ${billTypeId};';
	
	public static $SQL_CHECK_BILLTYPENAME = '
			SELECT count(0) check_count
			FROM pbs_bill_type
			WHERE bill_type_id <> ${billTypeId}
				AND bill_type_name = ${billTypeName}
				AND user_id = ${userId};';
	
	public static $SQL_INSERT = '
			INSERT INTO pbs_bill_type(
				bill_type_name, 
				bill_type_desc, 
				bill_type_flag, 
				user_id, 
				system_flag) 
			VALUES (
				${billTypeName},
				${billTypeDesc},
				${billTypeFlag},
				${userId},
				${systemFlag});';
	
	public static $SQL_UPDATE = '
			UPDATE pbs_bill_type
			SET bill_type_name = ${billTypeName}, 
				bill_type_desc = ${billTypeDesc},
				user_id = ${userId}
			WHERE bill_type_id = ${billTypeId};';
	
	public static $SQL_DELETE = '
			DELETE FROM pbs_bill_type
			where bill_type_id = ${billTypeId};';
	
	public static $SQL_SELECT_SIMPLE_BY_USERID_BILLTYPEFLAG = '
			SELECT bill_type_id,
				bill_type_name,
				bill_type_flag
			FROM pbs_bill_type
			WHERE (system_flag = 1
				OR user_id = ${userId})
				<IF_billTypeFlag_IS_NOTNULL>
				AND bill_type_flag = ${billTypeFlag}
				</IF>
			ORDER BY bill_type_flag,
				CONVERT(bill_type_name USING gbk)COLLATE gbk_chinese_ci;';
}
?>
