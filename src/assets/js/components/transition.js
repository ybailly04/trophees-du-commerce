import { ScrollTrigger } from "gsap/all";
import gsap from "gsap";

gsap.registerPlugin(ScrollTrigger);

class TransitionManager {
  constructor(el) {
    this.el = el;
    
    gsap.to(this.el, {
        yPercent: 35,
        scrollTrigger: {
            trigger: this.el,
            start: "top bottom",
            end: "bottom top",
            scrub: true,
        }
    });
  }
}

[].forEach.call(document.querySelectorAll("[data-transition]"), (el) => {
  new TransitionManager(el);
});
