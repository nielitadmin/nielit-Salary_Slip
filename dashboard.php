<?php
session_start();
require 'db.php';

// 🔒 Secure access
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// 🧾 Fetch all slips (latest first)
try {
    $stmt = $pdo->query("SELECT * FROM slips ORDER BY id DESC");
    $slips = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $slips = [];
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — NIELIT BHUBANESWAR</title>
<link rel="icon" href="assets/nb_logo.jpg">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    :root {
        --primary: #4f46e5;
        --secondary: #0ea5e9;
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.5);
        --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }

    body {
        font-family: 'Outfit', sans-serif;
        background-color: #f3f4f6;
        overflow-x: hidden;
        min-height: 100vh;
        color: #1f2937;
    }

    /* --- 3D Moving Background Animation --- */
    .animated-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: -1;
        overflow: hidden;
        background: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);
    }
    
    .blob {
        position: absolute;
        filter: blur(80px);
        opacity: 0.8;
        animation: float 20s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
    }
    
    .blob-1 {
        top: -10%; left: -10%; width: 50vw; height: 50vw;
        background: #a78bfa;
        animation-delay: 0s;
    }
    .blob-2 {
        bottom: -10%; right: -10%; width: 50vw; height: 50vw;
        background: #38bdf8;
        animation-delay: -5s;
        animation-direction: alternate-reverse;
    }
    .blob-3 {
        top: 40%; left: 40%; width: 30vw; height: 30vw;
        background: #f472b6;
        animation: float-rotate 25s infinite linear;
        opacity: 0.6;
    }

    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(50px, 80px) scale(1.1); }
    }
    @keyframes float-rotate {
        0% { transform: rotate(0deg) translate(0, 0); }
        50% { transform: rotate(180deg) translate(50px, 20px); }
        100% { transform: rotate(360deg) translate(0, 0); }
    }

    /* --- Glassmorphism Cards --- */
    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        box-shadow: var(--glass-shadow);
        border-radius: 1.5rem;
        transition: transform 0.2s ease-out, box-shadow 0.3s ease;
    }

    /* --- Header Styling --- */
    .header-glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 4px 30px rgba(0,0,0,0.05);
    }

    /* --- Inputs --- */
    .input-group { position: relative; }
    .input-group i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        transition: color 0.3s;
    }
    .modern-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.8rem;
        background: rgba(255,255,255,0.6);
        border: 1px solid rgba(209, 213, 219, 0.8);
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .modern-input:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }
    .modern-input:focus + i { color: var(--primary); }
    
    label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.3rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* --- Buttons --- */
    .btn-3d {
        background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
        color: white;
        border: none;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        z-index: 1;
    }
    .btn-3d::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(135deg, #0ea5e9 0%, #4f46e5 100%);
        opacity: 0;
        z-index: -1;
        transition: opacity 0.3s ease;
    }
    .btn-3d:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.5);
    }
    .btn-3d:hover::before { opacity: 1; }

    /* --- Scrollbar --- */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.5); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(107, 114, 128, 0.8); }

    /* --- Animations --- */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-entry { animation: slideIn 0.6s ease-out forwards; opacity: 0; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* Specific highlights */
    .highlight-blue { background: rgba(239, 246, 255, 0.8); color: #1e40af; border-color: #bfdbfe; font-weight: 700; }
    .highlight-green { background: rgba(240, 253, 244, 0.8); color: #166534; border-color: #bbf7d0; font-weight: 700; }
    .highlight-red { background: rgba(254, 242, 242, 0.8); color: #991b1b; border-color: #fecaca; font-weight: 700; }
</style>
</head>

<body class="p-4 sm:p-6 lg:p-8">

<div class="animated-bg">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<header class="header-glass rounded-2xl mb-8 sticky top-4 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="relative group cursor-pointer">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg blur opacity-25 group-hover:opacity-75 transition duration-200"></div>
                <img src="assets/nb_logo.jpg" class="relative w-12 h-12 rounded-lg object-cover shadow-sm bg-white" alt="Logo">
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-cyan-600">NIELIT BHUBANESWAR</h1>
                <p class="text-xs text-gray-500 font-medium tracking-wider">SALARY AUTOMATION SUITE</p>
            </div>
        </div>

        <div class="hidden md:flex flex-col items-end text-right mr-6">
            <div id="clock-time" class="text-2xl font-bold text-gray-700 leading-none font-mono">00:00:00</div>
            <div id="clock-date" class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Mon, Jan 01</div>
        </div>

        <a href="logout.php" class="bg-white text-red-500 border border-red-100 hover:bg-red-50 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-power-off"></i> <span class="hidden sm:inline">Logout</span>
        </a>
    </div>
</header>

<main class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <section class="lg:col-span-8 space-y-8">
        
        <form method="post" action="generate_pdf.php">
            
            <div class="glass-card p-6 mb-6 flex justify-between items-center animate-entry tilt-element">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">New Salary Slip</h2>
                    <p class="text-sm text-gray-500">Fill in the details below to generate.</p>
                </div>
                <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>

            <div class="glass-card p-6 sm:p-8 animate-entry delay-1 tilt-element relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-user-circle text-blue-500"></i> Employee Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label>Employee Name</label>
                        <div class="input-group">
                            <input name="name" class="modern-input" placeholder="John Doe" required>
                            <i class="fa-regular fa-user"></i>
                        </div>
                    </div>
                    <div>
                        <label>Designation</label>
                        <div class="input-group">
                            <input name="designation" class="modern-input" placeholder="e.g. Scientist 'B'" required>
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                    </div>
                    
                    <div>
                        <label>Place of Posting</label>
                        <div class="input-group">
                            <input name="place_of_posting" class="modern-input" value="Bhubaneswar">
                            <i class="fa-solid fa-map-pin"></i>
                        </div>
                    </div>
                    <div>
                        <label>Wing / Section</label>
                        <div class="input-group">
                            <input name="wing_section" class="modern-input" placeholder="e.g. Technical">
                            <i class="fa-solid fa-building"></i>
                        </div>
                    </div>

                    <div>
                        <label>Pay Matrix & Cell</label>
                        <div class="input-group">
                            <input name="pay_matrix_cell" class="modern-input" placeholder="Level-10, Cell-1">
                            <i class="fa-solid fa-table"></i>
                        </div>
                    </div>
                    <div>
                        <label>PAN & Aadhar</label>
                        <div class="input-group">
                            <input name="pan_aadhar" class="modern-input" placeholder="PAN / Aadhar">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                    </div>

                    <div>
                        <label>EPF Account No.</label>
                        <div class="input-group">
                            <input name="epf_account" class="modern-input">
                            <i class="fa-solid fa-piggy-bank"></i>
                        </div>
                    </div>
                    <div>
                        <label>UAN</label>
                        <div class="input-group">
                            <input name="uan" class="modern-input">
                            <i class="fa-solid fa-hashtag"></i>
                        </div>
                    </div>

                    <div>
                        <label>Bank Name & Branch</label>
                        <div class="input-group">
                            <input name="bank_name" class="modern-input">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                    </div>
                    <div>
                        <label>Account Number</label>
                        <div class="input-group">
                            <input name="bank_acc" class="modern-input">
                            <i class="fa-solid fa-money-check"></i>
                        </div>
                    </div>

                    <div>
                        <label>Mode of Payment</label>
                        <div class="input-group">
                            <select name="mode_of_payment" class="modern-input appearance-none">
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Cash">Cash</option>
                            </select>
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                    </div>
                    <div>
                        <label>Date of Payment</label>
                        <div class="input-group">
                            <input type="date" name="date_of_payment" class="modern-input">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                    </div>
                    <div>
                        <label>Days Present</label>
                        <div class="input-group">
                            <input type="number" name="days_present" value="30" class="modern-input">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <div>
                        <label>Month / Year</label>
                        <div class="input-group">
                            <input name="month_year" placeholder="August 2025" class="modern-input">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6 sm:p-8 mt-8 animate-entry delay-2 tilt-element relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-trend-up text-green-500"></i> Earnings
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="col-span-1 sm:col-span-3">
                        <label class="text-blue-600">Basic Pay (B)</label>
                        <div class="input-group">
                            <input type="number" id="basic" name="basic" step="0.01" class="modern-input text-lg font-semibold" placeholder="₹ 0.00" required>
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                        </div>
                    </div>

                    <div>
                        <label>DA %</label>
                        <div class="input-group">
                            <select id="da_percent" name="da_percent" class="modern-input">
                                <?php for($i=0;$i<=100;$i++){ echo "<option value='$i'>$i%</option>"; } ?>
                            </select>
                            <i class="fa-solid fa-percent"></i>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label>DA Amount (Automatic)</label>
                        <div class="input-group">
                            <input type="number" id="da_amount" name="da_amount" class="modern-input bg-gray-50" readonly>
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                    </div>

                    <div>
                        <label>HRA %</label>
                        <div class="input-group">
                            <select id="hra_percent" name="hra_percent" class="modern-input">
                                <option value="10">10%</option>
                                <option value="20">20%</option>
                                <option value="30">30%</option>
                            </select>
                            <i class="fa-solid fa-house"></i>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label>HRA Amount (Automatic)</label>
                        <div class="input-group">
                            <input type="number" id="hra_amount" name="hra_amount" class="modern-input bg-gray-50" readonly>
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                    </div>

                    <div>
                        <label>Transport Allow (TA)</label>
                        <div class="input-group">
                            <select id="ta" name="ta" class="modern-input">
                                <option value="900">₹900</option>
                                <option value="1800">₹1800</option>
                                <option value="3600" selected>₹3600</option>
                                <option value="7200">₹7200</option>
                            </select>
                            <i class="fa-solid fa-bus"></i>
                        </div>
                    </div>
                    <div>
                        <label>DA on TA</label>
                        <div class="input-group">
                            <input type="number" id="da_on_ta" name="da_on_ta" class="modern-input bg-gray-50" readonly>
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                    </div>
                    <div>
                        <label>Other Earnings</label>
                        <div class="input-group">
                            <input type="number" id="other_earnings" name="other_earnings" class="modern-input" value="0">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                    </div>

                    <div class="sm:col-span-3 mt-2">
                        <label class="text-blue-700">Gross Salary</label>
                        <div class="input-group">
                            <input type="number" id="gross_salary" name="gross_salary" class="modern-input highlight-blue" readonly>
                            <i class="fa-solid fa-sack-dollar text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6 sm:p-8 mt-8 animate-entry delay-3 tilt-element relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-trend-down text-red-500"></i> Deductions
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label>EPF/NPS %</label>
                        <div class="input-group">
                            <select id="epf_percent" name="epf_percent" class="modern-input">
                                <option value="10">10%</option>
                                <option value="11">11%</option>
                                <option value="12" selected>12%</option>
                            </select>
                            <i class="fa-solid fa-percent"></i>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label>EPF Amount</label>
                        <div class="input-group">
                            <input type="number" id="epf_amount" name="epf_emp" class="modern-input bg-gray-50" readonly>
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                    </div>

                    <div>
                        <label>Prof. Tax</label>
                        <div class="input-group">
                            <select id="pt" name="pt" class="modern-input">
                                <?php for($i=50;$i<=500;$i+=50){ echo "<option value='$i'>₹$i</option>"; } ?>
                            </select>
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                    </div>
                    <div>
                        <label>Income Tax</label>
                        <div class="input-group">
                            <input type="number" id="it" name="income_tax" class="modern-input" value="0">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                    </div>
                    <div>
                        <label>Other Deduct.</label>
                        <div class="input-group">
                            <input type="number" id="od" name="other_deductions" class="modern-input" value="0">
                            <i class="fa-solid fa-minus-circle"></i>
                        </div>
                    </div>

                    <div class="sm:col-span-3 mt-2">
                        <label class="text-red-700">Total Deductions</label>
                        <div class="input-group">
                            <input type="number" id="total_deductions" name="total_deductions" class="modern-input highlight-red" readonly>
                            <i class="fa-solid fa-hand-holding-dollar text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-6 mt-8 border-l-4 border-green-500 bg-white/80 animate-entry delay-3">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="w-full">
                        <label class="text-green-800 text-lg">Net Payable Salary</label>
                        <div class="input-group mt-1">
                            <input type="number" id="net_salary" name="net_salary" class="modern-input highlight-green text-2xl" readonly>
                            <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn-3d w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-lg shadow-lg flex items-center justify-center gap-2 mt-4 sm:mt-0 whitespace-nowrap">
                        <i class="fa-solid fa-file-pdf"></i> Generate PDF
                    </button>
                </div>
            </div>

        </form>
    </section>

    <aside class="lg:col-span-4 space-y-6">
        <div class="glass-card p-6 sticky top-28 h-[calc(100vh-8rem)] flex flex-col animate-entry delay-2">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Recent History
            </h3>
            
            <div class="overflow-y-auto pr-2 space-y-3 flex-1 custom-scrollbar">
                <?php if (empty($slips)): ?>
                    <div class="text-center py-10 opacity-60">
                        <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-400"></i>
                        <p class="text-sm text-gray-500">No slips generated yet.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($slips as $s): ?>
                    <div class="bg-white/50 hover:bg-white p-4 rounded-xl border border-white/60 shadow-sm transition-all hover:shadow-md group">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm"><?=htmlspecialchars($s['employee_name'] ?? 'Unknown')?></h4>
                                <p class="text-xs text-gray-500"><?=htmlspecialchars($s['month_year'])?></p>
                            </div>
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-bold">
                                ₹<?=number_format($s['net_salary'],0)?>
                            </span>
                        </div>
                        <div class="flex gap-2 mt-3 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a target="_blank" href="generate_pdf.php?id=<?=$s['id']?>&download=0" class="flex-1 text-center py-1.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100 transition">View</a>
                            <a href="generate_pdf.php?id=<?=$s['id']?>&download=1" class="flex-1 text-center py-1.5 rounded-lg bg-green-50 text-green-600 text-xs font-semibold hover:bg-green-100 transition">Save</a>
                            <a href="delete_slip.php?id=<?=$s['id']?>" onclick="return confirm('Delete this salary slip?')" class="w-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>

</main>

<script>
// --- Real-time Clock ---
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateString = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    
    document.getElementById('clock-time').textContent = timeString;
    document.getElementById('clock-date').textContent = dateString;
}
setInterval(updateClock, 1000);
updateClock();

// --- 3D Tilt Effect (Lightweight Vanilla JS) ---
document.addEventListener('mousemove', (e) => {
    const cards = document.querySelectorAll('.tilt-element');
    const x = (window.innerWidth / 2 - e.pageX) / 50;
    const y = (window.innerHeight / 2 - e.pageY) / 50;

    cards.forEach(card => {
        // Subtle tilt
        card.style.transform = `rotateY(${x}deg) rotateX(${y}deg)`;
    });
});

// --- Calculator Logic (Existing) ---
function calcAll() {
    const B = parseFloat(document.getElementById('basic').value) || 0;
    const DA_percent = parseFloat(document.getElementById('da_percent').value) / 100;
    const HRA_percent = parseFloat(document.getElementById('hra_percent').value) / 100;
    const TA = parseFloat(document.getElementById('ta').value) || 0;
    const OE = parseFloat(document.getElementById('other_earnings').value) || 0;
    const EPF = parseFloat(document.getElementById('epf_percent').value) / 100;
    const PT = parseFloat(document.getElementById('pt').value) || 0;
    const IT = parseFloat(document.getElementById('it').value) || 0;
    const OD = parseFloat(document.getElementById('od').value) || 0;

    const DA_amt = Math.round(B * DA_percent);
    const HRA_amt = Math.round(B * HRA_percent);
    const DA_TA = Math.round(TA * DA_percent);
    const GS = Math.round(B + DA_amt + HRA_amt + TA + DA_TA + OE);
    const EPF_amt = Math.round((B + DA_amt) * EPF);
    const TD = Math.round(EPF_amt + PT + IT + OD);
    const NP = Math.round(GS - TD);

    document.getElementById('da_amount').value = DA_amt;
    document.getElementById('hra_amount').value = HRA_amt;
    document.getElementById('da_on_ta').value = DA_TA;
    document.getElementById('gross_salary').value = GS;
    document.getElementById('epf_amount').value = EPF_amt;
    document.getElementById('total_deductions').value = TD;
    document.getElementById('net_salary').value = NP;
}

document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('input', calcAll);
    el.addEventListener('change', calcAll);
});
</script>
</body>
</html>