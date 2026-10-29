/**
 * Rodapé — as letras do wordmark "ENTRE RIOS" sobem de baixo quando o rodapé
 * entra na tela (Figma: "Wordmark (reveal)"). Sem JS ou com
 * prefers-reduced-motion as letras já aparecem no lugar.
 */
(function () {
	'use strict';

	var rodape = document.querySelector('[data-er-footer]');

	if (!rodape || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	var marca = rodape.querySelector('.footer-er__wordmark');

	if (!marca) {
		return;
	}

	rodape.classList.add('is-revelavel');

	var observador = new IntersectionObserver(function (entradas) {
		entradas.forEach(function (entrada) {
			if (entrada.isIntersecting) {
				rodape.classList.add('is-revelado');
				observador.disconnect();
			}
		});
	}, { threshold: 0.35 });

	observador.observe(marca);
})();
