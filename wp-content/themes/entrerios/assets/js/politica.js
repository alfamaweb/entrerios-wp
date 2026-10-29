/**
 * Política de Privacidade — sumário (template-parts/politica/conteudo.php).
 *
 * 1. Clique num item: rola até a seção em 600 ms, ease in and out (Figma:
 *    Item/<seção> → On click → Scroll to, Animate, 600ms) e atualiza o
 *    #hash sem pular. Com prefers-reduced-motion, vai direto.
 * 2. Rolagem: o item da seção que está no terço de cima da tela fica ativo
 *    (.is-ativo + aria-current="location").
 *
 * Sem JS os itens são âncoras comuns e o primeiro fica marcado. ~1 KB.
 */
(function () {
	'use strict';

	var sumario = document.querySelector('[data-pol-sumario]');

	if (!sumario) {
		return;
	}

	var links = Array.prototype.slice.call(sumario.querySelectorAll('a[href^="#"]'));
	var alvos = links.map(function (link) {
		return document.getElementById(decodeURIComponent(link.hash.slice(1)));
	});
	var reduzir = window.matchMedia('(prefers-reduced-motion: reduce)');
	var DURACAO = 600;
	var rolando = null;

	function ativar(indice) {
		links.forEach(function (link, i) {
			var ativo = i === indice;
			link.classList.toggle('is-ativo', ativo);

			if (ativo) {
				link.setAttribute('aria-current', 'location');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	}

	// O style.css liga scroll-behavior: smooth no <html>; cada passo da
	// animação precisa ser instantâneo, senão o navegador suaviza por cima.
	function irPara(y) {
		window.scrollTo({ top: y, left: 0, behavior: 'instant' });
	}

	// Ease in and out (cubic), como o "Ease in and out" do Figma.
	function suavizar(t) {
		return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
	}

	function rolarAte(alvo, aoTerminar) {
		var margem = parseFloat(getComputedStyle(alvo).scrollMarginTop) || 0;
		var destino = alvo.getBoundingClientRect().top + window.pageYOffset - margem;

		if (rolando) {
			cancelAnimationFrame(rolando);
		}

		if (reduzir.matches) {
			irPara(destino);
			aoTerminar();
			return;
		}

		var inicio = window.pageYOffset;
		var distancia = destino - inicio;
		var t0 = null;

		function passo(agora) {
			if (t0 === null) {
				t0 = agora;
			}

			var p = Math.min(1, (agora - t0) / DURACAO);
			irPara(inicio + distancia * suavizar(p));

			if (p < 1) {
				rolando = requestAnimationFrame(passo);
			} else {
				rolando = null;
				aoTerminar();
			}
		}

		rolando = requestAnimationFrame(passo);
	}

	links.forEach(function (link, i) {
		link.addEventListener('click', function (e) {
			var alvo = alvos[i];

			if (!alvo || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) {
				return;
			}

			e.preventDefault();
			ativar(i);

			rolarAte(alvo, function () {
				history.replaceState(null, '', link.hash);
				// Leva o foco do teclado para a seção (sem rolar de novo).
				alvo.setAttribute('tabindex', '-1');
				alvo.focus({ preventScroll: true });
			});
		});
	});

	// Seção ativa: a primeira que cruza a faixa entre 15% e 40% da altura
	// da tela (rootMargin abaixo).
	if (!('IntersectionObserver' in window)) {
		return;
	}

	var visiveis = new Set();

	var observador = new IntersectionObserver(function (entradas) {
		entradas.forEach(function (entrada) {
			if (entrada.isIntersecting) {
				visiveis.add(entrada.target);
			} else {
				visiveis.delete(entrada.target);
			}
		});

		// Durante a rolagem do clique o item já foi marcado.
		if (rolando) {
			return;
		}

		var indice = -1;

		alvos.forEach(function (alvo, i) {
			if (visiveis.has(alvo)) {
				indice = indice === -1 ? i : indice;
			}
		});

		if (indice !== -1) {
			ativar(indice);
		}
	}, { rootMargin: '-15% 0px -60% 0px' });

	alvos.forEach(function (alvo) {
		if (alvo) {
			observador.observe(alvo);
		}
	});

	// A última seção é curta e pode nunca chegar à faixa: no fim da página
	// ela fica ativa.
	var ultimo = alvos[alvos.length - 1];
	var agendado = false;

	window.addEventListener('scroll', function () {
		if (agendado || rolando || !ultimo) {
			return;
		}

		agendado = true;
		requestAnimationFrame(function () {
			agendado = false;

			if (ultimo.getBoundingClientRect().bottom <= window.innerHeight + 2) {
				ativar(alvos.length - 1);
			}
		});
	}, { passive: true });
})();
