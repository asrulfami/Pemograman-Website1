document.addEventListener("DOMContentLoaded", function() {
    let menuIcon = document.querySelector('#menu-icon');
    let navbar = document.querySelector('.navbar');
    let sections = document.querySelectorAll('section');
    let navLinks = document.querySelectorAll('header nav a');
    let images = document.querySelectorAll('.project-info img'); // Tambahkan ini untuk memilih semua gambar

    window.onscroll = () => {
        sections.forEach(sec => {
            let top = window.scrollY;
            let offset = sec.offsetTop - 150;
            let height = sec.offsetHeight;
            let id = sec.getAttribute('id');

            if (top >= offset && top < offset + height) {
                navLinks.forEach(links => {
                    links.classList.remove('active');
                    document.querySelector('header nav a[href*=' + id + ']').classList.add('active'); // Perbaikan sintaks
                })
                
            }
        })
    }

    menuIcon.onclick = () => {
        menuIcon.classList.toggle('bx-x');
        navbar.classList.toggle('active');
    }

    // Tambahkan event listener untuk setiap gambar
    images.forEach(img => {
        img.addEventListener("click", function() {
            openFullscreen(img.src);
        });
    });

    function openFullscreen(imageSrc) {
        const fullscreenContainer = document.createElement("div");
        fullscreenContainer.classList.add("fullscreen-container");

        const fullscreenImage = document.createElement("img");
        fullscreenImage.src = imageSrc;
        fullscreenImage.classList.add("fullscreen-image");

        fullscreenContainer.appendChild(fullscreenImage);
        document.body.appendChild(fullscreenContainer);

        fullscreenContainer.addEventListener("click", closeFullscreen);
    }

    function closeFullscreen() {
        this.remove();
    }
});
