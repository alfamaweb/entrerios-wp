/**
 * Vitrine de produtos da Home — abas por linha, carrossel, barra de progresso
 * e setas (Figma: "Produtos / Vitrine").
 *
 *  - Abas: padrão WAI-ARIA (setas, Home, End); troca título e painel.
 *  - Carrossel: rolagem nativa (dedo, trackpad, teclado) + arrastar com o
 *    mouse. A barra mostra a fração visível e a posição da rolagem.
 *  - Setas: avançam / voltam N cards inteiros (600 ms ease-in-out). N é o
 *    mesmo em todas as linhas (o que cabe na tela, reduzido para a linha
 *    mais longa ter 3 cliques), então linha com menos cards = menos cliques.
 *    Ficam desabilitadas quando já não há para onde rolar.
 * Sem dependências. Carregado com defer apenas no template Home.
 */
(function () {
	'use strict';

	var reduzido = window.matchMedia('(prefers-reduced-motion: reduce)');

	document.querySelectorAll('[data-er-vitrine]').forEach(iniciarVitrine);

	function iniciarVitrine(vitrine) {
		var abas = Array.prototype.slice.call(vitrine.querySelectorAll('[data-er-aba]'));

		var paineis = Array.prototype.slice.call(vitrine.querySelectorAll('.vitrine-er__painel'));

		// Cards por clique — o MESMO para todas as linhas da vitrine, para que
		// a linha com menos cards precise de menos cliques (a rolagem mantém o
		// mesmo ritmo em todas). É o que cabe na tela, reduzido só o bastante
		// para que a linha mais longa leve PASSOS_MIN cliques.
		var PASSOS_MIN = 3;

		function cardsPorPasso(cabem) {
			var maisLonga = paineis.reduce(function (maior, p) {
				return Math.max(maior, p.querySelectorAll('.vitrine-er__item').length);
			}, 0);
			var escondidos = Math.max(0, maisLonga - cabem);

			return Math.max(1, Math.min(cabem, Math.floor(escondidos / PASSOS_MIN)));
		}

		paineis.forEach(function (painel) {
			iniciarCarrossel(painel, cardsPorPasso);
		});

		if (!abas.length) {
			return;
		}

		function ativar(indice, focar) {
			indice = (indice + abas.length) % abas.length;

			abas.forEach(function (aba, i) {
				var ativa = i === indice;

				aba.classList.toggle('is-ativo', ativa);
				aba.setAttribute('aria-selected', ativa ? 'true' : 'false');
				aba.tabIndex = ativa ? 0 : -1;

				if (ativa) {
					trazerParaVista(aba);

					if (focar) {
						aba.focus({ preventScroll: true });
					}
				}
			});

			vitrine.querySelectorAll('[data-er-painel-item]').forEach(function (el) {
				var ativo = parseInt(el.getAttribute('data-er-painel-item'), 10) === indice;

				el.hidden = !ativo;
				el.classList.toggle('is-ativo', ativo);
			});

			var painel = vitrine.querySelector('#vitrine-painel-' + indice);
			if (painel && painel.atualizarCarrossel) {
				painel.atualizarCarrossel();
			}
		}

		abas.forEach(function (aba, i) {
			aba.addEventListener('click', function () {
				ativar(i);
			});
		});

		abas[0].parentElement.addEventListener('keydown', function (e) {
			var atual = abas.findIndex(function (aba) {
				return aba.classList.contains('is-ativo');
			});
			var mapa = { ArrowRight: atual + 1, ArrowLeft: atual - 1, Home: 0, End: abas.length - 1 };

			if (Object.prototype.hasOwnProperty.call(mapa, e.key)) {
				e.preventDefault();
				ativar(mapa[e.key], true);
			}
		});
	}

	// Abas roláveis (Tablet/Mobile): mostra a aba ativa sem mexer na página.
	function trazerParaVista(aba) {
		var trilho = aba.closest('.vitrine-er__abas-viewport');

		if (!trilho || trilho.scrollWidth <= trilho.clientWidth) {
			return;
		}

		trilho.scrollTo({
			left: aba.offsetLeft - (trilho.clientWidth - aba.offsetWidth) / 2,
			behavior: reduzido.matches ? 'auto' : 'smooth'
		});
	}

	function iniciarCarrossel(painel, cardsPorPasso) {
		var viewport = painel.querySelector('[data-er-carrossel]');
		var barra = painel.querySelector('[data-er-progresso]');
		var inicio = painel.querySelector('[data-er-inicio]');
		var fim = painel.querySelector('[data-er-fim]');

		if (!viewport) {
			return;
		}

		var animacao = null;

		function atualizar() {
			var total = viewport.scrollWidth;
			var visivel = viewport.clientWidth;
			var maximo = Math.max(0, total - visivel);
			var pos = viewport.scrollLeft;

			if (barra && total > 0) {
				var fracao = Math.min(1, visivel / total);
				barra.style.setProperty('--vit-visivel', (fracao * 100) + '%');
				// translateX em % da própria barra: anda (1 − fração) do trilho.
				var andou = maximo > 0 ? pos / maximo : 0;
				barra.style.setProperty('--vit-posicao', (andou * (1 - fracao) / fracao * 100) + '%');
			}

			if (inicio) {
				inicio.disabled = pos <= 1;
			}

			if (fim) {
				fim.disabled = pos >= maximo - 1;
			}
		}

		// Rolagem com duração fixa (scroll-behavior: smooth não aceita duração).
		function rolarPara(destino) {
			cancelAnimationFrame(animacao);

			if (reduzido.matches) {
				viewport.scrollLeft = destino;
				return;
			}

			var origem = viewport.scrollLeft;
			var distancia = destino - origem;
			var duracao = 600;
			var t0 = null;

			viewport.style.scrollSnapType = 'none';

			function passo(t) {
				if (t0 === null) {
					t0 = t;
				}

				var p = Math.min(1, (t - t0) / duracao);
				// ease-in-out (cúbica)
				var e = p < 0.5 ? 4 * p * p * p : 1 - Math.pow(-2 * p + 2, 3) / 2;

				viewport.scrollLeft = origem + distancia * e;

				if (p < 1) {
					animacao = requestAnimationFrame(passo);
				} else {
					viewport.style.scrollSnapType = '';
				}
			}

			animacao = requestAnimationFrame(passo);
		}

		function larguraDoCard() {
			var itens = viewport.querySelectorAll('.vitrine-er__item');

			if (itens.length > 1) {
				return itens[1].offsetLeft - itens[0].offsetLeft;
			}

			return itens.length ? itens[0].offsetWidth : viewport.clientWidth;
		}

		// Cards inteiros visíveis na área útil (sem as laterais do trilho).
		function cardsQueCabem(card) {
			var trilho = viewport.querySelector('.vitrine-er__trilho');
			var estilo = trilho ? getComputedStyle(trilho) : null;
			var laterais = estilo ? parseFloat(estilo.paddingLeft) + parseFloat(estilo.paddingRight) : 0;
			var gap = estilo ? parseFloat(estilo.columnGap) || 0 : 0;

			return Math.max(1, Math.floor((viewport.clientWidth - laterais + gap) / card));
		}

		function tamanhoDoPasso() {
			var card = larguraDoCard();

			return cardsPorPasso(cardsQueCabem(card)) * card;
		}

		function andar(direcao) {
			var p = tamanhoDoPasso();
			var maximo = viewport.scrollWidth - viewport.clientWidth;
			// Parte da posição alinhada ao card mais próximo, para não acumular
			// sobras depois de uma rolagem manual.
			var atual = Math.round(viewport.scrollLeft / p) * p;
			var destino = Math.max(0, Math.min(maximo, atual + direcao * p));

			// Perto do fim, vai até o fim (o último card não fica cortado).
			if (direcao > 0 && maximo - destino < p / 2) {
				destino = maximo;
			}

			rolarPara(destino);
		}

		if (inicio) {
			inicio.addEventListener('click', function () {
				andar(-1);
			});
		}

		if (fim) {
			fim.addEventListener('click', function () {
				andar(1);
			});
		}

		// Arrastar com o mouse (toque e trackpad já rolam nativamente).
		var arrasto = null;

		viewport.addEventListener('pointerdown', function (e) {
			if (e.pointerType !== 'mouse' || e.button !== 0) {
				return;
			}

			cancelAnimationFrame(animacao);
			arrasto = { x: e.clientX, scroll: viewport.scrollLeft, moveu: false };
		});

		window.addEventListener('pointermove', function (e) {
			if (!arrasto) {
				return;
			}

			var dx = e.clientX - arrasto.x;

			if (!arrasto.moveu && Math.abs(dx) > 5) {
				arrasto.moveu = true;
				viewport.classList.add('is-arrastando');
			}

			if (arrasto.moveu) {
				viewport.scrollLeft = arrasto.scroll - dx;
			}
		});

		window.addEventListener('pointerup', function () {
			if (!arrasto) {
				return;
			}

			var moveu = arrasto.moveu;
			arrasto = null;

			// Solta o clique só depois do evento click, para o arrasto não abrir
			// o card que estava embaixo do ponteiro.
			setTimeout(function () {
				viewport.classList.remove('is-arrastando');
			}, 0);
		});

		viewport.addEventListener('click', function (e) {
			if (viewport.classList.contains('is-arrastando')) {
				e.preventDefault();
			}
		}, true);

		viewport.addEventListener('scroll', atualizar, { passive: true });
		window.addEventListener('resize', atualizar);

		painel.atualizarCarrossel = atualizar;
		atualizar();
	}
})();
