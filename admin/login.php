<?php
/**
 * ============================================
 * Login Admin
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';

// Jika admin sudah login, arahkan ke dashboard admin
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/index.php');
}

$pageTitle = 'Login Admin';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        // Verifikasi password (asumsi menggunakan password_hash di database)
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_username'] = $admin['username'];
            
            setFlash('success', 'Selamat datang kembali, Administrator.');
            redirect(BASE_URL . '/admin/index.php');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

include 'includes/header.php';
?>

<!-- Parallax 3D Background -->
<canvas id="parallax-canvas" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>
<style>
    body.g-sidenav-show.bg-gray-100 {
        background: #f8f9fa !important;
        min-height: 100vh;
    }
    /* Login wrapper */
    #login-wrapper {
        position: relative;
        z-index: 10;
    }
    /* Card 3D tilt container */
    #card-3d-wrapper {
        perspective: 1000px;
        perspective-origin: center center;
    }
    #card-3d-inner {
        transform-style: preserve-3d;
        transition: transform 0.05s linear;
        will-change: transform;
    }
    #login-wrapper .card {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.8) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        border-radius: 1.25rem !important;
    }
    #login-wrapper .card h5 {
        color: #344767;
    }
    #login-wrapper .card p {
        color: #67748e;
    }
    #login-wrapper .form-control {
        background: #fff !important;
        border: 1px solid #d2d6da !important;
        color: #495057 !important;
        border-radius: 0.5rem !important;
    }
    #login-wrapper .form-control:focus {
        border-color: #e293d3 !important;
        box-shadow: 0 0 0 2px #e9aede !important;
    }
    #login-wrapper .btn.bg-gradient-dark {
        background-image: linear-gradient(310deg, #141727 0%, #3A416F 100%) !important;
        border: none;
        letter-spacing: 0.04em;
        font-weight: 600;
        box-shadow: 0 4px 7px -1px rgba(0,0,0,0.11), 0 2px 4px -1px rgba(0,0,0,0.07);
    }
    #login-wrapper .alert-danger {
        background: #fde6e8 !important;
        border: 1px solid #f6c3c8;
        color: #fd5c70;
    }
    /* Shine effect on card */
    #card-3d-inner::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1.25rem;
        background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 0%), rgba(255,255,255,0.8) 0%, transparent 60%);
        pointer-events: none;
        z-index: 2;
        transition: background 0.05s;
    }
</style>

<div id="login-wrapper" class="d-flex align-items-center justify-content-center min-vh-100">
    <div id="card-3d-wrapper" class="col-xl-4 col-lg-5 col-md-7">
        <div id="card-3d-inner">
        <div class="card z-index-0">
            <div class="card-header text-center pt-4 pb-4">
                <h5>Login Administrator</h5>
                <p class="text-sm mb-0">Sistem Kendaraan Perumahan</p>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger text-white p-2 text-sm text-center">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form role="form text-left" method="POST" action="">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" aria-label="Username" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" aria-label="Password" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn bg-gradient-dark w-100 mt-4 mb-2">Masuk</button>
                    </div>
                    <p class="text-sm mt-3 mb-0 text-center">Belum punya akun? <a href="register.php" class="text-dark font-weight-bolder">Daftar Admin</a></p>
                </form>
            </div>
        </div>
        </div> <!-- end card-3d-inner -->
    </div>
</div>

<script>
(function() {
    // =============================================
    // Canvas Bubble Parallax 3D
    // =============================================
    const canvas = document.getElementById('parallax-canvas');
    const ctx = canvas.getContext('2d');

    let W = canvas.width  = window.innerWidth;
    let H = canvas.height = window.innerHeight;

    window.addEventListener('resize', () => {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    });

    // ---- Particles config ----
    const NUM_PARTICLES = 80;
    const particles = [];
    
    for (let i = 0; i < NUM_PARTICLES; i++) {
        const layer = Math.floor(Math.random() * 3); // 0, 1, 2
        particles.push({
            x: Math.random() * W,
            y: Math.random() * H,
            baseX: 0,
            baseY: 0,
            r: (layer + 1) * 1.5,
            layer: layer,
            alpha: 0.2 + (layer * 0.2),
            driftX: (Math.random() - 0.5) * 0.4,
            driftY: (Math.random() - 0.5) * 0.4,
            phase: Math.random() * Math.PI * 2,
            speed: 0.005 + Math.random() * 0.01
        });
        particles[i].baseX = particles[i].x;
        particles[i].baseY = particles[i].y;
    }

    // ---- Mouse state ----
    let mouseX = W / 2, mouseY = H / 2;
    let curX = 0, curY = 0;
    let tarX = 0, tarY = 0;

    document.addEventListener('mousemove', e => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        tarX = (e.clientX / W - 0.5) * 2;
        tarY = (e.clientY / H - 0.5) * 2;
    });

    const DEPTH_MULT = [20, 50, 90]; 

    let t = 0;
    function draw() {
        t += 1;
        curX += (tarX - curX) * 0.05;
        curY += (tarY - curY) * 0.05;

        // Clear with white background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        for (let l = 0; l <= 2; l++) {
            const layerParticles = particles.filter(p => p.layer === l);
            
            layerParticles.forEach(p => {
                p.baseX += p.driftX;
                p.baseY += p.driftY;
                
                if (p.baseY > H + p.r) p.baseY = -p.r;
                if (p.baseY < -p.r) p.baseY = H + p.r;
                if (p.baseX > W + p.r) p.baseX = -p.r;
                if (p.baseX < -p.r) p.baseX = W + p.r;

                const sway = Math.sin(t * p.speed + p.phase) * 15;
                const px = curX * DEPTH_MULT[p.layer];
                const py = curY * DEPTH_MULT[p.layer];

                const dx = p.baseX + sway + px;
                const dy = p.baseY + py;

                ctx.beginPath();
                ctx.arc(dx, dy, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(50, 60, 90, ${p.alpha})`;
                ctx.fill();
            });
            
            // Draw connecting lines for particles in the same layer
            ctx.beginPath();
            ctx.strokeStyle = `rgba(50, 60, 90, ${0.05 + l * 0.05})`;
            ctx.lineWidth = 0.5 + (l * 0.2);
            for (let i = 0; i < layerParticles.length; i++) {
                for (let j = i + 1; j < layerParticles.length; j++) {
                    const p1 = layerParticles[i];
                    const p2 = layerParticles[j];
                    
                    const p1x = p1.baseX + Math.sin(t * p1.speed + p1.phase) * 15 + curX * DEPTH_MULT[p1.layer];
                    const p1y = p1.baseY + curY * DEPTH_MULT[p1.layer];
                    
                    const p2x = p2.baseX + Math.sin(t * p2.speed + p2.phase) * 15 + curX * DEPTH_MULT[p2.layer];
                    const p2y = p2.baseY + curY * DEPTH_MULT[p2.layer];
                    
                    const dist = Math.hypot(p1x - p2x, p1y - p2y);
                    if (dist < 120) {
                        ctx.moveTo(p1x, p1y);
                        ctx.lineTo(p2x, p2y);
                    }
                }
            }
            ctx.stroke();
        }

        requestAnimationFrame(draw);
    }
    draw();

    // =============================================
    // Card 3D Tilt on Mouse Move
    // =============================================
    const wrapper   = document.getElementById('card-3d-wrapper');
    const cardInner = document.getElementById('card-3d-inner');

    const MAX_TILT = 14; // degrees

    wrapper.addEventListener('mousemove', function(e) {
        const rect   = wrapper.getBoundingClientRect();
        const cx     = rect.left + rect.width  / 2;
        const cy     = rect.top  + rect.height / 2;
        const relX   = (e.clientX - cx) / (rect.width  / 2); // -1..1
        const relY   = (e.clientY - cy) / (rect.height / 2); // -1..1
        const rotY   =  relX * MAX_TILT;
        const rotX   = -relY * MAX_TILT;

        // Update shine position CSS vars
        const pctX = ((e.clientX - rect.left) / rect.width  * 100).toFixed(1);
        const pctY = ((e.clientY - rect.top)  / rect.height * 100).toFixed(1);
        cardInner.style.setProperty('--mouse-x', pctX + '%');
        cardInner.style.setProperty('--mouse-y', pctY + '%');

        cardInner.style.transform =
            `rotateX(${rotX}deg) rotateY(${rotY}deg) scale3d(1.03,1.03,1.03)`;
    });

    wrapper.addEventListener('mouseleave', function() {
        cardInner.style.transition = 'transform 0.6s cubic-bezier(0.23,1,0.32,1)';
        cardInner.style.transform  = 'rotateX(0deg) rotateY(0deg) scale3d(1,1,1)';
        setTimeout(() => cardInner.style.transition = '', 600);
    });

})();
</script>

<?php include 'includes/footer.php'; ?>
