//import '../../node_modules/swiper/swiper-bundle.js';
import '../../../node_modules/better-file-input/dist/bfi.js';

import "./components/svg-icons.js";
import "./components/sliders.js";
import "./components/animation.js";
import "./components/vote.js";
import "./components/barba.js";
import { ScrollManager } from "./components/scroll.js";
import { CursorManager } from "./components/cursor.js";

window.onload = (e) => {
  document.querySelector("html").classList.add("__loaded");
};

document.querySelector("html").classList.remove("no-js");
