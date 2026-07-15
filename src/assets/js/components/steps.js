import { ScrollTrigger } from "gsap/all";
import gsap from "gsap";

gsap.registerPlugin(ScrollTrigger);

class Steps {
  constructor(el) {
    this.el = el;
    this.wrapper = document.querySelector("[data-animation-container]");

    gsap.to(this.el, {
      y: "(-100%+1lh)",
      scrollTrigger: {
        trigger: this.wrapper,
        start: "top center",
        end: "bottom center",
        toggleActions: "play none none reverse",
        scrub: true,
      },
    });
  }
}

[].forEach.call(document.querySelectorAll("[data-animation='steps']"), (el) => {
  new Steps(el);
});
