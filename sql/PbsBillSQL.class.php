<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

class PbsBillSQL {
	
	public static $SQL_COUNT_BY_IDS = '
			SELECT count(0) bill_count
			FROM pbs_bill
			WHERE 1 = 1
				<IF_accountId_IS_NOTNULL>
				AND account_id = ${accountId}
				</IF>
				<IF_billTypeId_IS_NOTNULL>
				AND bill_type_id = ${billTypeId}
				</IF>
				<IF_userId_IS_NOTNULL>
				AND user_id = ${userId}
				</IF>;';
	
	public static $SQL_COUNT_REPAY_BY_RELATED = '
			SELECT count(0) bill_count
			FROM pbs_bill
			WHERE bill_related = ${billId}
				AND bill_type_id in (33, 35);';
	
	public static $SQL_COUNT_DEBT_BY_RELATED = '
			SELECT count(0) bill_count
			FROM pbs_bill
			WHERE bill_related = ${accountId}
				AND bill_type_id in (32, 34);';
	
	public static $SQL_SELECT_BY_CONDITIONS = '
			SELECT COUNT(*) total_count
		    FROM pbs_bill
		    WHERE user_id = ${userId}
				<IF_billTypeId_IS_NOTNULL>
				AND bill_type_id = ${billTypeId}
				</IF>
				<IF_accountId_IS_NOTNULL>
				AND account_id = ${accountId}
				</IF>
				<IF_startDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) >= TO_DAYS(${startDate})
				</IF>
				<IF_endDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) < TO_DAYS(${endDate}) + 1
				</IF>;
			
			SELECT b.bill_id,
				b.bill_type_id,
				b.account_id,
				a.account_name,
				CASE bt.bill_type_flag
					WHEN 1 THEN format(b.bill_sum, 2)
					WHEN 3 THEN format(b.bill_sum, 2)
					WHEN 5 THEN format(b.bill_sum, 2)
					WHEN 7 THEN format(b.bill_sum, 2)
					ELSE \'\'
				END bill_in_sum,
				CASE bt.bill_type_flag
					WHEN 0 THEN format(b.bill_sum, 2)
					WHEN 2 THEN format(b.bill_sum, 2)
					WHEN 4 THEN format(b.bill_sum, 2)
					WHEN 6 THEN format(b.bill_sum, 2)
					ELSE \'\'
				END bill_out_sum,
				bt.bill_type_name,
				b.bill_desc,
				b.bill_related,
				bt.bill_type_flag,
				DATE_FORMAT(b.bill_time, \'%Y-%m-%d\') bill_time
			FROM pbs_bill b
				INNER JOIN pbs_account a ON b.account_id = a.account_id
				INNER JOIN pbs_bill_type bt ON b.bill_type_id = bt.bill_type_id
			WHERE 
				b.user_id = ${userId}
				<IF_billTypeId_IS_NOTNULL>
				AND b.bill_type_id = ${billTypeId}
				</IF>
				<IF_accountId_IS_NOTNULL>
				AND b.account_id = ${accountId}
				</IF>
				<IF_startDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) >= TO_DAYS(${startDate})
				</IF>
				<IF_endDate_IS_NOTNULL>
				AND TO_DAYS(bill_time) < TO_DAYS(${endDate}) + 1
				</IF>
				<IF_billTypeFlag_IS_NOTNULL>
				AND bt.bill_type_flag = ${billTypeFlag}
				</IF>
			ORDER BY b.bill_time desc
			LIMIT ${startPos}, ${pageSize};';
	
	public static $SQL_SELECT_RELATED_BY_CONDITIONS = '
			SELECT b.bill_id,
				b.bill_sum,
				(b.bill_sum - (
					SELECT IFNULL(SUM(bill_sum), 0.00)
					FROM pbs_bill
					WHERE bill_related = b.bill_id)) bill_related_sum,
				b.bill_desc,
				b.account_id,
				a.account_name,
				b.bill_related,
				DATE_FORMAT(b.bill_time, \'%Y-%m-%d\') bill_time
			FROM pbs_bill b,
				pbs_bill_type bt,
				pbs_account a
			WHERE b.user_id = ${userId}
				AND b.bill_type_id = bt.bill_type_id
				AND a.account_id = b.bill_related
				AND bt.bill_type_flag = ${billTypeFlag}
				AND b.bill_repay = 0;';
	
	public static $SQL_SELECT_BY_BILLID = '
			SELECT bill_id, 
				bill_sum, 
				bill_desc, 
				bill_type_id, 
				account_id, 
				user_id, 
				bill_related, 
				bill_repay, 
				DATE_FORMAT(bill_time, \'%Y-%m-%d\') bill_time,
				update_time
			FROM pbs_bill
			WHERE bill_id = ${billId};';
	
	public static $SQL_INSERT_NORMAL = '
			INSERT INTO pbs_bill(
				bill_sum,
				bill_desc,
				bill_type_id,
				account_id,
				user_id,
				bill_time)
			VALUES(
				${billSum},
				${billDesc},
				${billTypeId},
				${accountId},
				${userId},
				${billTime});
				
			UPDATE pbs_account
			SET account_sum = <IF_billTypeFlag_EQUALS_0>account_sum - ${billSum}</IF>
				<IF_billTypeFlag_EQUALS_1>account_sum + ${billSum}</IF>
			WHERE account_id = ${accountId};';
	
	public static $SQL_UPDATE_NORMAL = '
			SELECT @v_last_account := account_id,
				@v_last_sum := bill_sum
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			SET @v_new_bill_sum := ${billSum};
			
			<IF_billTypeFlag_EQUALS_0>
			SET @v_new_bill_sum := 0 - ${billSum};
			</IF>
			<IF_billTypeFlag_EQUALS_1>
			SET @v_last_sum := 0 - @v_last_sum;
			</IF>
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_account = ${accountId}, account_sum + @v_last_sum + @v_new_bill_sum, account_sum + @v_new_bill_sum)
			WHERE account_id = ${accountId};
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_account <> ${accountId}, account_sum + @v_last_sum, account_sum)
			WHERE account_id = IF(@v_last_account <> ${accountId}, @v_last_account, -1);
			
			UPDATE pbs_bill
			SET bill_sum = ${billSum},
				bill_desc = ${billDesc},
				bill_type_id = ${billTypeId},
				account_id = ${accountId},
				bill_time = ${billTime},
				update_time = now()
			WHERE bill_id = ${billId};';
	
	public static $SQL_DELETE_NORMAL = '
			SELECT @v_account_id := account_id,
				@v_bill_sum := bill_sum
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			DELETE FROM pbs_bill
			WHERE bill_id = ${billId};
			
			<IF_billTypeFlag_EQUALS_1>
			SET @v_bill_sum := 0 - @v_bill_sum;
			</IF>
			
			UPDATE pbs_account
			SET account_sum = account_sum + @v_bill_sum
			WHERE account_id = @v_account_id;';

	public static $SQL_INSERT_DEBT = '
			INSERT INTO pbs_bill(
				bill_sum,
				bill_desc,
				bill_type_id,
				account_id,
				user_id,
				bill_related,
				bill_repay,
				bill_time)
			VALUES(
				${billSum},
				${billDesc},
				if(${billTypeFlag} = 5, 32, 34),
				${accountId},
				${userId},
				${account2Id}, /*债务账户id存入关联id中*/
				0,
				${billTime});
			
			SET @v_sum := ${billSum}; 
			<IF_billTypeFlag_EQUALS_6>
			SET @v_sum := 0 - ${billSum};
			</IF>
			
			UPDATE pbs_account
			SET account_sum = account_sum + @v_sum
			WHERE account_id = ${accountId};
			
			UPDATE pbs_account  -- 更新债务、债权账户
			SET account_sum = account_sum + ${billSum}
			WHERE account_id = ${account2Id};';
	
	public static $SQL_UPDATE_DEBT = '
			SELECT @v_last_account := account_id,
				@v_last_debt_account := bill_related,
				@v_last_sum := bill_sum,
				@v_last_sum2 := bill_sum
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			SET @v_new_sum := ${billSum};
			
			<IF_billTypeFlag_EQUALS_5>
			SET @v_last_sum := 0 - @v_last_sum;
			</IF>
			<IF_billTypeFlag_EQUALS_6>
			SET @v_new_sum := 0 - ${billSum};
			</IF>
			
			/*更新借出、借入账户*/
			UPDATE pbs_account
			SET account_sum = IF(@v_last_account = ${accountId}, account_sum + @v_last_sum + @v_new_sum, account_sum + @v_new_sum)
			WHERE account_id = ${accountId};
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_account <> ${accountId}, account_sum + @v_last_sum, account_sum)
			WHERE account_id = IF(@v_last_account <> ${accountId}, @v_last_account, -1);
			
			/*更新债务、债权账户*/
			UPDATE pbs_account
			SET account_sum = IF(@v_last_debt_account = ${account2Id}, account_sum - @v_last_sum2 + ${billSum}, account_sum + ${billSum})
			WHERE account_id = ${account2Id};
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_debt_account <> ${account2Id}, account_sum - @v_last_sum2, account_sum)
			WHERE account_id = IF(@v_last_debt_account <> ${account2Id}, @v_last_debt_account, -1);
			
			UPDATE pbs_bill
			SET bill_sum = ${billSum},
				bill_desc = ${billDesc},
				account_id = ${accountId},
				bill_related = ${account2Id},
				bill_time = ${billTime}
			WHERE bill_id = ${billId};';
	
	public static $SQL_DELETE_DEBT = '
			SELECT @v_last_account := account_id,
				@v_last_sum := bill_sum,
				@v_last_debt_account := bill_related
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			set @v_bill_repayed := (SELECT SUM(bill_sum) FROM pbs_bill WHERE bill_related = ${billId} AND bill_type_id in (33, 35));
			set @v_bill_repayed := IF(@v_bill_repayed IS NULL, 0.00, @v_bill_repayed);
			
			/*删除借入、借出记录*/
			DELETE FROM pbs_bill
			WHERE bill_id = ${billId};
			
			/*删除还款、收款记录*/
			DELETE FROM pbs_bill
			WHERE bill_related = ${billId};
			
			/*更新借入、借出账户*/
			UPDATE pbs_account
			SET account_sum = IF(${billTypeFlag} = 5, account_sum - @v_last_sum + @v_bill_repayed, account_sum + @v_last_sum - @v_bill_repayed)
			WHERE account_id = @v_last_account;
			
			/*更新债务、债权账户*/
			UPDATE pbs_account
			SET account_sum = account_sum - @v_last_sum + @v_bill_repayed
			WHERE account_id = @v_last_debt_account;';

	
	public static $SQL_INSERT_TRANSFER = '
			/*新增转出*/
			INSERT INTO pbs_bill(
				bill_sum,
				bill_type_id,
				account_id,
				user_id,
				bill_time)
			VALUES(
				${billSum},
				30, /*转出类别*/
				${accountId},
				${userId},
				${billTime});
			 
			/*新增转入*/
			SET @v_last_insert := LAST_INSERT_ID();
			INSERT INTO pbs_bill(
				bill_sum,
				bill_desc,
				bill_type_id,
				account_id,
				user_id,
				bill_related,
				bill_time)
			VALUES(
				${billSum},
				${billDesc},
				31, /*转入类别*/
				${account2Id},
				${userId},
				@v_last_insert,
				${billTime});
			
			UPDATE pbs_bill
			SET bill_related = LAST_INSERT_ID()
			WHERE bill_id = @v_last_insert;
			
			/*更新转出账户*/
			UPDATE pbs_account
			SET account_sum = account_sum - ${billSum}
			WHERE account_id = ${accountId};
			/*更新转入账户*/
			UPDATE pbs_account
			SET account_sum = account_sum + ${billSum}
			WHERE account_id = ${account2Id};';

	//TODO 待完成
	public static $SQL_UPDATE_TRNASFER_OUT = '
			/*获取转出记录*/
			SELECT @v_last_out_account := account_id,
				@v_last_out_sum := bill_sum,
				@v_in_related := bill_related
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			SET @v_new_out_sum := 0 - ${billSum};
			
			/*更新转出账户*/
			UPDATE pbs_account
			SET account_sum = IF(@v_last_out_account = ${accountId}, account_sum + @v_last_out_sum + @v_new_out_sum, account_sum + @v_new_out_sum)
			WHERE account_id = ${accountId};
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_out_account <> ${accountId}, account_sum + @v_last_out_sum, account_sum)
			WHERE account_id = IF(@v_last_out_account <> ${accountId}, @v_last_out_account, -1);
			
			/*获取转入记录*/
			SELECT @v_last_in_account := account_id
			FROM pbs_bill
			WHERE bill_id = @v_in_related;
			
			SET @v_new_out_sum := ${billSum};
			
			/*更新转入账户*/
			UPDATE pbs_account
			SET account_sum = IF(@v_last_in_account = ${account2Id}, account_sum - @v_last_out_sum + @v_new_out_sum, account_sum + @v_new_out_sum)
			WHERE account_id = ${account2Id};
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_in_account <> ${account2Id}, account_sum - @v_last_out_sum, account_sum)
			WHERE account_id = IF(@v_last_in_account <> ${account2Id}, @v_last_in_account, -1);
			
			/*更新转出记录*/
			UPDATE pbs_bill
			SET bill_sum = ${billSum},
				bill_desc = null,
				account_id = ${accountId},
				bill_time = ${billTime}
			WHERE bill_id = ${billId};
			
			/*更新转入记录*/
			UPDATE pbs_bill
			SET bill_sum = ${billSum},
				bill_desc = ${billDesc},
				account_id = ${account2Id},
				bill_time = ${billTime}
			WHERE bill_id = @v_in_related;';
	
	public static $SQL_DELETE_TRANSFER = '
			SELECT @v_bill_related := bill_related,
				@v_bill_sum := bill_sum,
				@v_account := account_id
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			/*删除收支*/
			DELETE FROM pbs_bill
			WHERE bill_id = ${billId};
			
			/*更新账户*/
			UPDATE pbs_account
			SET account_sum = IF((${billTypeFlag} = 2), account_sum + @v_bill_sum, account_sum - @v_bill_sum)
			WHERE account_id = @v_account;
			
			SELECT @v_account_related := account_id
			FROM pbs_bill
			WHERE bill_id = @v_bill_related;
			
			/*删除关联收支*/
			DELETE FROM pbs_bill
			WHERE bill_id = @v_bill_related;
			
			-- 更新关联账户
			UPDATE pbs_account
			SET account_sum = IF((${billTypeFlag} = 2), account_sum - @v_bill_sum, account_sum + @v_bill_sum)
			WHERE account_id = @v_account_related;';
	
	public static $SQL_INSERT_REPAY = '
			/*已还金额*/
			SET @v_repayed_sum := (SELECT IFNULL(SUM(bill_sum), 0.00) FROM pbs_bill WHERE bill_related = ${billId} AND bill_type_id in (33, 35));
			
			/*总共应还、应收金额*/
			SELECT @v_repay_total := bill_sum,
				@v_account_debt := bill_related /*对应债务、债权账户*/
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			/*若本次还款、收款与已还、已收金额相加等于总还款、收款额，标记为已还款、已收款*/
			UPDATE pbs_bill
			SET bill_repay = 1
			WHERE bill_id = IF(@v_repay_total = (@v_repayed_sum + ${repaySum}), ${billId}, -1);
				
			INSERT INTO pbs_bill(
				bill_sum,
				bill_desc,
				bill_type_id,
				account_id,
				user_id,
				bill_related,  /*关联的借出、借入记录*/
				bill_time)
			VALUES(
				${repaySum},
				${billDesc},
				IF(${billTypeFlag} = 4, 33, 35),
				${accountId},
				${userId},
				${billId},
				${billTime});
			  
			SET @v_repay_sum := IF(${billTypeFlag} = 4, 0 - ${repaySum}, ${repaySum});
			
			/*更新还款、收款账户*/
			UPDATE pbs_account
			SET account_sum = account_sum + @v_repay_sum
			where account_id = ${accountId};
			/*更新债务、债权账户*/
			UPDATE pbs_account
			SET account_sum = account_sum - ${repaySum}
			WHERE account_id = @v_account_debt;';
	
	public static $SQL_CHECK_REPAY = '
			SELECT a.bill_repay,
				(a.bill_sum - (
					SELECT IFNULL(SUM(bill_sum), 0.00)
					FROM pbs_bill b
					WHERE a.bill_id = b.bill_related
						AND bill_type_id in (33, 35))
					- ${repaySum}) bill_related_sum
			FROM pbs_bill a
			WHERE a.bill_id = ${billId};';
	
	public static $SQL_DELETE_REPAY = '
			/*获取要处理的还款、收款记录*/
			SELECT @v_last_repay_account := account_id,
				@v_last_repay_sum := bill_sum,
				@v_related := bill_related
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			/*获取对应的借款、垫付主记录*/
			select @v_related_account := bill_related
			from pbs_bill
			where bill_id = @v_related;
			
			/*删除还款、收款记录*/
			DELETE FROM pbs_bill
			WHERE bill_id = ${billId};
				
			/*更新借入、借出账户*/
			UPDATE pbs_account
			SET account_sum = IF(${billTypeFlag} = 4, account_sum + @v_last_repay_sum, account_sum - @v_last_repay_sum)
			WHERE account_id = @v_last_repay_account;
			
			/*更新债务、债权账户*/
			UPDATE pbs_account
			SET account_sum = account_sum + @v_last_repay_sum
			WHERE account_id = @v_related_account;
			
			/*检查已还款、已收款记录，更新标识*/
			UPDATE pbs_bill
			SET bill_repay = 0
			WHERE bill_id = @v_related;';
	
	public static $SQ_UPDATE_REPAY = '
			/*获取要处理的还款、收款记录*/
			SELECT @v_last_account := account_id,
				@v_last_sum := bill_sum,
				@v_last_sum2 := bill_sum,
				@v_related := bill_related
			FROM pbs_bill
			WHERE bill_id = ${billId};
			
			SELECT @v_last_debt_account := bill_related,
				@v_last_debt_sum := bill_sum
			FROM pbs_bill
			WHERE bill_id = @v_related
				AND bill_type_id in (32, 34);
			
			SET @v_new_sum := ${billSum};
			<IF_billTypeFlag_EQUALS_4>
			SET @v_new_sum := 0 - ${billSum};
			</IF>
			<IF_billTypeFlag_EQUALS_7>
			SET @v_last_sum := 0 - @v_last_sum;
			</IF>
			
			/*更新收款、还款账户*/
			UPDATE pbs_account
			SET account_sum = IF(@v_last_account = ${accountId}, account_sum + @v_last_sum + @v_new_sum, account_sum + @v_new_sum)
			WHERE account_id = ${accountId};
			
			UPDATE pbs_account
			SET account_sum = IF(@v_last_account <> ${accountId}, account_sum + @v_last_sum, account_sum)
			WHERE account_id = IF(@v_last_account <> ${accountId}, @v_last_account, -1);
			
			/*更新债务、债权账户*/
			UPDATE pbs_account
			SET account_sum = account_sum + @v_last_sum2 - ${billSum}
			WHERE account_id = @v_last_debt_account;
			
			/*更新相应已还款、已收款标记*/
			SET @v_repayed_sum := (
					SELECT SUM(bill_sum) 
					FROM pbs_bill 
					WHERE bill_related = @v_related 
						AND bill_type_id in (33, 35)
						AND bill_id <> ${billId});
			SET @v_repayed_sum := IF(@v_repayed_sum IS NULL, 0.00, @v_repayed_sum);
			
			UPDATE pbs_bill
			SET bill_repay = IF(@v_last_debt_sum = @v_repayed_sum + ${billSum}, 1, 0)
			WHERE bill_id = @v_related;
			
			/*更新收支表*/
			UPDATE pbs_bill
			SET bill_sum = ${billSum},
				bill_desc = ${billDesc},
				account_id = ${accountId},
				bill_time = ${billTime}
			WHERE bill_id = ${billId};';
	
	public static $SQL_SELECT_MIN_BILLTIME_BY_USERID = '
			SELECT MIN(bill_time) min_bill_time
			FROM pbs_bill
			WHERE user_id = ${userId};';
}
?>
