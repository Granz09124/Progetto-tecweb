window.addEventListener('scroll', function() {
    const tornaSu = document.getElementById('torna-su');
    if (window.scrollY > 300) {
        tornaSu.classList.add('visible');
    } else {
        tornaSu.classList.remove('visible');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}