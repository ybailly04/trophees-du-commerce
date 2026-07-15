document.querySelectorAll('svg.bim-icon').forEach((el) => {
	const iconName = el.getAttribute('data-icon');
	const iconPath = iconsSvg + '#' + iconName;

	use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
	use.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', iconPath);
	el.appendChild(use);
});
