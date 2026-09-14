import Swiper from 'swiper/bundle';

export class SliderManager {
    
    constructor(el){
        this.el = el;
        this.next = el.querySelector('[data-swiper-next]')
        this.prev = el.querySelector('[data-swiper-prev]')
        this.pagination = el.querySelector('[data-swiper-pagination]')
        this.slider;

        console.log(this.pagination);
    }

    initSlider(){
        this.slider = new Swiper(this.el, {
            slidesPerView: 1,
            spaceBetween: 0,

            navigation: {
                nextEl: this.next,
                prevEl: this.prev,
            },

            pagination: {
                el: this.pagination,
                type: 'bullets',
            },
            
        });
    }
}