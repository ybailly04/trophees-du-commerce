import Swiper from 'swiper/bundle';

class SliderManager {
    
    constructor(el){
        this.next = el.querySelector('.swiper-next')
        this.prev = el.querySelector('.swiper-prev')

        const swiperAuto = new Swiper(el, {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 0,
            effect: 'fade',

            fadeEffect: {
                crossFade: true,
            }, 

            navigation: {
                nextEl: this.next,
                prevEl: this.prev,
            },
        });
    }
}

[].forEach.call(document.querySelectorAll('.__slider-auto'), (el) => {
    new SliderManager(el);
});