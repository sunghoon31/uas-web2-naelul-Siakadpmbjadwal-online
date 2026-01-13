<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aetheris Academic</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --bg: #05070f;
    --bg-secondary: #070a18;
    --fg: #e5e7eb;
    --muted: #9ca3af;
    --accent: #7c7cff;
    --accent-glow: rgba(124, 124, 255, 0.3);
}

body {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background: var(--bg);
    color: var(--fg);
    overflow-x: hidden;
    line-height: 1.6;
}

/* ANIMATED BACKGROUND */
.bg-animation {
    position: fixed;
    inset: 0;
    z-index: 0;
    background: radial-gradient(circle at 20% 50%, rgba(124, 124, 255, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124, 124, 255, 0.06) 0%, transparent 50%);
    animation: bgPulse 8s ease-in-out infinite;
}

@keyframes bgPulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

/* NAVIGATION */
.nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    padding: clamp(16px, 3vw, 24px) clamp(20px, 5vw, 80px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(5, 7, 15, 0.7);
    backdrop-filter: blur(24px) saturate(180%);
    border-bottom: 1px solid rgba(124, 124, 255, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav.scrolled {
    background: rgba(5, 7, 15, 0.95);
    padding: clamp(12px, 2vw, 16px) clamp(20px, 5vw, 80px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
}

.brand {
    display: flex;
    align-items: center;
    gap: clamp(8px, 2vw, 12px);
    cursor: pointer;
    transition: transform 0.3s;
}

.brand:hover {
    transform: translateY(-2px);
}

.logo {
    width: clamp(36px, 5vw, 48px);
    height: clamp(36px, 5vw, 48px);
    border-radius: 12px;
    transition: all 0.4s;
    object-fit: contain;
}

.brand:hover .logo {
    transform: rotate(-8deg) scale(1.1);
    filter: drop-shadow(0 0 20px var(--accent));
}

.brand-text {
    font-weight: 700;
    font-size: clamp(16px, 2.5vw, 20px);
    letter-spacing: -0.5px;
}

.nav-action {
    display: flex;
    align-items: center;
    gap: clamp(12px, 2vw, 24px);
}

.nav-link {
    text-decoration: none;
    color: var(--fg);
    font-weight: 500;
    font-size: clamp(14px, 1.8vw, 16px);
    padding: clamp(8px, 1.5vw, 12px) clamp(16px, 2.5vw, 24px);
    border-radius: 8px;
    position: relative;
    transition: all 0.3s;
}

.nav-link:hover {
    color: var(--accent);
    background: rgba(124, 124, 255, 0.1);
}

.nav-link.cta {
    background: var(--accent);
    color: var(--bg);
    font-weight: 600;
    padding: clamp(10px, 1.8vw, 14px) clamp(20px, 3vw, 32px);
    border-radius: 50px;
    box-shadow: 0 4px 20px var(--accent-glow);
}

.nav-link.cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(124, 124, 255, 0.5);
    background: #8c8cff;
    color: var(--bg);
}

/* HERO */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: clamp(80px, 12vw, 120px) clamp(20px, 5vw, 80px);
}

.hero-bg {
    position: absolute;
    inset: 0;
    background: 
        linear-gradient(to bottom, rgba(5, 7, 15, 0.7), rgba(5, 7, 15, 0.95)),
        radial-gradient(circle at 30% 40%, rgba(0, 0, 0, 0.4), transparent 60%);
    animation: heroFloat 20s ease-in-out infinite;
}

.hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent, rgba(5, 7, 15, 0.9));
    z-index: 1;
    pointer-events: none;
}

@keyframes heroFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(-20px, -20px) scale(1.05); }
}

.hero-content {
    position: relative;
    z-index: 10;
    max-width: 1200px;
    text-align: center;
    animation: fadeInUp 1s ease-out;
    padding: clamp(20px, 4vw, 40px);
    background: rgba(5, 7, 15, 0.6);
    border-radius: 24px;
    backdrop-filter: blur(10px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero h1 {
    font-size: clamp(36px, 7vw, 80px);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: clamp(20px, 4vw, 40px);
    color: #ffffff;
    letter-spacing: -2px;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8), 0 0 60px rgba(124, 124, 255, 0.5);
    filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.9));
}

.hero-subtitle {
    font-size: clamp(16px, 2.5vw, 22px);
    color: #f3f4f6;
    max-width: 700px;
    margin: 0 auto clamp(30px, 5vw, 50px);
    line-height: 1.7;
    text-shadow: 0 2px 15px rgba(0, 0, 0, 0.9), 0 4px 30px rgba(0, 0, 0, 0.7);
    font-weight: 400;
}

.hero-btn {
    display: flex;
    gap: clamp(16px, 3vw, 24px);
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: clamp(14px, 2vw, 18px) clamp(28px, 4vw, 40px);
    font-size: clamp(15px, 2vw, 17px);
    font-weight: 600;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}

.btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.btn:hover::before {
    transform: translateX(100%);
}

.btn-primary {
    background: var(--accent);
    color: var(--bg);
    box-shadow: 0 8px 32px var(--accent-glow);
}

.btn-primary:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 16px 48px rgba(124, 124, 255, 0.5);
}

.btn-secondary {
    color: var(--accent);
    border: 2px solid var(--accent);
    background: rgba(124, 124, 255, 0.05);
}

.btn-secondary:hover {
    background: rgba(124, 124, 255, 0.15);
    transform: translateY(-4px);
}

.scroll-indicator {
    position: absolute;
    bottom: clamp(30px, 5vw, 50px);
    left: 50%;
    transform: translateX(-50%);
    color: var(--muted);
    font-size: clamp(12px, 1.5vw, 14px);
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-10px); }
}

/* SECTION COMMON */
section {
    position: relative;
    z-index: 1;
    padding: clamp(60px, 10vw, 120px) clamp(20px, 5vw, 80px);
}

.section-title {
    font-size: clamp(32px, 5vw, 48px);
    font-weight: 800;
    margin-bottom: clamp(20px, 4vw, 40px);
    text-align: center;
    letter-spacing: -1px;
}

/* MANIFESTO */
.manifesto {
    background: linear-gradient(180deg, var(--bg), var(--bg-secondary));
}

.manifesto-content {
    max-width: 900px;
    margin: 0 auto;
}

.manifesto p {
    font-size: clamp(16px, 2vw, 20px);
    line-height: 1.9;
    color: var(--muted);
    margin-bottom: clamp(24px, 4vw, 36px);
    text-align: justify;
}

/* HIGHLIGHT CARDS */
.highlight {
    background: var(--bg-secondary);
}

.highlight-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr));
    gap: clamp(24px, 4vw, 40px);
    max-width: 1400px;
    margin: 0 auto;
}

.highlight-card {
    padding: clamp(30px, 5vw, 50px);
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(124, 124, 255, 0.15);
    border-radius: 24px;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.highlight-card:hover {
    transform: translateY(-12px) scale(1.02);
    background: rgba(124, 124, 255, 0.08);
    border-color: var(--accent);
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5), 0 0 80px var(--accent-glow);
}

.highlight-card h3 {
    font-size: clamp(20px, 3vw, 26px);
    margin-bottom: clamp(12px, 2vw, 16px);
    color: var(--accent);
}

.highlight-card p {
    color: var(--muted);
    font-size: clamp(14px, 1.8vw, 16px);
    line-height: 1.7;
}

/* ARTICLE GRID */
.article-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(280px, 100%), 1fr));
    gap: clamp(30px, 5vw, 50px);
    max-width: 1400px;
    margin: 0 auto;
}

.article-card {
    padding: clamp(24px, 4vw, 36px);
    border-left: 4px solid var(--accent);
    background: rgba(255, 255, 255, 0.02);
    border-radius: 0 12px 12px 0;
    transition: all 0.4s;
}

.article-card:hover {
    background: rgba(124, 124, 255, 0.08);
    transform: translateX(12px);
    box-shadow: -8px 0 24px var(--accent-glow);
}

.article-card h4 {
    font-size: clamp(18px, 2.5vw, 22px);
    margin-bottom: clamp(12px, 2vw, 16px);
    color: var(--fg);
}

.article-card p {
    color: var(--muted);
    font-size: clamp(14px, 1.8vw, 16px);
    line-height: 1.7;
}

/* PRESTIGE STATS */
.prestige {
    background: var(--bg-secondary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(220px, 100%), 1fr));
    gap: clamp(30px, 5vw, 60px);
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}

.stat h3 {
    font-size: clamp(48px, 8vw, 72px);
    font-weight: 900;
    color: var(--accent);
    margin-bottom: clamp(8px, 1.5vw, 12px);
    text-shadow: 0 0 40px var(--accent-glow);
}

.stat span {
    font-size: clamp(14px, 2vw, 18px);
    color: var(--muted);
    font-weight: 500;
}

/* CAMPUS GALLERY */
.campus-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr));
    gap: clamp(24px, 4vw, 40px);
    max-width: 1400px;
    margin: clamp(40px, 6vw, 60px) auto 0;
}

.campus-img {
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    aspect-ratio: 4/3;
    cursor: pointer;
    transition: all 0.5s;
}

.campus-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.campus-img:hover {
    transform: translateY(-8px);
    box-shadow: 0 32px 80px rgba(124, 124, 255, 0.3);
}

.campus-img:hover img {
    transform: scale(1.15) rotate(2deg);
}

/* FOOTER */
footer {
    padding: clamp(40px, 6vw, 60px);
    text-align: center;
    color: var(--muted);
    font-size: clamp(13px, 1.8vw, 15px);
    border-top: 1px solid rgba(124, 124, 255, 0.1);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .nav {
        padding: 16px 20px;
    }
    
    .hero {
        padding: 100px 20px 60px;
    }
    
    .hero-btn {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn {
        justify-content: center;
    }
}

/* SCROLL REVEAL */
.reveal {
    opacity: 0;
    transform: translateY(60px);
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.reveal.active {
    opacity: 1;
    transform: translateY(0);
}
</style>
</head>
<body>

<div class="bg-animation"></div>

<!-- NAVIGATION -->
<header class="nav">
    <div class="brand">
        <img src="{{ asset('storage/aether/logo.png') }}" class="logo" alt="Aetheris Logo">
        <span class="brand-text">Aetheris Academic</span>
    </div>
    <div class="nav-action">
        <a href="/login" class="nav-link">Login</a>
        <a href="/register" class="nav-link cta">Register</a>
    </div>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg" style="background-image:url('{{ asset('storage/aether/hero-campus.jpg') }}'); background-size: cover; background-position: center;"></div>
    <div class="hero-content">
        <h1>Where Academic<br>Civilization Evolves</h1>
        <p class="hero-subtitle">
            Aetheris Academic adalah sistem peradaban digital akademik
            yang dirancang untuk institusi pendidikan
            dengan visi jangka panjang dan integritas tinggi.
        </p>
        <div class="hero-btn">
            <a href="/login" class="btn btn-primary">
                <span>Enter System</span>
                <span>→</span>
            </a>
            <a href="#manifesto" class="btn btn-secondary">Read Manifesto</a>
        </div>
    </div>
    <div class="scroll-indicator">↓ Scroll</div>
</section>

<!-- MANIFESTO -->
<section id="manifesto" class="manifesto reveal">
    <h2 class="section-title">Academic Manifesto</h2>
    <div class="manifesto-content">
        <p>
            Pendidikan tinggi bukan sekadar proses administratif.
            Ia adalah ruang pembentukan karakter intelektual,
            disiplin berpikir, dan warisan pengetahuan lintas generasi.
        </p>
        <p>
            Aetheris Academic lahir sebagai respons terhadap
            kebutuhan akan sistem akademik yang tidak hanya
            fungsional, tetapi juga bermartabat.
        </p>
        <p>
            Setiap interaksi di dalam sistem ini dirancang
            dengan prinsip ketertiban, kejelasan, dan keberlanjutan —
            agar institusi tidak hanya berjalan,
            tetapi berkembang dengan identitas yang kuat.
        </p>
    </div>
</section>

<!-- HIGHLIGHT -->
<section class="highlight reveal">
    <div class="highlight-grid">
        <div class="highlight-card">
            <h3>Smart Academic Control</h3>
            <p>Manajemen akademik presisi tinggi berbasis sistem terintegrasi dengan teknologi terkini</p>
        </div>
        <div class="highlight-card">
            <h3>Elite Digital Campus</h3>
            <p>Identitas kampus modern dengan infrastruktur digital berkelas dunia</p>
        </div>
        <div class="highlight-card">
            <h3>Next-Gen Education</h3>
            <p>Mendukung pembelajaran berkelanjutan dan masa depan akademik yang cemerlang</p>
        </div>
    </div>
</section>

<!-- ARTICLE -->
<section class="article-extended reveal">
    <h2 class="section-title">Academic Insight</h2>
    <div class="article-grid">
        <article class="article-card">
            <h4>Transformasi Digital Akademik</h4>
            <p>
                Aetheris Academic mengintegrasikan seluruh proses akademik
                ke dalam satu ekosistem digital yang efisien, aman,
                dan terstruktur dengan sempurna.
            </p>
        </article>
        <article class="article-card">
            <h4>Integritas & Ketertiban Sistem</h4>
            <p>
                Setiap data, setiap proses, dan setiap keputusan
                dicatat secara sistematis untuk menjaga
                kredibilitas institusi pendidikan.
            </p>
        </article>
        <article class="article-card">
            <h4>Pengalaman Sivitas Akademika</h4>
            <p>
                Antarmuka dirancang bukan hanya untuk fungsi,
                tetapi juga kenyamanan dan kebanggaan pengguna
                dalam setiap interaksi.
            </p>
        </article>
    </div>
</section>

<!-- PRESTIGE -->
<section class="prestige reveal">
    <h2 class="section-title">Excellence in Numbers</h2>
    <div class="stats-grid">
        <div class="stat">
            <h3>99.9%</h3>
            <span>Data Integrity</span>
        </div>
        <div class="stat">
            <h3>24/7</h3>
            <span>Academic Access</span>
        </div>
        <div class="stat">
            <h3>∞</h3>
            <span>Scalable Architecture</span>
        </div>
    </div>
</section>

<!-- CAMPUS -->
<section class="campus reveal">
    <h2 class="section-title">Campus Presence</h2>
    <p style="text-align: center; color: var(--muted); max-width: 700px; margin: 0 auto 40px; font-size: clamp(16px, 2vw, 18px);">
        Lingkungan akademik yang merepresentasikan stabilitas, visi, dan kematangan institusi
    </p>
    <div class="campus-gallery">
        <div class="campus-img">
            <img src="{{ asset('storage/aether/campus-1.jpg') }}" alt="Campus 1">
        </div>
        <div class="campus-img">
            <img src="{{ asset('storage/aether/campus-2.jpg') }}" alt="Campus 2">
        </div>
        <div class="campus-img">
            <img src="{{ asset('storage/aether/campus-3.jpg') }}" alt="Campus 3">
        </div>
    </div>
</section>

<footer>
    © 2026 Aetheris Academic — Digital Academic Civilization
</footer>

<script>
// Scroll nav effect
window.addEventListener('scroll', () => {
    const nav = document.querySelector('.nav');
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});

// Scroll reveal
const revealElements = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, { threshold: 0.15 });

revealElements.forEach(el => revealObserver.observe(el));

// Magnetic button effect
document.querySelectorAll('.btn, .nav-link.cta').forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px) scale(1.05)`;
    });
    
    btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'translate(0, 0) scale(1)';
    });
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Parallax effect on hero
document.addEventListener('mousemove', (e) => {
    const bg = document.querySelector('.hero-bg');
    const x = (e.clientX / window.innerWidth - 0.5) * 30;
    const y = (e.clientY / window.innerHeight - 0.5) * 30;
    bg.style.transform = `translate(${x}px, ${y}px)`;
});

// 3D tilt on campus images
document.querySelectorAll('.campus-img').forEach(img => {
    img.addEventListener('mousemove', (e) => {
        const rect = img.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        img.style.transform = `perspective(1000px) rotateX(${-y * 10}deg) rotateY(${x * 10}deg) translateY(-8px)`;
    });
    
    img.addEventListener('mouseleave', () => {
        img.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
    });
});
</script>

</body>
</html>