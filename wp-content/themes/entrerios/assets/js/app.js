/**
 * Alfama WEB v3 — JS do tema.
 *
 * DÍVIDAS RESOLVIDAS (v2): sem jQuery (era a 1.11.1, de 2014, carregada à mão
 * no header), sem Bootstrap JS e sem <script> inline em wp_footer. Tudo aqui é
 * ES2018+ com fetch e AbortController, progressivamente aprimorado: menu,
 * formulário e filtro funcionam sem JS, e só ficam melhores com ele.
 *
 * Depende de window.AW, injetado por wp_localize_script (inc/assets.php).
 */
(function () {
	'use strict';

	var AW = window.AW || {};
	var reduzirMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* =====================================================================
	 * Utilidades
	 * ================================================================== */

	function $(seletor, escopo) {
		return (escopo || document).querySelector(seletor);
	}

	function $$(seletor, escopo) {
		return Array.prototype.slice.call((escopo || document).querySelectorAll(seletor));
	}

	/**
	 * Escapa texto vindo do banco antes de entrar em innerHTML.
	 *
	 * @param {string} texto
	 * @returns {string}
	 */
	function esc(texto) {
		var div = document.createElement('div');
		div.textContent = texto == null ? '' : String(texto);
		return div.innerHTML;
	}

	function debounce(fn, espera) {
		var timer;
		return function () {
			var args = arguments;
			var ctx = this;
			clearTimeout(timer);
			timer = setTimeout(function () {
				fn.apply(ctx, args);
			}, espera);
		};
	}

	/**
	 * POST para admin-ajax.php com o nonce do tema.
	 *
	 * @param {string} acao
	 * @param {Object|FormData} dados
	 * @param {AbortSignal} [signal]
	 * @returns {Promise<Object>}
	 */
	function ajax(acao, dados, signal) {
		var body = dados instanceof FormData ? dados : new FormData();

		if (!(dados instanceof FormData)) {
			Object.keys(dados || {}).forEach(function (chave) {
				var valor = dados[chave];

				if (Array.isArray(valor)) {
					valor.forEach(function (v) {
						body.append(chave + '[]', v);
					});
				} else if (valor !== null && valor !== undefined && valor !== '') {
					body.append(chave, valor);
				}
			});
		}

		body.set('action', acao);
		body.set('nonce', AW.nonce || '');

		return fetch(AW.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
			signal: signal
		}).then(function (r) {
			return r.json();
		});
	}

	/* =====================================================================
	 * Menu mobile
	 * ================================================================== */

	function iniciarMenu() {
		var toggle = $('[data-aw-menu-toggle]');
		var menu = $('#menu-mobile');

		if (!toggle || !menu) {
			return;
		}

		var fechar = $('[data-aw-menu-close]', menu);

		function abrir() {
			menu.hidden = false;
			toggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('aw-menu-aberto');

			var primeiroLink = $('a, button', menu);
			if (primeiroLink) {
				primeiroLink.focus();
			}
		}

		function esconder() {
			menu.hidden = true;
			toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('aw-menu-aberto');
			toggle.focus();
		}

		toggle.addEventListener('click', abrir);

		if (fechar) {
			fechar.addEventListener('click', esconder);
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !menu.hidden) {
				esconder();
			}
		});

		// Fecha ao navegar para uma âncora da própria página.
		$$('a[href^="#"]', menu).forEach(function (link) {
			link.addEventListener('click', esconder);
		});
	}

	/* =====================================================================
	 * Swiper — inicializado por data-attribute, sem JS por página
	 * ================================================================== */

	function iniciarSwipers() {
		if (typeof window.Swiper !== 'function') {
			return;
		}

		$$('[data-aw-swiper]').forEach(function (el) {
			// Guarda de idempotência: esta função roda no DOMContentLoaded e
			// de novo no load, porque o Swiper vem do rodapé.
			if (el.dataset.awSwiperPronto === '1') {
				return;
			}

			el.dataset.awSwiperPronto = '1';

			var opcoes = { slidesPerView: 1, spaceBetween: 24 };

			try {
				opcoes = Object.assign(opcoes, JSON.parse(el.dataset.awSwiper || '{}'));
			} catch (e) {
				// data-attribute malformado: segue com o padrão.
			}

			if (reduzirMovimento) {
				opcoes.autoplay = false;
				opcoes.speed = 0;
			}

			new window.Swiper(el, opcoes);
		});
	}

	/* =====================================================================
	 * Fancybox
	 * ================================================================== */

	var fancyboxPronto = false;

	function iniciarFancybox() {
		if (fancyboxPronto) {
			return;
		}

		if (window.Fancybox && typeof window.Fancybox.bind === 'function') {
			window.Fancybox.bind('[data-fancybox]', {});
			fancyboxPronto = true;
		}
	}

	/* =====================================================================
	 * Filtro e paginação assíncronos
	 * ================================================================== */

	function iniciarFiltros() {
		$$('[data-aw-filtro]').forEach(function (form) {
			var grid = $(form.dataset.target);
			var wrapPaginacao = form.dataset.pagination ? $(form.dataset.pagination) : null;
			var contador = form.dataset.count ? $(form.dataset.count) : null;
			var acao = form.dataset.action;

			if (!grid || !acao) {
				return;
			}

			var controller = null;
			var paginaAtual = 1;

			function skeletons() {
				var quantos = Math.max(3, grid.children.length || 6);
				var html = '';

				for (var i = 0; i < quantos; i++) {
					html += '<div class="aw-skeleton" aria-hidden="true"></div>';
				}

				grid.setAttribute('aria-busy', 'true');
				grid.innerHTML = html;
			}

			function buscar(pagina) {
				paginaAtual = pagina || 1;

				// Cancela a requisição anterior: respostas lentas não podem
				// sobrescrever uma busca mais recente.
				if (controller) {
					controller.abort();
				}

				controller = new AbortController();

				var dados = new FormData(form);
				dados.delete('action');
				dados.set('paged', String(paginaAtual));

				skeletons();

				var inicio = Date.now();

				ajax(acao, dados, controller.signal)
					.then(function (resposta) {
						if (!resposta || !resposta.success) {
							throw new Error((resposta && resposta.data && resposta.data.mensagem) || AW.i18n.erroGenerico);
						}

						// Espera mínima de 350 ms para o skeleton não piscar.
						var espera = Math.max(0, 350 - (Date.now() - inicio));

						setTimeout(function () {
							aplicar(resposta.data);
						}, espera);
					})
					.catch(function (erro) {
						if (erro.name === 'AbortError') {
							return;
						}

						grid.setAttribute('aria-busy', 'false');
						grid.innerHTML = '<p class="aw-vazio">' + (erro.message || AW.i18n.erroGenerico) + '</p>';
					});
			}

			function aplicar(dados) {
				grid.setAttribute('aria-busy', 'false');
				grid.innerHTML = dados.cards || '';

				if (wrapPaginacao) {
					wrapPaginacao.innerHTML = dados.paginacao || '';
				}

				if (contador) {
					if (dados.fallback) {
						contador.textContent = AW.i18n.semResultado + ' ' +
							(dados.termoBusca ? '“' + dados.termoBusca + '”' : '');
					} else {
						contador.textContent = dados.total + '';
					}
				}
			}

			// Busca textual com debounce; selects reagem na hora.
			$$('input[type="search"], input[type="text"]', form).forEach(function (input) {
				input.addEventListener('input', debounce(function () {
					buscar(1);
				}, 400));
			});

			$$('select, input[type="checkbox"], input[type="radio"]', form).forEach(function (campo) {
				campo.addEventListener('change', function () {
					buscar(1);
				});
			});

			form.addEventListener('submit', function (e) {
				e.preventDefault();
				buscar(1);
			});

			form.addEventListener('reset', function () {
				setTimeout(function () {
					buscar(1);
				}, 0);
			});

			// Paginação: delegação, porque o HTML é substituído a cada busca.
			if (wrapPaginacao) {
				wrapPaginacao.addEventListener('click', function (e) {
					var botao = e.target.closest('[data-page]');

					if (!botao || botao.disabled) {
						return;
					}

					buscar(parseInt(botao.dataset.page, 10) || 1);
					grid.scrollIntoView({ behavior: reduzirMovimento ? 'auto' : 'smooth', block: 'start' });
				});
			}

			// Limpa da barra de endereços os parâmetros já aplicados no SSR.
			if (window.location.search.indexOf('termo=') > -1 || window.location.search.indexOf('cidade=') > -1) {
				window.history.replaceState({}, document.title, window.location.pathname);
			}
		});
	}

	/* =====================================================================
	 * Cascata cidade → bairro
	 * ================================================================== */

	function iniciarCascata() {
		$$('[data-aw-cascata]').forEach(function (select) {
			var alvo = $(select.dataset.awCascataAlvo);

			if (!alvo) {
				return;
			}

			var vazio = alvo.querySelector('option') ? alvo.querySelector('option').textContent : '';

			select.addEventListener('change', function () {
				alvo.disabled = true;
				alvo.innerHTML = '<option value="">…</option>';

				if (!select.value) {
					alvo.innerHTML = '<option value="">' + esc(vazio) + '</option>';
					return;
				}

				ajax('aw_termos_filhos', {
					taxonomia: select.dataset.awCascata,
					pai: select.value
				}).then(function (resposta) {
					var termos = (resposta && resposta.success && resposta.data.termos) || [];

					if (!termos.length) {
						alvo.innerHTML = '<option value="">' + esc(vazio) + '</option>';
						return;
					}

					var html = '<option value="">' + esc(vazio) + '</option>';

					termos.forEach(function (termo) {
						html += '<option value="' + esc(termo.slug) + '">' + esc(termo.nome) + '</option>';
					});

					alvo.innerHTML = html;
					alvo.disabled = false;
				}).catch(function () {
					alvo.innerHTML = '<option value="">' + esc(vazio) + '</option>';
				});
			});
		});
	}

	/* =====================================================================
	 * Formulários
	 * ================================================================== */

	/**
	 * Resolve o token do reCAPTCHA v3, se estiver ativo.
	 *
	 * @param {string} acao
	 * @returns {Promise<string>}
	 */
	function tokenRecaptcha(acao) {
		if (!window.grecaptcha || !window.AW_RECAPTCHA_KEY) {
			return Promise.resolve('');
		}

		return new Promise(function (resolve) {
			window.grecaptcha.ready(function () {
				window.grecaptcha
					.execute(window.AW_RECAPTCHA_KEY, { action: acao })
					.then(resolve)
					.catch(function () {
						resolve('');
					});
			});
		});
	}

	function iniciarFormularios() {
		$$('form[data-aw-form-ajax]').forEach(function (form) {
			var botao = $('[data-aw-form-submit]', form);
			var spinner = botao ? $('.aw-spinner', botao) : null;
			var status = $('.aw-form-status', form);

			function mostrar(tipo, texto) {
				if (!status) {
					return;
				}

				status.hidden = false;
				status.className = 'aw-form-status is-' + tipo;
				status.textContent = texto;
			}

			form.addEventListener('submit', function (e) {
				e.preventDefault();

				if (botao) {
					botao.disabled = true;
				}

				if (spinner) {
					spinner.hidden = false;
				}

				var campoToken = form.querySelector('[name="aw_recaptcha_token"]');
				var acaoRecaptcha = campoToken ? campoToken.dataset.awRecaptchaAction || 'form' : 'form';

				tokenRecaptcha(acaoRecaptcha)
					.then(function (token) {
						if (campoToken) {
							campoToken.value = token;
						}

						var dados = new FormData(form);
						dados.delete('action');

						return ajax('aw_form', dados);
					})
					.then(function (resposta) {
						var payload = (resposta && resposta.data) || {};

						if (resposta && resposta.success) {
							mostrar('success', payload.mensagem);
							form.reset();
						} else {
							mostrar('error', payload.mensagem || AW.i18n.erroGenerico);
						}
					})
					.catch(function () {
						mostrar('error', AW.i18n.erroGenerico);
					})
					.then(function () {
						if (botao) {
							botao.disabled = false;
						}

						if (spinner) {
							spinner.hidden = true;
						}
					});
			});
		});
	}

	/* =====================================================================
	 * Máscara de telefone (BR), sem dependência externa
	 * ================================================================== */

	function iniciarMascaras() {
		$$('[data-aw-mask="telefone"]').forEach(function (input) {
			input.addEventListener('input', function () {
				var d = input.value.replace(/\D/g, '').slice(0, 11);

				if (d.length <= 10) {
					input.value = d
						.replace(/^(\d{2})(\d)/, '($1) $2')
						.replace(/(\d{4})(\d)/, '$1-$2');
				} else {
					input.value = d
						.replace(/^(\d{2})(\d)/, '($1) $2')
						.replace(/(\d{5})(\d)/, '$1-$2');
				}
			});
		});
	}

	/* =====================================================================
	 * Toast do fluxo sem JS: limpa a query string e some sozinho
	 * ================================================================== */

	function iniciarToast() {
		var toast = $('[data-aw-toast]');

		if (!toast) {
			return;
		}

		toast.focus();

		var url = new URL(window.location.href);
		url.searchParams.delete('aw_form');
		window.history.replaceState({}, document.title, url.pathname + (url.search || '') + url.hash);

		var fechar = $('[data-aw-toast-fechar]', toast);

		function esconder() {
			toast.remove();
		}

		if (fechar) {
			fechar.addEventListener('click', esconder);
		}

		if (!toast.classList.contains('aw-toast--error')) {
			setTimeout(esconder, 8000);
		}
	}

	/* =====================================================================
	 * Boot
	 * ================================================================== */

	function iniciar() {
		iniciarMenu();
		iniciarSwipers();
		iniciarFancybox();
		// A cascata é registrada ANTES do filtro de propósito: ao trocar a
		// cidade, o handler dela zera o select de bairro, e só então o handler
		// do filtro monta o FormData — sem isso, a primeira busca sairia com o
		// bairro da cidade anterior.
		iniciarCascata();
		iniciarFiltros();
		iniciarFormularios();
		iniciarMascaras();
		iniciarToast();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', iniciar);
	} else {
		iniciar();
	}

	// Swiper e Fancybox são carregados no rodapé e podem chegar depois do
	// DOMContentLoaded; o load garante a inicialização em qualquer ordem.
	window.addEventListener('load', function () {
		iniciarSwipers();
		iniciarFancybox();
	});
})();
