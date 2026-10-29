/**
 * Hero Slider da Home — autoplay, seletor de linhas e anterior/próximo.
 *
 * O crossfade é todo CSS (classe .is-ativo em fundo, produto e texto de cada
 * slide, ver assets/css/home.css); aqui só se troca a classe e o estado ARIA.
 * Sem dependências. Carregado com defer apenas no template Home.
 *
 * Autoplay (data-intervalo, em segundos; 0 desliga) pausa quando:
 *  - o usuário prefere movimento reduzido;
 *  - o ponteiro está sobre o hero ou o foco está dentro dele;
 *  - o hero sai da tela ou a aba fica em segundo plano.
 */
(function () {
	'use strict';

	var hero = document.querySelector('[data-er-hero]');

	if (!hero) {
		return;
	}

	var chips = Array.prototype.slice.call(hero.querySelectorAll('[data-er-chip]'));
	var total = hero.querySelectorAll('.hero-er__copia').length;

	if (total < 2) {
		return;
	}

	var atual = 0;
	var intervalo = parseInt(hero.getAttribute('data-intervalo'), 10) * 1000 || 0;
	var reduzido = window.matchMedia('(prefers-reduced-motion: reduce)');
	var timer = null;
	var pausas = { ponteiro: false, foco: false, fora: false };

	function ir(indice, focarChip) {
		atual = (indice + total) % total;

		hero.querySelectorAll('[data-er-slide]').forEach(function (el) {
			var ativo = parseInt(el.getAttribute('data-er-slide'), 10) === atual;

			el.classList.toggle('is-ativo', ativo);

			if (el.classList.contains('hero-er__copia')) {
				el.inert = !ativo;
			}
		});

		chips.forEach(function (chip, i) {
			var ativo = i === atual;

			chip.classList.toggle('is-ativo', ativo);
			chip.setAttribute('aria-selected', ativo ? 'true' : 'false');
			chip.tabIndex = ativo ? 0 : -1;

			if (ativo) {
				mostrarChip(chip);

				if (focarChip) {
					chip.focus({ preventScroll: true });
				}
			}
		});
	}

	// No mobile o seletor rola na horizontal: traz o chip ativo para a vista
	// sem mexer na rolagem vertical da página (scrollIntoView mexeria).
	function mostrarChip(chip) {
		var trilho = chip.parentElement;

		if (!trilho || trilho.scrollWidth <= trilho.clientWidth) {
			return;
		}

		trilho.scrollTo({
			left: chip.offsetLeft - (trilho.clientWidth - chip.offsetWidth) / 2,
			behavior: reduzido.matches ? 'auto' : 'smooth'
		});
	}

	function pausado() {
		return !intervalo || reduzido.matches || document.hidden ||
			pausas.ponteiro || pausas.foco || pausas.fora;
	}

	function agendar() {
		clearTimeout(timer);

		if (!pausado()) {
			timer = setTimeout(function () {
				ir(atual + 1);
				agendar();
			}, intervalo);
		}
	}

	// Navegação manual reinicia a contagem.
	function navegar(indice, focarChip) {
		ir(indice, focarChip);
		agendar();
	}

	chips.forEach(function (chip, i) {
		chip.addEventListener('click', function () {
			navegar(i);
		});
	});

	// Setas, Home e End no tablist (padrão WAI-ARIA de abas).
	if (chips.length) {
		chips[0].parentElement.addEventListener('keydown', function (e) {
			var mapa = { ArrowRight: atual + 1, ArrowLeft: atual - 1, Home: 0, End: total - 1 };

			if (Object.prototype.hasOwnProperty.call(mapa, e.key)) {
				e.preventDefault();
				navegar(mapa[e.key], true);
			}
		});
	}

	var anterior = hero.querySelector('[data-er-anterior]');
	var proximo = hero.querySelector('[data-er-proximo]');

	if (anterior) {
		anterior.addEventListener('click', function () {
			navegar(atual - 1);
		});
	}

	if (proximo) {
		proximo.addEventListener('click', function () {
			navegar(atual + 1);
		});
	}

	hero.addEventListener('pointerenter', function (e) {
		if (e.pointerType === 'mouse') {
			pausas.ponteiro = true;
			agendar();
		}
	});

	hero.addEventListener('pointerleave', function () {
		pausas.ponteiro = false;
		agendar();
	});

	hero.addEventListener('focusin', function () {
		pausas.foco = true;
		agendar();
	});

	hero.addEventListener('focusout', function (e) {
		if (!hero.contains(e.relatedTarget)) {
			pausas.foco = false;
			agendar();
		}
	});

	document.addEventListener('visibilitychange', agendar);

	if (reduzido.addEventListener) {
		reduzido.addEventListener('change', agendar);
	}

	if ('IntersectionObserver' in window) {
		new IntersectionObserver(function (entradas) {
			pausas.fora = !entradas[0].isIntersecting;
			agendar();
		}, { threshold: 0.25 }).observe(hero);
	}

	agendar();
})();
