import { ScrollTrigger } from "gsap/all";
import { SplitText } from "gsap/all";
import gsap from "gsap";

gsap.registerPlugin(ScrollTrigger, SplitText);

class Appear {
  constructor(el) {
    this.el = el;
    this.direction = this.el.dataset.appear;

    if (!ScrollTrigger.isInViewport(this.el)) {
      if (this.el && this.direction == "right") {
        this.appearFromRight(this.el);
      }

      if (this.el && this.direction == "left") {
        this.appearFromLeft(this.el);
      }

      if (this.el && this.direction == "both") {
        this.appearSymmetry(this.el);
      }

      if (this.el && this.direction == "bottom") {
        this.appearBottom(this.el);
      }

      if (this.el && this.direction == "top") {
        this.appearTop(this.el);
      }

      if (this.el && this.direction == "text") {
        this.appearText(this.el);
      }

      if (this.el && this.direction == "gallery") {
        this.appearGallery(this.el);
      }
    }

    if (this.el && this.direction == "title") {
      this.appearTitle(this.el);
    }
    if (this.el && this.direction == "defil") {
      this.initDefil(this.el);
    }
  }

  appearFromRight(el) {
    gsap.from(el.children, {
      opacity: 0,
      x: 80,
      stagger: 0.2,
      scrollTrigger: {
        trigger: el,
        start: "top 70%",
      },
    });
  }

  appearFromLeft(el) {
    gsap.from(el.children, {
      opacity: 0,
      x: -80,
      stagger: 0.2,
      scrollTrigger: {
        trigger: el,
        start: "top 70%",
      },
    });
  }

  appearSymmetry(el) {
    let transform = 80;
    if (el.classList.contains("right")) {
      transform = -transform;
    }

    gsap.from(el.firstElementChild, {
      opacity: 0,
      x: -transform,
      stagger: 0.2,
      scrollTrigger: {
        trigger: el,
        start: "top 70%",
      },
    });
    gsap.from(el.lastElementChild, {
      opacity: 0,
      x: transform,
      stagger: 0.2,
      scrollTrigger: {
        trigger: el,
        start: "top 70%",
      },
    });
  }

  appearBottom(el) {
    gsap.from(el.children, {
      opacity: 0,
      y: -80,
      stagger: 0.2,
      scrollTrigger: {
        trigger: el,
        start: "top 70%",
      },
    });
  }

  appearTop(el) {
    gsap.from(el.children, {
      opacity: 0,
      stagger: 0.2,
      scrollTrigger: {
        trigger: el,
        start: "top 70%",
      },
    });
  }

  appearText(el) {
    let split = new SplitText(el, { type: "words,chars" });

    gsap.from(split.chars, {
      duration: 0.8,
      opacity: 0.4,
      ease: "back",
      stagger: 0.1,
      scrollTrigger: {
        trigger: el,
        start: "top 60%",
        end: "top 20%",
        scrub: true,
      },
    });
  }

  appearTitle(el) {
    let split = new SplitText(el, { type: "lines, words", mask:"lines", wordsClass: "word" });
    let delay = el.dataset.delay ?? "";

    if (delay != "") {
      gsap.from(split.lines, {
        yPercent: 100,
        ease: "power3.inOut",
        duration: 1.2,
        delay: delay,
        stagger: .1
      })
    } else {
      gsap.from(split.lines, {
        yPercent: 100,
        ease: "power3.inOut",
        duration: 1.2,
        stagger: .1,
        scrollTrigger: {
          trigger: el,
          start: "top 75%",
        }
      })
    }
  }

  appearGallery(el) {
    for (let image of el.children) {
      gsap.from(image, {
        scale: 0,
        opacity: 0,
        duration: 1,
        ease: "power4",
        scrollTrigger: {
          trigger: image,
          start: "top 85%",
        },
      });
    }
  }

  initDefil(el) {
    gsap.to(el, {
      ease: "none",
      x: -(el.offsetWidth - window.innerWidth),
      scrollTrigger: {
        trigger: el.parentElement,
        start: "top bottom",
        end: "bottom top",
        scrub: true,
        toggleActions: "play none none reverse",
      },
    });
  }
}

[].forEach.call(document.querySelectorAll("[data-appear]"), (el) => {
  new Appear(el);
});
