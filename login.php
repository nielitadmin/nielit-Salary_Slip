<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>NIELIT Bhubaneswar — Salary Slip Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="assets/nb_logo.jpg">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <script src="https://cdn.tailwindcss.com"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/tsparticles-slim@2.0.6/tsparticles.slim.bundle.min.js"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      overflow: hidden; /* Prevent scrollbars from particles */
      background-color: #f0f4f8; /* Fallback */
    }

    /* Glassmorphism Card */
    .glass-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.6);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    }

    /* Input Autofill Fix (keeps glass look) */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active{
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    /* Animation for logo */
    .logo-hover:hover {
        transform: rotate(5deg) scale(1.05);
        transition: all 0.3s ease;
    }

    /* Particle Container */
    #tsparticles {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
  </style>
</head>

<body class="flex items-center justify-center min-h-screen relative">

  <div id="tsparticles"></div>

  <div class="w-full max-w-[420px] mx-4 relative z-10">
    
    <div class="glass-card rounded-2xl p-8 sm:p-10 shadow-2xl transition-all duration-300 hover:shadow-blue-900/10">
      
      <div class="text-center mb-8">
        <div class="inline-block p-2 bg-white rounded-xl shadow-sm mb-4 logo-hover">
            <img src="assets/nb_logo.jpg" alt="NIELIT" class="w-16 h-16 object-contain rounded-lg"> 
        </div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">NIELIT Bhubaneswar</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">Salary Slip Generator Portal</p>
      </div>

      <?php if (isset($_GET['err'])): ?>
        <div class="flex items-center gap-3 bg-red-50 border border-red-100 text-red-600 p-3 rounded-lg mb-6 text-sm animate-bounce">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span>Invalid username or password.</span>
        </div>
      <?php endif; ?>

      <form id="loginForm" method="post" action="authenticate.php" class="space-y-5" autocomplete="off">
        
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <i class="fa-regular fa-user"></i>
            </div>
            <input type="text" name="username" required autofocus
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-200 bg-white/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all duration-200 text-slate-700 placeholder-slate-400 font-medium"
                   placeholder="Enter your ID">
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Password</label>
            <a href="#" class="text-xs text-blue-600 hover:text-blue-700 hover:underline font-medium tabindex='-1'">Forgot Password?</a>
          </div>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
              <i class="fa-solid fa-lock"></i>
            </div>
            <input id="password" name="password" type="password" required
                   class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-slate-200 bg-white/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all duration-200 text-slate-700 placeholder-slate-400 font-medium"
                   placeholder="••••••••">
            <button type="button" id="togglePwd" 
                    class="absolute right-3 top-2.5 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          <div id="capsLockWarning" class="hidden text-xs text-amber-600 mt-2 font-medium flex items-center gap-1">
            <i class="fa-solid fa-triangle-exclamation"></i> Caps Lock is ON
          </div>
        </div>

        <div class="flex items-center justify-between">
           <label class="flex items-center gap-2 cursor-pointer group">
             <input type="checkbox" name="remember" class="peer sr-only">
             <div class="w-4 h-4 border-2 border-slate-300 rounded transition-colors peer-checked:bg-blue-600 peer-checked:border-blue-600 relative">
                <i class="fa-solid fa-check text-[10px] text-white absolute top-0.5 left-0.5 opacity-0 peer-checked:opacity-100"></i>
             </div>
             <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors select-none">Remember me</span>
           </label>
        </div>

        <button id="submitBtn" type="submit"
                class="w-full py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:scale-[1.01] active:scale-[0.98] transition-all duration-200 flex justify-center items-center gap-2">
          <span>Sign In securely</span>
          <i class="fa-solid fa-arrow-right text-sm"></i>
        </button>

      </form>

      <div class="mt-8 pt-5 border-t border-slate-200/60 text-center">
        <p class="text-xs text-slate-400 font-medium">
          &copy; <?php echo date("Y"); ?> NIELIT Bhubaneswar
        </p>
        <p class="text-[10px] text-slate-400 mt-1">
          Designed by <span class="text-blue-600 font-semibold">Kumar Dinesh Behera</span>
        </p>
      </div>
    </div>
    
    <div class="absolute -top-10 -left-10 w-32 h-32 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
    <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-cyan-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>

  </div>

  <script>
    /* --- 1. Particle Configuration --- */
    (async () => {
        await tsParticles.load("tsparticles", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: "#3b82f6" }, // Blue particles
                shape: { type: "circle" },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                move: {
                    enable: true,
                    speed: 1.5,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    attract: { enable: false, rotateX: 600, rotateY: 1200 }
                },
                links: {
                    enable: true,
                    distance: 150,
                    color: "#93c5fd", // Light blue links
                    opacity: 0.3,
                    width: 1
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "grab" },
                    onclick: { enable: true, mode: "push" },
                    resize: true
                },
                modes: {
                    grab: { distance: 140, line_linked: { opacity: 0.5 } },
                    push: { particles_nb: 4 }
                }
            },
            retina_detect: true
        });
    })();

    /* --- 2. Password Toggle --- */
    const toggleBtn = document.getElementById('togglePwd');
    const passwordInput = document.getElementById('password');
    const icon = toggleBtn.querySelector('i');

    toggleBtn.addEventListener('click', () => {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      
      // Switch Icon
      if(isPassword) {
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });

    /* --- 3. Caps Lock Detection --- */
    const capsWarning = document.getElementById('capsLockWarning');
    passwordInput.addEventListener('keyup', function(event) {
        if (event.getModifierState("CapsLock")) {
            capsWarning.classList.remove('hidden');
        } else {
            capsWarning.classList.add('hidden');
        }
    });

    /* --- 4. Loading State on Submit --- */
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');

    form.addEventListener('submit', function() {
        // Change button to loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    });
  </script>

  <style>
    @keyframes blob {
      0% { transform: translate(0px, 0px) scale(1); }
      33% { transform: translate(30px, -50px) scale(1.1); }
      66% { transform: translate(-20px, 20px) scale(0.9); }
      100% { transform: translate(0px, 0px) scale(1); }
    }
    .animate-blob {
      animation: blob 7s infinite;
    }
    .animation-delay-2000 {
      animation-delay: 2s;
    }
  </style>

</body>
</html>