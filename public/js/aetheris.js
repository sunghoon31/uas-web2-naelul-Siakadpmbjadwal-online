// PARALLAX HERO
document.addEventListener('mousemove', e => {
    const bg = document.querySelector('.hero-bg');
    const x = (e.clientX / window.innerWidth - 0.5) * 10;
    const y = (e.clientY / window.innerHeight - 0.5) * 10;
    bg.style.transform = `scale(1.1) translate(${x}px,${y}px)`;
});

// SCROLL REVEAL
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.style.opacity = 1;
            e.target.style.transform = 'none';
        }
    });
},{ threshold: .15 });

document.querySelectorAll('section').forEach(sec => {
    sec.style.opacity = 0;
    sec.style.transform = 'translateY(60px)';
    sec.style.transition = '1s';
    observer.observe(sec);
});
// MAGNETIC BUTTON
document.querySelectorAll('.primary, .cta').forEach(btn => {
    btn.addEventListener('mousemove', e => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width/2;
        const y = e.clientY - rect.top - rect.height/2;
        btn.style.transform = `translate(${x/6}px,${y/6}px) scale(1.1)`;
    });
    btn.addEventListener('mouseleave', () => {
        btn.style.transform = 'translate(0,0) scale(1)';
    });
});

// IMAGE TILT
document.querySelectorAll('.campus-gallery img').forEach(img => {
    img.addEventListener('mousemove', e => {
        const r = img.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - .5;
        const y = (e.clientY - r.top) / r.height - .5;
        img.style.transform =
            `perspective(900px) rotateX(${-y*10}deg) rotateY(${x*10}deg) scale(1.08)`;
    });
    img.addEventListener('mouseleave', () => {
        img.style.transform = 'none';
    });
});
const reveal = new IntersectionObserver(entries=>{
    entries.forEach(e=>{
        if(e.isIntersecting){
            e.target.classList.add('show');
        }
    });
},{threshold:.15});

document.querySelectorAll('.reveal').forEach(el=>reveal.observe(el));
