/**
 * Página A Entre Rios — vídeo da fábrica e galeria.
 *
 * Vídeo: quando o card chega perto da tela, a prévia curta (sem som) é
 * baixada e toca em loop por baixo do Play Button; fora da tela, pausa. Não
 * roda com prefers-reduced-motion nem com economia de dados (fica a capa).
 * O vídeo completo só é baixado no clique em Play: entra com controles e som.
 *
 * Galeria: setas avançam um slide (rolagem suave nativa + scroll-snap),
 * contador "01 / 02" e pontos acompanham a rolagem (dedo, trackpad, teclado).
 */
(function () {
	'use strict';

	var reduzido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var economia = !!(navigator.connection && navigator.connection.saveData);

	/* ------------------------------------------------------------------
	 * Vídeo
	 * --------------------------------------------------------------- */
	document.querySelectorAll('[data-ae-video]').forEach(function (card) {
		var src = card.getAttribute('data-src');
		var previaSrc = card.getAttribute('data-previa');
		var botao = card.querySelector('[data-ae-video-play]');
		var previa = null;
		var observador = null;

		if (previaSrc && !reduzido && !economia && 'IntersectionObserver' in window) {
			observador = new IntersectionObserver(function (entradas) {
				entradas.forEach(function (entrada) {
					if (entrada.isIntersecting) {
						if (!previa) {
							previa = document.createElement('video');
							previa.className = 'ae-video__previa';
							previa.muted = true;
							previa.loop = true;
							previa.playsInline = true;
							previa.setAttribute('muted', '');
							previa.setAttribute('playsinline', '');
							previa.setAttribute('aria-hidden', 'true');
							previa.preload = 'auto';
							previa.src = previaSrc;
							previa.addEventListener('playing', function () {
								previa.classList.add('is-pronta');
							}, { once: true });
							var capa = card.querySelector('.ae-video__capa');
							card.insertBefore(previa, capa ? capa.nextSibling : card.firstChild);
						}
						var p = previa.play();
						if (p && p.catch) {
							p.catch(function () { /* autoplay bloqueado: fica a capa */ });
						}
					} else if (previa) {
						previa.pause();
					}
				});
			}, { rootMargin: '200px 0px' });

			observador.observe(card);
		}

		if (!botao || !src) {
			return;
		}

		botao.addEventListener('click', function () {
			if (observador) {
				observador.disconnect();
			}
			if (previa) {
				previa.pause();
				previa.remove();
			}

			var video = document.createElement('video');
			var capa = card.querySelector('.ae-video__capa');

			video.src = src;
			video.controls = true;
			video.playsInline = true;
			video.preload = 'auto';

			if (capa && capa.currentSrc) {
				video.poster = capa.currentSrc;
			}

			card.appendChild(video);
			card.classList.add('is-tocando');

			var tocando = video.play();
			if (tocando && tocando.catch) {
				tocando.catch(function () { /* bloqueado: ficam os controles */ });
			}

			video.focus();
		});
	});

	/* ------------------------------------------------------------------
	 * Galeria
	 * --------------------------------------------------------------- */
	document.querySelectorAll('[data-ae-galeria]').forEach(function (galeria) {
		var trilho = galeria.querySelector('[data-ae-galeria-trilho]');
		var nav = galeria.querySelector('[data-ae-galeria-nav]');
		var pontosWrap = galeria.querySelector('[data-ae-galeria-pontos]');

		if (!trilho || !nav) {
			return;
		}

		var slides = Array.prototype.slice.call(trilho.children);
		var pontos = pontosWrap ? Array.prototype.slice.call(pontosWrap.children) : [];
		var atualEl = galeria.querySelector('[data-ae-galeria-atual]');
		var anterior = galeria.querySelector('[data-ae-galeria-anterior]');
		var proxima = galeria.querySelector('[data-ae-galeria-proxima]');
		var atual = 0;
		var pendente = false;

		nav.hidden = false;
		if (pontosWrap) {
			pontosWrap.hidden = false;
		}

		function inicioDoTrilho() {
			return parseFloat(getComputedStyle(trilho).scrollPaddingInlineStart) || 0;
		}

		function irPara(i) {
			i = Math.max(0, Math.min(slides.length - 1, i));
			trilho.scrollTo({
				left: slides[i].offsetLeft - inicioDoTrilho(),
				behavior: reduzido ? 'auto' : 'smooth'
			});
		}

		function atualizar() {
			pendente = false;

			var fim = trilho.scrollWidth - trilho.clientWidth;
			var alvo = trilho.scrollLeft + inicioDoTrilho();
			var i = 0;
			var menor = Infinity;

			// No fim da rolagem, o último slide é o atual mesmo que não chegue
			// à borda esquerda.
			if (trilho.scrollLeft >= fim - 2) {
				i = slides.length - 1;
			} else {
				slides.forEach(function (s, n) {
					var d = Math.abs(s.offsetLeft - alvo);
					if (d < menor) {
						menor = d;
						i = n;
					}
				});
			}

			atual = i;

			if (atualEl) {
				atualEl.textContent = String(i + 1).padStart(2, '0');
			}

			pontos.forEach(function (p, n) {
				if (n === i) {
					p.setAttribute('aria-current', 'true');
				} else {
					p.removeAttribute('aria-current');
				}
			});

			if (anterior) {
				anterior.disabled = trilho.scrollLeft <= 2;
			}
			if (proxima) {
				proxima.disabled = trilho.scrollLeft >= fim - 2;
			}
		}

		trilho.addEventListener('scroll', function () {
			if (!pendente) {
				pendente = true;
				requestAnimationFrame(atualizar);
			}
		}, { passive: true });

		window.addEventListener('resize', function () {
			requestAnimationFrame(atualizar);
		});

		if (anterior) {
			anterior.addEventListener('click', function () { irPara(atual - 1); });
		}
		if (proxima) {
			proxima.addEventListener('click', function () { irPara(atual + 1); });
		}

		pontos.forEach(function (p, n) {
			p.addEventListener('click', function () { irPara(n); });
		});

		atualizar();
	});
})();
