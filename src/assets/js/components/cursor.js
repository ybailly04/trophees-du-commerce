import MouseFollower from "mouse-follower";
import gsap from "gsap";

MouseFollower.registerGSAP(gsap);

export class CursorManager{
    constructor(){
        if(window.matchMedia("screen and (min-width: 992px)").matches){
            this.C = new MouseFollower({
                skewing: 2,
                stateDetection: {
                    '-pointer': 'a,button',
                    '-orange' : '.cursor-orange'
                },
            });
        }
    }
}