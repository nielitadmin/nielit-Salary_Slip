<?php
session_start();
require 'db.php';

// 🔹 Require Composer's autoloader for mPDF
require_once __DIR__ . '/vendor/autoload.php'; 

// --- Minimal GET support: if ?id=.. is provided, load existing slip and set variables
$is_get = false;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $download = isset($_GET['download']) ? boolval($_GET['download']) : false;

    $stmt = $pdo->prepare("SELECT * FROM slips WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) exit('Slip not found.');

    // Map DB row to the same $data keys your PDF expects
    $data = [];
    $data['name'] = $row['employee_name'];
    $data['designation'] = $row['designation'];
    $data['month_year'] = $row['month_year'];
    $data['place_of_posting'] = $row['place_of_posting'];
    $data['wing_section'] = $row['wing_section'];
    $data['pay_matrix_cell'] = $row['pay_matrix_cell'];
    $data['pan_aadhar'] = $row['pan_aadhar'];
    $data['epf_account'] = $row['epf_account'];
    $data['uan'] = $row['uan'];
    $data['bank_name'] = $row['bank_name'];
    $data['bank_acc'] = $row['bank_acc'];
    $data['mode_of_payment'] = $row['mode_of_payment'];
    $data['date_of_payment'] = $row['date_of_payment'];
    $data['days_present'] = $row['days_present'];

    // Numeric fields (from DB)
    $basic = floatval($row['basic']);
    $da_amt = floatval($row['da']);
    $hra_amt = floatval($row['hra']);
    $ta = floatval($row['ta']);
    $da_on_ta = floatval($row['da_on_ta'] ?? 0);
    $oe = floatval($row['other_earnings'] ?? 0);
    $gross_salary = floatval($row['gross_salary']);
    $epf_amt = floatval($row['epf_amount'] ?? 0);
    $pt = floatval($row['professional_tax'] ?? 0);
    $it = floatval($row['income_tax'] ?? 0);
    $od = floatval($row['other_deductions'] ?? 0);
    $total_ded = floatval($row['total_deductions']);
    $net_salary = floatval($row['net_salary']);

    // Derive percentages similarly to POST case so existing HTML lines that show percentage work:
    $da_percent = ($basic > 0) ? ($da_amt / $basic) : 0;
    $hra_percent = ($basic > 0) ? ($hra_amt / $basic) : 0;

    $is_get = true;
}

// Only require POST when not re-opening an existing slip
if (!$is_get && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid access. Please submit the form again.');
}

// Collect data from dashboard (POST case)
if (!$is_get) {
    $data = $_POST;

    // 🧮 Salary Calculations
    $basic = floatval($data['basic'] ?? 0);
    $da_percent = floatval($data['da_percent'] ?? 0) / 100;
    $hra_percent = floatval($data['hra_percent'] ?? 0) / 100;
    $ta = floatval($data['ta'] ?? 0);
    $oe = floatval($data['other_earnings'] ?? 0);
    $epf_percent = floatval($data['epf_percent'] ?? 0) / 100;
    $pt = floatval($data['pt'] ?? 0);
    $it = floatval($data['income_tax'] ?? 0);
    $od = floatval($data['other_deductions'] ?? 0);

    // Calculations
    $da_amt = $basic * $da_percent;
    $hra_amt = $basic * $hra_percent;
    $da_on_ta = $ta * $da_percent;
    $gross_salary = $basic + $da_amt + $hra_amt + $ta + $da_on_ta + $oe;
    $epf_amt = ($basic + $da_amt) * $epf_percent;
    $total_ded = $epf_amt + $pt + $it + $od;
    $net_salary = $gross_salary - $total_ded;
}

// Convert number to words (same as before)
function amountInWords($num) {
    $ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine",
             "Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen",
             "Seventeen","Eighteen","Nineteen"];
    $tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
    $num = round($num);
    if ($num == 0) return "Zero Rupees Only";
    $words = "";
    if (($num / 10000000) >= 1) { $words .= amountInWords(floor($num / 10000000)) . " Crore "; $num %= 10000000; }
    if (($num / 100000) >= 1) { $words .= amountInWords(floor($num / 100000)) . " Lakh "; $num %= 100000; }
    if (($num / 1000) >= 1) { $words .= amountInWords(floor($num / 1000)) . " Thousand "; $num %= 1000; }
    if (($num / 100) >= 1) { $words .= amountInWords(floor($num / 100)) . " Hundred "; $num %= 100; }
    if ($num > 0) {
        if ($num < 20) $words .= $ones[$num];
        else $words .= $tens[floor($num / 10)] . " " . $ones[$num % 10];
    }
    return trim($words) . " Rupees Only";
}
$amount_words = amountInWords($net_salary);

// ✅ Insert into Database (only for POST/new slips)
if (!$is_get) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO slips (
                employee_name, designation, month_year, basic, da, hra, ta, da_on_ta,
                other_earnings, gross_salary, epf_amount, professional_tax, income_tax,
                other_deductions, total_deductions, net_salary, date_of_payment,
                place_of_posting, wing_section, pay_matrix_cell, pan_aadhar,
                epf_account, uan, bank_name, bank_acc, mode_of_payment, days_present
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['designation'],
            $data['month_year'],
            $basic,
            $da_amt,
            $hra_amt,
            $ta,
            $da_on_ta,
            $oe,
            $gross_salary,
            $epf_amt,
            $pt,
            $it,
            $od,
            $total_ded,
            $net_salary,
            $data['date_of_payment'],
            $data['place_of_posting'] ?? null,
            $data['wing_section'] ?? null,
            $data['pay_matrix_cell'] ?? null,
            $data['pan_aadhar'] ?? null,
            $data['epf_account'] ?? null,
            $data['uan'] ?? null,
            $data['bank_name'] ?? null,
            $data['bank_acc'] ?? null,
            $data['mode_of_payment'] ?? null,
            $data['days_present'] ?? null
        ]);
    } catch (Exception $e) {
        error_log("DB Insert Error: " . $e->getMessage());
    }
}

// ✅ Generate PDF using mPDF
ob_clean();

// 🔹 Initialize mPDF with wider margins for larger text
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 8,
    'margin_bottom' => 8,
    'default_font' => 'freeserif'
]);

$mpdf->SetCreator('NIELIT Bhubaneswar');
$mpdf->SetAuthor('Kumar Dinesh Behera');
$mpdf->SetTitle('Salary Slip - '.$data['name']);

// 🔹 MAGIC SETTINGS FOR HINDI
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

$logo = __DIR__ . '/assets/nb_logo.jpg';

// 🔹 SCALED UP FONTS: Header text is much larger now
$html = '
<table width="100%" style="line-height:1.1;">
<tr>
  <td width="80%" align="center" valign="middle">
    <div style="font-size:24px; font-weight:bold; color:#003399; margin-bottom:5px; font-family: freesans, sans-serif;">
      राष्ट्रीय इलेक्ट्रॉनिकी एवं सूचना प्रौद्योगिकी संस्थान, भुवनेश्वर
    </div>
    <div style="font-size:16px; color:#003399; margin-bottom:2px;">
      <b>National Institute of Electronics & Information Technology, Bhubaneswar</b>
    </div>
    <div style="font-size:11px; color:#444;">
      3rd Floor, North Side OCAC Tower, Doordarshan Colony, Acharya Vihar, Bhubaneswar - 751013<br>
      Ministry of Electronics and Information Technology (MeitY), Govt. of India
    </div>
  </td>
  <td width="20%" align="right" valign="middle">
    <img src="'.$logo.'" width="90">
  </td>
</tr>
</table>
<hr style="border:0.8px solid #003399; margin-top:8px; margin-bottom:5px;">
<h3 style="text-align:center; color:#003399; margin:4px 0 6px 0; font-size:18px;">Pay Slip for the Month of '.htmlspecialchars($data['month_year']).'</h3>
';

// 🔹 SCALED UP FONTS: Employee Details (font-size: 12px, cellpadding: 4)
$html .= '
<table cellpadding="4" cellspacing="0" border="0" width="100%" style="font-size:12px;">
<tr><td width="25%">Name:</td><td width="25%"><b>'.htmlspecialchars($data['name']).'</b></td><td width="25%">Designation:</td><td width="25%">'.htmlspecialchars($data['designation']).'</td></tr>
<tr><td>Posting:</td><td>'.htmlspecialchars($data['place_of_posting']).'</td><td>Wing/Section:</td><td>'.htmlspecialchars($data['wing_section']).'</td></tr>
<tr><td>Pay Matrix & Cell:</td><td>'.htmlspecialchars($data['pay_matrix_cell']).'</td><td>PAN & Aadhar:</td><td>'.htmlspecialchars($data['pan_aadhar']).'</td></tr>
<tr><td>EPF No.:</td><td>'.htmlspecialchars($data['epf_account']).'</td><td>UAN:</td><td>'.htmlspecialchars($data['uan']).'</td></tr>
<tr><td>Bank:</td><td>'.htmlspecialchars($data['bank_name']).'</td><td>A/C No.:</td><td>'.htmlspecialchars($data['bank_acc']).'</td></tr>
<tr><td>Mode:</td><td>'.htmlspecialchars($data['mode_of_payment']).'</td><td>Date:</td><td>'.htmlspecialchars($data['date_of_payment']).'</td></tr>
<tr><td>Days Present:</td><td>'.htmlspecialchars($data['days_present']).'</td></tr>
</table>
<hr style="border:0.5px solid #ccc; margin-bottom: 5px;">
';

// SALARY DETAILS TABLE
function fmt($v){ return number_format(round($v), 0); }

// 🔹 SCALED UP FONTS: Salary Table (font-size: 12px, cellpadding: 6)
$html .= '
<table cellpadding="6" cellspacing="0" border="1" width="100%" style="font-size:12px; border-collapse:collapse;">
<tr style="background-color:#f0f8ff; font-weight:bold;">
<th width="35%">Earnings</th><th width="15%">Amount (₹)</th><th width="35%">Deductions</th><th width="15%">Amount (₹)</th>
</tr>
<tr><td>Basic Pay</td><td>'.fmt($basic).'</td><td>EPF / NPS (on Basic+DA)</td><td>'.fmt($epf_amt).'</td></tr>
<tr><td>Dearness Allowance ('.($da_percent*100).'%)</td><td>'.fmt($da_amt).'</td><td>Professional Tax</td><td>'.fmt($pt).'</td></tr>
<tr><td>House Rent Allowance ('.($hra_percent*100).'%)</td><td>'.fmt($hra_amt).'</td><td>Income Tax</td><td>'.fmt($it).'</td></tr>
<tr><td>Transport Allowance</td><td>'.fmt($ta).'</td><td>Other Deductions</td><td>'.fmt($od).'</td></tr>
<tr><td>DA on TA</td><td>'.fmt($da_on_ta).'</td><td></td><td></td></tr>
<tr><td>Other Earnings</td><td>'.fmt($oe).'</td><td></td><td></td></tr>
<tr style="font-weight:bold;"><td>Gross Earnings</td><td>'.fmt($gross_salary).'</td><td>Gross Deductions</td><td>'.fmt($total_ded).'</td></tr>
<tr style="font-size:14px; font-weight:bold; background-color:#e8f0ff;">
  <td colspan="2">Net Salary (Take Home)</td><td colspan="2" align="right">₹ '.fmt($net_salary).'</td>
</tr>
</table>
';

// 🔹 SCALED UP FONTS: Footer Text
$html .= '
<p style="margin-top:10px; font-size:12px;"><b>Amount in Words:</b> '.$amount_words.'</p>
<p style="text-align:right; font-size:11px;">This is a system-generated document; no signature required.<br><b>For NIELIT Bhubaneswar</b></p>
<hr style="border:0.8px solid #003399;">
<p style="text-align:center; font-size:11px; color:#003399;">Website: https://nielit.gov.in/bhubaneswar</p>
';

// 🔹 Use mPDF write method
$mpdf->WriteHTML($html);

// 🔹 Output the PDF
$mpdf->Output('SalarySlip_'.$data['name'].'_'.$data['month_year'].'.pdf', \Mpdf\Output\Destination::INLINE);
exit;
?>