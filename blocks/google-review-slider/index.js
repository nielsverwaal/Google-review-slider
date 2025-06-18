import Swiper from 'swiper';
import { Autoplay, Pagination } from 'swiper/modules';

const swiperInstances = new WeakMap();

export default function googleReviewSlider(scope = document) {
    const sliderElements = scope.querySelectorAll('.google-review-slider.swiper');
    if (!sliderElements.length) return;

    sliderElements.forEach((el) => {
        const slides = el.querySelectorAll('.swiper-slide');

        // Fallback: te weinig slides
        if (slides.length < 5) {
            el.classList.remove('swiper');
            el.querySelector('.swiper-wrapper')?.classList.remove('swiper-wrapper');
            slides.forEach((slide) => slide.classList.remove('swiper-slide'));
            el.querySelector('.swiper-pagination')?.remove();

            const existing = swiperInstances.get(el);
            if (existing) {
                existing.destroy(true, true);
                swiperInstances.delete(el);
            }

            return;
        }

        // Probeer paginering-element te vinden
        let paginationEl = el.querySelector('.swiper-pagination') || el.closest('.acf-block-preview')?.querySelector('.swiper-pagination');

        if (!paginationEl) {
            // Observeer paginering tot deze beschikbaar is
            const observer = new MutationObserver(() => {
                paginationEl = el.querySelector('.swiper-pagination') || el.closest('.acf-block-preview')?.querySelector('.swiper-pagination');
                if (paginationEl) {
                    observer.disconnect();
                    googleReviewSlider(scope); // opnieuw proberen
                }
            });

            observer.observe(el, { childList: true, subtree: true });
            return;
        }

        // Als er al een Swiper bestaat: vernietig
        const existing = swiperInstances.get(el);
        if (existing) {
            existing.destroy(true, true);
        }

        const slider = new Swiper(el, {
            modules: [Autoplay, Pagination],
            loop: false,
            preloadImages: true,
            spaceBetween: 16,
            slidesPerView: 1,
            pagination: {
                el: paginationEl,
                clickable: true,
            },
            autoplay: {
                enabled: true,
                delay: 4000,
                disableOnInteraction: false,
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
        });

        // Forceer start bij editor
        setTimeout(() => {
            if (slider?.el?.classList.contains('swiper-initialized')) return;
            slider.init();
            slider.autoplay?.start?.();
        }, 300);

        swiperInstances.set(el, slider);
    });
}
