const html = document.querySelector('html');

class HeaderManager {
	
	constructor() {
		this.buttons = document.querySelectorAll('[data-menu-button]');

		if(this.buttons){
			this.buttons.forEach(element => {
				element.addEventListener('click', () => this.handleMenu(element));
			})
		}
	}

	// Toggle Animation
  	handleMenu(target){
		const currentState = target.getAttribute("data-menu-button");
		if (currentState === "burger") {
			this.menuOpen()
			//target.setAttribute("data-menu-button", "close");
		} else {
			this.menuClose()
			//target.setAttribute("data-menu-button", "burger");
		}
  	}

	menuOpen(){
		html.classList.add('__menu-active')
	}

	menuClose(){
		html.classList.remove('__menu-active')
	}
}

[].forEach.call(document.querySelectorAll('.header'), (el) => {
    new HeaderManager(el);
});