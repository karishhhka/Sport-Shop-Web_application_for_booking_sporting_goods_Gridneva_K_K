const slides = document.querySelector('.slides');
const images = document.querySelectorAll('.slides img');
const nextButton = document.getElementById('next');
const prevButton = document.getElementById('prev');
let currentIndex = 0;

function updateSlider() {
    slides.style.transform = `translateX(-${currentIndex * 1920}px)`; 
}
updateSlider();

nextButton.addEventListener('click', () => {
    if (currentIndex < images.length - 1) {
        currentIndex++;
    } else {
        currentIndex = 0; 
    }
    updateSlider();
});

prevButton.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
    } else {
        currentIndex = images.length - 1; 
    }
    updateSlider();
});

setInterval(() => {
    if (currentIndex < images.length - 1) {
        currentIndex++;
    } else {
        currentIndex = 0; 
    }
    updateSlider();
}, 6000);

