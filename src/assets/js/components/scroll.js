import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/all';

gsap.registerPlugin(ScrollTrigger);


export class ScrollManager {
	
	constructor() {
		// Initialize a new Lenis instance for smooth scrolling
		const lenis = new Lenis();

		// Synchronize Lenis scrolling with GSAP's ScrollTrigger plugin
		lenis.on('scroll', ScrollTrigger.update);

		// Add Lenis's requestAnimationFrame (raf) method to GSAP's ticker
		// This ensures Lenis's smooth scroll animation updates on each GSAP tick
		gsap.ticker.add((time) => {
			lenis.raf(time * 1000); // Convert time from seconds to milliseconds
		});

		// Disable lag smoothing in GSAP to prevent any delay in scroll animations
		gsap.ticker.lagSmoothing(0);


		//Control scroll methods
		this.stop = document.querySelectorAll('[data-lenis-stop]');
		this.resume = document.querySelectorAll('[data-lenis-resume]');
		this.toggle = document.querySelectorAll('[data-lenis-toggle]');

		if(this.stop){
			this.stop.forEach(element => {
				element.addEventListener('click', () => lenis.stop());
			});
		}
		
		if(this.resume){
			this.resume.forEach(element => {
				element.addEventListener('click', () => lenis.resume());
			});
		}

		if(this.toggle){
			this.toggle.forEach(element => {
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
}