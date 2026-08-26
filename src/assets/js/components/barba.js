import gsap from "gsap";
import { CustomEase } from "gsap/all";
import barba from '@barba/core';

import { HeaderManager } from "./header";
import { initSocialShare } from "./share.js";
import { initSectionAnchorDock, syncSectionAnchorDockActiveState } from "./nav.js";
import { initAccordionCSS } from "./accordion.js";
import { initVoteButtons } from "./vote.js";
import { initGallery } from "./gallery.js";
import { bfi_init } from '../../../../node_modules/better-file-input/dist/bfi.js';
import { CursorManager } from "./cursor.js";
import { SliderManager } from "./sliders.js";

// -----------------------------------------
// OSMO PAGE TRANSITION BOILERPLATE
// -----------------------------------------

gsap.registerPlugin(CustomEase);

history.scrollRestoration = "manual";

let lenis = null;
let nextPage = document;
let onceFunctionsInitialized = false;

const hasLenis = typeof window.Lenis !== "undefined";
const hasScrollTrigger = typeof window.ScrollTrigger !== "undefined";

const rmMQ = window.matchMedia("(prefers-reduced-motion: reduce)");
let reducedMotion = rmMQ.matches;
rmMQ.addEventListener?.("change", e => (reducedMotion = e.matches));
rmMQ.addListener?.(e => (reducedMotion = e.matches));

const has = (s) => !!nextPage.querySelector(s);

let staggerDefault = 0.05;
let durationDefault = 0.6;

CustomEase.create("osmo", "0.625, 0.05, 0, 1");
gsap.defaults({ ease: "osmo", duration: durationDefault });

// -----------------------------------------
// CUSTOM CLASSES
// -----------------------------------------

let header = document.querySelector('.header');
let headerManager = new HeaderManager(header);
let cursorManager = new CursorManager();

// -----------------------------------------
// FUNCTION REGISTRY
// -----------------------------------------

function initOnceFunctions() {
  initLenis();
  initLenisControls(lenis);
  if (onceFunctionsInitialized) return;
  onceFunctionsInitialized = true;
  
  // Runs once on first load
  initSocialShare();
  initSectionAnchorDock();
  if(has('.bfi')) bfi_init();
  if(has('[data-accordion-css-init]')) initAccordionCSS();
  if(has('[data-vote-button]')) initVoteButtons();
  if(has('[data-gallery-thumb]')) initGallery();
  if(has('[data-swiper]')){
    let sliderManager = new SliderManager(document.querySelector('[data-swiper]'));
    sliderManager.initSlider();
  }
}

function initBeforeEnterFunctions(next) {
  nextPage = next || document;
  
  headerManager.menuClose();
  // Runs before the enter animation
  // if (has('[data-something]')) initSomething();
}

function initAfterEnterFunctions(next) {
  nextPage = next || document;
  
  // Runs after enter animation completes
  // if (has('[data-something]')) initSomething();
  
  headerManager.initBurgers();
  initSocialShare();
  initSectionAnchorDock();
  if(has('.bfi')) bfi_init();
  if(has('[data-accordion-css-init]')) initAccordionCSS();
  if(has('[data-vote-button]')) initVoteButtons();
  if(has('[data-gallery-thumb]')) initGallery();
  if(has('[data-swiper]')){
    let sliderManager = new SliderManager(document.querySelector('[data-swiper]'));
    sliderManager.initSlider();
  }

  if(cursorManager){
    cursorManager.C.removeText();
  }

  if(hasLenis){
    lenis.resize();
  }
  
  if (hasScrollTrigger) {
    ScrollTrigger.refresh();
  }
}



// -----------------------------------------
// PAGE TRANSITIONS
// -----------------------------------------

function runPageOnceAnimation(next) {
  const tl = gsap.timeline();

  tl.call(() => {
    resetPage(next)
  }, null, 0);

  return tl;
}

function runPageLeaveAnimation(current, next) {
  const tl = gsap.timeline({
    onComplete: () => { current.remove() }
  });
  
  if (reducedMotion) {
    // Immediate swap behavior if user prefers reduced motion
    return tl.set(current, { autoAlpha: 0 });
  }

  tl.to(current, { autoAlpha: 0, duration: 0.4 });

  return tl;
}

function runPageEnterAnimation(next){
  const tl = gsap.timeline();
  
  if (reducedMotion) {
    // Immediate swap behavior if user prefers reduced motion
    tl.set(next, { autoAlpha: 1 });
    tl.add("pageReady")
    tl.call(resetPage, [next], "pageReady");
    return new Promise(resolve => tl.call(resolve, null, "pageReady"));
  }
  
  tl.add("startEnter", 0.6);
  
  tl.fromTo(next, {
    autoAlpha: 0,
  },{
    autoAlpha: 1,
  }, "startEnter");

  tl.add("pageReady");
  tl.call(resetPage, [next], "pageReady");

  return new Promise(resolve => {
    tl.call(resolve, null, "pageReady");
  });
}


// -----------------------------------------
// BARBA HOOKS + INIT
// -----------------------------------------

barba.hooks.beforeEnter(data => {
  // Position new container on top
  gsap.set(data.next.container, {
    position: "fixed",
    top: 0,
    left: 0,
    right: 0,
  });
  
  if (lenis && typeof lenis.stop === "function") {
    lenis.stop();
  }
  
  initBeforeEnterFunctions(data.next.container);
  applyThemeFrom(data.next.container);
});

barba.hooks.afterLeave(() => {
  if(hasScrollTrigger){
    ScrollTrigger.getAll().forEach(trigger => trigger.kill());
  }
});

barba.hooks.enter(data => {
  initBarbaNavUpdate(data);
  syncSectionAnchorDockActiveState();
})

barba.hooks.afterEnter(data => {
  // Run page functions
  initAfterEnterFunctions(data.next.container);
  
  // Settle
  if(hasLenis){
    lenis.resize();
    lenis.start();    
  }
  
  if(hasScrollTrigger){
    ScrollTrigger.refresh(); 
  }
});

barba.init({
  debug: false, // Set to 'false' in production
  timeout: 7000,
  preventRunning: true,
  transitions: [
    {
      name: "splitLeave",
      sync: true,
      from: {
        namespace: [
          'candidate'
        ]
      },

      // First load
      async once(data) {
        initOnceFunctions();

        return runPageOnceAnimation(data.next.container);
      },
      // Current page leaves
      async leave(data) {
        return runCandidateLeaveAnimation(data.current.container, data.next.container);
      },

      // New page enters
      async enter(data) {
        return runCandidateEnterAnimation(data.next.container);
      }
    },{
      name: "splitEnter",
      sync: true,
      to: {
        namespace: [
          'candidate'
        ]
      },

      // First load
      async once(data) {
        initOnceFunctions();

        return runPageOnceAnimation(data.next.container);
      },
      // Current page leaves
      async leave(data) {
        return runCandidateLeaveAnimation(data.current.container, data.next.container);
      },

      // New page enters
      async enter(data) {
        return runCandidateEnterAnimation(data.next.container);
      }
    },{
      name: "default",
      sync: true,
      
      // First load
      async once(data) {
        initOnceFunctions();

        return runPageOnceAnimation(data.next.container);
      },

      // Current page leaves
      async leave(data) {
        return runPageLeaveAnimation(data.current.container, data.next.container);
      },

      // New page enters
      async enter(data) {
        return runPageEnterAnimation(data.next.container);
      }
    },
  ],
  views: [{
    namespace: 'home',
    beforeLeave(data) {
        gsap.to(header, {
            opacity: 1
        })
    },
    beforeEnter(date){
        gsap.to(header, {
            opacity: 0
        })
    }
  },{
    namespace: 'default-header',
    beforeLeave(data) {
        header.classList.remove('white');
    },
    beforeEnter(date){
        header.classList.add('white');
    }
  },{
    namespace: 'categories',
    beforeLeave(data) {
      gsap.to('.section-dock',{
        autoAlpha: 0,
      })
    },
    beforeEnter(date){
      gsap.to('.section-dock',{
        autoAlpha: 1,
      })
    }
  }]
});



// -----------------------------------------
// GENERIC + HELPERS
// -----------------------------------------

const themeConfig = {
  light: {
    nav: "dark",
    transition: "light"
  },
  dark: {
    nav: "light",
    transition: "dark"
  }
};

function applyThemeFrom(container) {
  const pageTheme = container?.dataset?.pageTheme || "light";
  const config = themeConfig[pageTheme] || themeConfig.light;
  
  document.body.dataset.pageTheme = pageTheme;
  const transitionEl = document.querySelector('[data-theme-transition]');
  if (transitionEl) {
    transitionEl.dataset.themeTransition = config.transition;
  }

  const nav = document.querySelector('[data-theme-nav]');
  if (nav) {
    nav.dataset.themeNav = config.nav;
  }
}

function initLenis() {
  if (lenis) return; // already created
  if (!hasLenis) return;

  lenis = new Lenis({
    lerp: 0.165,
    wheelMultiplier: 1.25,
  });

  if (hasScrollTrigger) {
    lenis.on("scroll", ScrollTrigger.update);
  }

  gsap.ticker.add((time) => {
    lenis.raf(time * 1000);
  });

  gsap.ticker.lagSmoothing(0);
}

function resetPage(container){
  window.scrollTo(0, 0);
  gsap.set(container, { clearProps: "position,top,left,right" });
  
  if(hasLenis){
    lenis.resize();
    lenis.start();    
  }
}

function debounceOnWidthChange(fn, ms) {
  let last = innerWidth,
    timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => {
      if (innerWidth !== last) {
        last = innerWidth;
        fn.apply(this, args);
      }
    }, ms);
  };
}

function initBarbaNavUpdate(data) {
  var tpl = document.createElement('template');
  tpl.innerHTML = data.next.html.trim();
  var nextNodes = tpl.content.querySelectorAll('[data-barba-update]');
  var currentNodes = document.querySelectorAll('nav [data-barba-update]');

  currentNodes.forEach(function (curr, index) {
    var next = nextNodes[index];
    if (!next) return;

    // Aria-current sync
    var newStatus = next.getAttribute('aria-current');
    if (newStatus !== null) {
      curr.setAttribute('aria-current', newStatus);
    } else {
      curr.removeAttribute('aria-current');
    }

    // Class list sync
    var newClassList = next.getAttribute('class') || '';
    curr.setAttribute('class', newClassList);

    // Active state sync (e.g. section-dock category link)
    curr.toggleAttribute('data-active', next.hasAttribute('data-active'));
  });
}



// -----------------------------------------
// YOUR FUNCTIONS GO BELOW HERE
// -----------------------------------------

//Control scroll methods
function initLenisControls(lenis){
    let stop = document.querySelectorAll('[data-lenis-stop]');
    let resume = document.querySelectorAll('[data-lenis-resume]');
    let toggle = document.querySelectorAll('[data-lenis-toggle]');

    if(stop){
        stop.forEach(element => {
            if (lenis && typeof lenis.stop === "function") {
                element.addEventListener('click', () => lenis.stop());
            }
        });
    }

    if(resume){
        resume.forEach(element => {
            if (lenis && typeof lenis.resume === "function") {
                element.addEventListener('click', () => lenis.resume());
            }
        });
    }

    if(toggle){
        toggle.forEach(element => {
            element.addEventListener('click', () => {
                const currentState = target.getAttribute("data-lenis-toggle");
                if (currentState === "stop") {
                    lenis.stop();
                    target.setAttribute("data-lenis-toggle", "resume");
                } else {
                    lenis.start();
                    target.setAttribute("data-lenis-toggle", "stop");
                }
            });
        });
    }
}

//Custom transition
function runCandidateLeaveAnimation(current, next) {
  const transitionWrap = document.querySelector("[data-transition-wrap]");
  const transitionColumns = transitionWrap.querySelectorAll("[data-transition-column]");
  
  const tl = gsap.timeline({
    onComplete: () => { current.remove() }
  });
  
  if (reducedMotion) {
    // Immediate swap behavior if user prefers reduced motion
    return tl.set(current, { autoAlpha: 0 });
  }
  
  tl.set(next, {
    autoAlpha: 0,
  }, 0);
  
  tl.fromTo(transitionColumns, {
    yPercent: 0
  },{
    yPercent: function(index){ return index == 0 ? 100 : -100 },
    duration: 0.6,
  }, 0);

  return tl;
}

function runCandidateEnterAnimation(next){
  const transitionWrap = document.querySelector("[data-transition-wrap]");
  const transitionColumns = transitionWrap.querySelectorAll("[data-transition-column]");
  
  const tl = gsap.timeline();
  
  if (reducedMotion) {
    // Immediate swap behavior if user prefers reduced motion
    tl.set(next, { autoAlpha: 1 });
    tl.add("pageReady")
    tl.call(resetPage, [next], "pageReady");
    return new Promise(resolve => tl.call(resolve, null, "pageReady"));
  }
  
  tl.add("startEnter", 1);
  
  tl.set(next, {
    autoAlpha: 1,
  }, "startEnter");
  
  tl.to(transitionColumns, {
    yPercent: function(index){ return index == 0 ? 200 : -200 },
    duration: 0.6,
    overwrite: "auto",
  }, "startEnter");

  tl.add("pageReady");
  tl.call(resetPage, [next], "pageReady");

  return new Promise(resolve => {
    tl.call(resolve, null, "pageReady");
  });
}
