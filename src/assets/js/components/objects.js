import { ScrollTrigger } from "gsap/all";
import gsap from "gsap";

gsap.registerPlugin(ScrollTrigger);

class Objects {
  constructor(el) {
    this.el = el;
    this.items = this.el.querySelectorAll("[data-object]");
    this.images = this.el.querySelectorAll("[data-index]");
    this.zIndex = 20;

    this.items.forEach((element) => {
      element.addEventListener("mouseenter", (e) => {
        let index = e.target.dataset.object;
        let image = this.el.querySelector("[data-index='" + index + "']");
        let tl = gsap.timeline();

        tl.set(image, {
          zIndex: this.zIndex,
        }).fromTo(
          image,
          {
            clipPath: "inset(100% 100%)",
            duration: 0,
            onComplete: () => (this.zIndex += 5),
          },
          {
            clipPath: "inset(0% 0%)",
          },
        );
      });
    });
  }
}

[].forEach.call(document.querySelectorAll("[data-objects]"), (el) => {
  new Objects(el);
});
