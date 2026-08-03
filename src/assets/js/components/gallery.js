import { gsap } from 'gsap';

export function initGallery() {
    var i = 0;
    var activeSlide;
    document.querySelectorAll("[data-gallery-thumb]").forEach((button) => {
        button.addEventListener("click", () => {
            if(button !== activeSlide){
                let index = button.dataset.index;
    
                let slide = document.querySelector("[data-gallery-index='"+ index +"']");
                slide.style.zIndex = i;
                i++;
    
                const tl = gsap.timeline({});
                tl.fromTo(slide,{
                    duration: 0,
                    clipPath: "inset(100% 0%)",
                },{
                    duration: 1,
                    clipPath: "inset(0% 0%)",
                    ease: "power3.out",
                })

                activeSlide = button;
            }
        });
    });
}