<?php
$admin_total_registered_users = in_table("COUNT(id) AS Total","reg_users","","Total");
$admin_total_registered_users = (empty($admin_total_registered_users))?0:$admin_total_registered_users;
$total_referred_users = in_table("COUNT(id) AS Total","referred_users","WHERE referred_by = '$id'","Total");
$total_referred_users = (empty($total_referred_users))?0:$total_referred_users;
$admin_total_referred_users = in_table("COUNT(id) AS Total","referred_users","","Total");
$admin_total_referred_users = (empty($admin_total_referred_users))?0:$admin_total_referred_users;
$all_deposits = in_table("SUM(earning) AS Total","deposits","WHERE user_id = '$id'","Total");
$all_deposits = (empty($all_deposits))?0:$all_deposits;
$admin_all_deposits = in_table("SUM(earning) AS Total","deposits","","Total");
$admin_all_deposits = (empty($admin_all_deposits))?0:$admin_all_deposits;
$total_bonuses = in_table("SUM(amount) AS Total","bonuses","WHERE user_id = '$id'","Total");
$total_bonuses = (empty($total_bonuses))?0:$total_bonuses;
$admin_total_bonuses = in_table("SUM(amount) AS Total","bonuses","","Total");
$admin_total_bonuses = (empty($admin_total_bonuses))?0:$admin_total_bonuses;
$admin_total_bonus_log = in_table("SUM(amount) AS Total","bonus_log","","Total");
$admin_total_bonus_log = (empty($admin_total_bonus_log))?0:$admin_total_bonus_log;
$total_deposit = in_table("SUM(earning) AS Total","deposits","WHERE user_id = '$id' AND maturity_date <= '{$date_time}'","Total");
$total_deposit = (empty($total_deposit))?0:$total_deposit;
$admin_total_deposit = in_table("SUM(earning) AS Total","deposits","WHERE maturity_date <= '{$date_time}'","Total");
$admin_total_deposit = (empty($admin_total_deposit))?0:$admin_total_deposit;
$total_commission = in_table("SUM(amount) AS Total","referral_commisions","WHERE user_id = '$id'","Total");
$total_commission = (empty($total_commission))?0:$total_commission;
$admin_total_commission = in_table("SUM(amount) AS Total","referral_commisions","","Total");
$admin_total_commission = (empty($admin_total_commission))?0:$admin_total_commission;
$total_withdrawal = in_table("SUM(amount) AS Total","withdrawals","WHERE user_id = '$id'","Total");
$total_withdrawal = (empty($total_withdrawal))?0:$total_withdrawal;
$admin_total_withdrawal = in_table("SUM(amount) AS Total","withdrawals","","Total");
$admin_total_withdrawal = (empty($admin_total_withdrawal))?0:$admin_total_withdrawal;
$total_penalties = in_table("SUM(amount) AS Total","penalties","WHERE user_id = '$id'","Total");
$total_penalties = (empty($total_penalties))?0:$total_penalties;
$admin_total_penalties = in_table("SUM(amount) AS Total","penalties","","Total");
$admin_total_penalties = (empty($admin_total_penalties))?0:$admin_total_penalties;
$admin_total_penalty_log = in_table("SUM(amount) AS Total","penalty_log","","Total");
$admin_total_penalty_log = (empty($admin_total_penalty_log))?0:$admin_total_penalty_log;

$curr_balance = $total_deposit + $total_bonuses + $total_commission - $total_withdrawal - $total_penalties;
$curr_balance = (empty($curr_balance))?0:$curr_balance;
$admin_curr_balance = $admin_total_deposit + $admin_total_bonuses + $admin_total_commission - $admin_total_withdrawal - $admin_total_penalties;
$admin_curr_balance = (empty($admin_curr_balance))?0:$admin_curr_balance;

$pending_withdrawal = in_table("SUM(amount) AS Total","transaction_log","WHERE user_id = '$id' AND transaction_type = '2' AND  confirmed = '0'","Total");
$pending_withdrawal = (empty($pending_withdrawal))?0:$pending_withdrawal;
$admin_pending_withdrawal = in_table("SUM(amount) AS Total","transaction_log","WHERE transaction_type = '2' AND  confirmed = '0'","Total");
$admin_pending_withdrawal = (empty($admin_pending_withdrawal))?0:$admin_pending_withdrawal;
?>