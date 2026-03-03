<?php
session_start();
require 'db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: dashboard.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // update
    $data = $_POST;
    $stmt = $pdo->prepare("UPDATE slips SET month_year=?, basic=?, da=?, hra=?, transport=?, other_earnings=?, epf_emp=?, income_tax=?, professional_tax=?, gsl_insurance=?, other_deductions=?, gross_earnings=?, gross_deductions=?, net_salary=?, days_present=?, date_of_payment=? WHERE id=?");
    $stmt->execute([ $data['month_year'],$data['basic'],$data['da'],$data['hra'],$data['transport'],$data['other_earnings'],$data['epf_emp'],$data['income_tax'],$data['professional_tax'],$data['gsl_insurance'],$data['other_deductions'],$data['gross_earnings'],$data['gross_deductions'],$data['net_salary'],$data['days_present'],$data['date_of_payment'],$id ]);
    header('Location: dashboard.php'); exit;
}
$slip = $pdo->query("SELECT * FROM slips WHERE id = $id")->fetch();
$employees = $pdo->query("SELECT * FROM employees ORDER BY name ASC")->fetchAll();
?>
<!doctype html><html><head><meta charset='utf-8'><title>Edit Slip</title><script src="https://cdn.tailwindcss.com"></script></head><body class='p-6 bg-slate-900 text-white'>
<div class='max-w-3xl mx-auto bg-slate-800 p-6 rounded'>
<h2 class='text-xl mb-4'>Edit Slip</h2>
<form method='post'>
<label>Month/Year</label><input name='month_year' value='<?=htmlspecialchars($slip['month_year'])?>' class='w-full p-2 rounded bg-slate-900 mb-2' required>
<label>Basic</label><input name='basic' value='<?=htmlspecialchars($slip['basic'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>DA</label><input name='da' value='<?=htmlspecialchars($slip['da'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>HRA</label><input name='hra' value='<?=htmlspecialchars($slip['hra'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Transport</label><input name='transport' value='<?=htmlspecialchars($slip['transport'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Other Earnings</label><input name='other_earnings' value='<?=htmlspecialchars($slip['other_earnings'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>EPF</label><input name='epf_emp' value='<?=htmlspecialchars($slip['epf_emp'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Income Tax</label><input name='income_tax' value='<?=htmlspecialchars($slip['income_tax'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Professional Tax</label><input name='professional_tax' value='<?=htmlspecialchars($slip['professional_tax'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>GSL Insurance</label><input name='gsl_insurance' value='<?=htmlspecialchars($slip['gsl_insurance'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Other Deductions</label><input name='other_deductions' value='<?=htmlspecialchars($slip['other_deductions'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Gross Earnings</label><input name='gross_earnings' value='<?=htmlspecialchars($slip['gross_earnings'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Gross Deductions</label><input name='gross_deductions' value='<?=htmlspecialchars($slip['gross_deductions'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Net Salary</label><input name='net_salary' value='<?=htmlspecialchars($slip['net_salary'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Days Present</label><input name='days_present' value='<?=htmlspecialchars($slip['days_present'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<label>Date of Payment</label><input type='date' name='date_of_payment' value='<?=htmlspecialchars($slip['date_of_payment'])?>' class='w-full p-2 rounded bg-slate-900 mb-2'>
<div class='mt-3'><button class='px-4 py-2 bg-cyan-600 rounded'>Save</button> <a href='dashboard.php' class='ml-2 text-sm text-gray-300'>Back</a></div>
</form></div></body></html>