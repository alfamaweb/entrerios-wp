/**
 * Produto 360° da Home (Figma: "Produto 360° (animado · filtro de produto)").
 *
 *  - Rolagem: a seção vira uma pista de 260svh com o palco preso (sticky). O
 *    quanto já se rolou da pista vira o giro 0° → 360°: troca a vista do
 *    produto, gira o botão do mostrador, enche o anel e a barra, e o contador
 *    vai de 01 a 72 (um quadro a cada 5°). Destaque 1 na 1ª metade, 2 na 2ª.
 *  - Chips: padrão WAI-ARIA de abas; dissolve de 400 ms entre os produtos.
 *  - prefers-reduced-motion ou sem JS: sem pista nem giro, vista frontal.
 */
(function () {
	'use strict';

	var reduzido = window.matchMedia('(prefers-reduced-motion: reduce)');

	document.querySelectorAll('[data-er-p360]').forEach(iniciar);

	function iniciar(secao) {
		var variantes = Array.prototype.slice.call(secao.querySelectorAll('[data-er-p360-variante]'));
		var chips = Array.prototype.slice.call(secao.querySelectorAll('[data-er-p360-chip]'));
		var grau = secao.querySelector('[data-er-p360-grau]');
		var contador = secao.querySelector('[data-er-p360-contador]');
		var total = contador ? parseInt(contador.getAttribute('data-total'), 10) || 72 : 72;
		var atual = 0;
		var ultimoGiro = -1;
		var pedido = null;
		var temporizador = null;

		function varianteAtiva() {
			return variantes[atual];
		}

		// ----- Giro -----------------------------------------------------------
		function progresso() {
			if (!secao.classList.contains('is-giratorio')) {
				return 0;
			}

			var caixa = secao.getBoundingClientRect();
			var percurso = secao.offsetHeight - window.innerHeight;

			return percurso > 0 ? Math.min(1, Math.max(0, -caixa.top / percurso)) : 0;
		}

		function aplicar(p) {
			var graus = Math.round(p * 360 / 5) * 5;   // passos de 5° = 72 quadros
			var quadro = Math.min(total, Math.floor(p * total) + 1);

			secao.style.setProperty('--p360-p', p.toFixed(4));
			secao.style.setProperty('--p360-graus', graus);

			if (grau) {
				grau.textContent = graus + '°';
			}

			if (contador) {
				contador.textContent = String(quadro).padStart(2, '0') + ' / ' + total;
			}

			var variante = varianteAtiva();
			if (!variante) {
				return;
			}

			var vistas = variante.querySelectorAll('.p360-er__vista');
			var indice = Math.min(vistas.length - 1, Math.floor(p * vistas.length));

			if (indice !== ultimoGiro) {
				vistas.forEach(function (vista, i) {
					vista.classList.toggle('is-ativo', i === indice);
				});
				ultimoGiro = indice;
			}

			variante.querySelectorAll('[data-er-p360-destaque]').forEach(function (d) {
				var n = parseInt(d.getAttribute('data-er-p360-destaque'), 10);
				d.classList.toggle('is-visivel', n === (p < 0.5 ? 0 : 1));
			});
		}

		function aoRolar() {
			if (pedido) {
				return;
			}

			pedido = requestAnimationFrame(function () {
				pedido = null;
				aplicar(progresso());
			});
		}

		function configurarGiro() {
			secao.classList.toggle('is-giratorio', !reduzido.matches);
			ultimoGiro = -1;
			aplicar(progresso());
		}

		// Carrega as vistas da variante antes de precisar delas (o giro não
		// pode esperar a rede a cada troca).
		function preCarregar(variante) {
			variante.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
				img.loading = 'eager';
			});
		}

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entradas, obs) {
				if (entradas[0].isIntersecting) {
					preCarregar(varianteAtiva());
					obs.disconnect();
				}
			}, { rootMargin: '400px 0px' }).observe(secao);
		}

		// ----- Chips ----------------------------------------------------------
		function ativar(indice, focar) {
			indice = (indice + variantes.length) % variantes.length;

			if (indice === atual) {
				return;
			}

			var anterior = variantes[atual];
			var proxima = variantes[indice];

			clearTimeout(temporizador);
			preCarregar(proxima);

			proxima.hidden = false;
			// força o estilo inicial antes da transição de opacidade
			void proxima.offsetWidth;
			proxima.classList.add('is-ativo');
			anterior.classList.remove('is-ativo');

			temporizador = setTimeout(function () {
				variantes.forEach(function (v, i) {
					v.hidden = i !== atual;
				});
			}, reduzido.matches ? 0 : 400);

			atual = indice;
			ultimoGiro = -1;

			chips.forEach(function (chip, i) {
				var ativo = i === indice;
				chip.classList.toggle('is-ativo', ativo);
				chip.setAttribute('aria-selected', ativo ? 'true' : 'false');
				chip.tabIndex = ativo ? 0 : -1;

				if (ativo && focar) {
					chip.focus({ preventScroll: true });
				}
			});

			aplicar(progresso());
		}

		chips.forEach(function (chip, i) {
			chip.addEventListener('click', function () {
				ativar(i);
			});
		});

		if (chips.length) {
			chips[0].parentElement.addEventListener('keydown', function (e) {
				var mapa = { ArrowRight: atual + 1, ArrowLeft: atual - 1, Home: 0, End: chips.length - 1 };

				if (Object.prototype.hasOwnProperty.call(mapa, e.key)) {
					e.preventDefault();
					ativar(mapa[e.key], true);
				}
			});
		}

		window.addEventListener('scroll', aoRolar, { passive: true });
		window.addEventListener('resize', aoRolar);

		if (reduzido.addEventListener) {
			reduzido.addEventListener('change', configurarGiro);
		}

		configurarGiro();
	}
})();
