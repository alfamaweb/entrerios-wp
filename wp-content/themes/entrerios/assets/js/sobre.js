/**
 * Vídeo institucional (Sobre a Entre Rios) — o MP4 só é baixado no clique em
 * Play: troca a capa por um <video> com controles nativos e já toca.
 */
(function () {
	'use strict';

	document.querySelectorAll('[data-er-video]').forEach(function (card) {
		var botao = card.querySelector('[data-er-video-play]');
		var src = card.getAttribute('data-src');

		if (!botao || !src) {
			return;
		}

		botao.addEventListener('click', function () {
			var video = document.createElement('video');
			var capa = card.querySelector('.sobre-er__capa');

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
				tocando.catch(function () { /* o navegador bloqueou: fica com os controles */ });
			}

			video.focus();
		});
	});
})();
