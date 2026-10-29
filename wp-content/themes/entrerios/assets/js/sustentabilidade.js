/**
 * Sustentabilidade — frase final com efeito de digitação (Figma: componente
 * "Frase (digitação)"). Etapa 0 = frase oculta; quando a assinatura entra na
 * tela, cada linha é revelada da esquerda para a direita (linear, ~55 ms por
 * caractere, ver --sus-letras no template) com o cursor acompanhando; no fim o
 * cursor pisca na última linha.
 *
 * Sem JS ou com prefers-reduced-motion, a frase fica inteira (o CSS só esconde
 * as linhas depois que este script marca .is-pronta).
 */
(function () {
	'use strict';

	var frases = document.querySelectorAll('[data-sus-digitacao]');

	if (!frases.length || !('IntersectionObserver' in window)) {
		return;
	}

	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	function digitar(frase) {
		var linhas = frase.querySelectorAll('.sus-digitacao__linha');
		var i = 0;

		function proxima() {
			if (i > 0) {
				linhas[i - 1].classList.remove('is-digitando');
				linhas[i - 1].classList.add('is-digitada');
			}

			if (i >= linhas.length) {
				linhas[linhas.length - 1].classList.add('is-fim');
				return;
			}

			var linha = linhas[i];
			var texto = linha.querySelector('.sus-digitacao__texto');
			i += 1;

			texto.addEventListener('animationend', proxima, { once: true });
			linha.classList.add('is-digitando');
		}

		proxima();
	}

	var observador = new IntersectionObserver(function (entradas) {
		entradas.forEach(function (entrada) {
			if (!entrada.isIntersecting) {
				return;
			}

			observador.unobserve(entrada.target);
			digitar(entrada.target);
		});
	}, { threshold: 0.5 });

	frases.forEach(function (frase) {
		frase.classList.add('is-pronta');
		observador.observe(frase);
	});
})();
