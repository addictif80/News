document.addEventListener('click', (event) => {
    const button = event.target.closest('.article-carousel__nav');
    if (!button) {
        return;
    }

    const track = button.closest('.article-carousel')?.querySelector('.article-carousel__track');
    if (!track) {
        return;
    }

    const step = track.clientWidth * 0.8;
    const direction = button.classList.contains('article-carousel__nav--prev') ? -1 : 1;

    track.scrollBy({ left: step * direction, behavior: 'smooth' });
});
