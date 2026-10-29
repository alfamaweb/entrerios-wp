/**
 * Página Produtos — filtros, abas e "Carregar mais" sem recarregar.
 *
 * O HTML já funciona sem JS (formulário GET, abas e "Carregar mais" são
 * links). Aqui: cada mudança busca a grade por AJAX (ação aw_filtrar_produtos,
 * inc/produtos.php), "Carregar mais" acrescenta os próximos 12 cards, a URL
 * acompanha o estado (dá para compartilhar/recarregar) e, abaixo de 1280px,
 * o painel de filtros vira um botão que abre e fecha.
 *
 * Depende de window.AW (ajaxUrl, nonce, i18n), do aw-app.
 */
(function () {
	'use strict';

	var form = document.querySelector('[data-er-produtos]');
	var AW = window.AW || {};

	if (!form || !AW.ajaxUrl || !window.fetch) {
		return;
	}

	var grade = form.querySelector('[data-er-grade]');
	var total = form.querySelector('[data-er-total]');
	var resumo = form.querySelector('[data-er-resumo]');
	var paginacao = form.querySelector('[data-er-paginacao]');
	var mostrando = form.querySelector('[data-er-mostrando]');
	var progresso = form.querySelector('[data-er-progresso]');
	var mais = form.querySelector('[data-er-mais]');
	var abas = Array.prototype.slice.call(form.querySelectorAll('[data-er-aba]'));
	var limpar = form.querySelector('[data-er-limpar]');
	var painel = form.querySelector('[data-er-filtros]');
	var botaoFiltros = form.querySelector('[data-er-filtros-botao]');
	var contaFiltros = form.querySelector('[data-er-filtros-total]');
	var desktop = window.matchMedia('(min-width: 1280px)');
	var reduzir = window.matchMedia('(prefers-reduced-motion: reduce)');

	var pagina = parseInt(form.dataset.pagina, 10) || 1;
	var controller = null;

	// Com JS o "Aplicar filtros" sobra: cada caixa já aplica.
	Array.prototype.forEach.call(form.querySelectorAll('[data-er-sem-js]'), function (el) {
		el.hidden = true;
	});

	function caixas(param) {
		return Array.prototype.slice.call(form.querySelectorAll('input[name="' + param + '[]"]'));
	}

	/* ------------------------------------------------------------------
	 * Estado → abas, contador do botão Filtros e URL
	 * ---------------------------------------------------------------- */

	function sincronizarAbas() {
		var marcadas = caixas('categoria').filter(function (c) { return c.checked; }).map(function (c) { return c.value; });
		// Uma categoria marcada = a aba dela; nenhuma = TODOS; várias = nenhuma aba.
		var ativa = marcadas.length === 0 ? '' : (marcadas.length === 1 ? marcadas[0] : null);

		abas.forEach(function (aba) {
			var on = ativa !== null && aba.dataset.erAba === ativa;
			aba.classList.toggle('is-ativo', on);

			if (on) {
				aba.setAttribute('aria-current', 'true');
			} else {
				aba.removeAttribute('aria-current');
			}
		});
	}

	function contarFiltros() {
		var n = form.querySelectorAll('input[type="checkbox"]:checked').length;

		if (contaFiltros) {
			contaFiltros.textContent = n ? String(n) : '';
			contaFiltros.hidden = !n;
		}
	}

	function atualizarUrl() {
		var params = new URLSearchParams();

		Array.prototype.forEach.call(form.querySelectorAll('input[type="checkbox"]:checked'), function (c) {
			params.append(c.name, c.value);
		});

		if (pagina > 1) {
			params.set('pagina', String(pagina));
		}

		var qs = params.toString();
		window.history.replaceState({}, document.title, window.location.pathname + (qs ? '?' + qs : '') + window.location.hash);
	}

	/* ------------------------------------------------------------------
	 * Busca
	 * ---------------------------------------------------------------- */

	function buscar(acrescentar) {
		if (controller) {
			controller.abort();
		}

		controller = new AbortController();

		var proxima = acrescentar ? pagina + 1 : 1;
		var dados = new FormData(form);
		dados.set('action', 'aw_filtrar_produtos');
		dados.set('nonce', AW.nonce || '');
		dados.set('paged', String(proxima));

		form.classList.add('is-carregando');
		grade.setAttribute('aria-busy', 'true');

		if (mais) {
			mais.setAttribute('aria-disabled', 'true');
		}

		fetch(AW.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: dados, signal: controller.signal })
			.then(function (r) { return r.json(); })
			.then(function (resposta) {
				if (!resposta || !resposta.success) {
					throw new Error((resposta && resposta.data && resposta.data.mensagem) || (AW.i18n && AW.i18n.erroGenerico) || '');
				}

				aplicar(resposta.data, acrescentar);
			})
			.catch(function (erro) {
				if (erro.name === 'AbortError') {
					return;
				}

				if (!acrescentar) {
					grade.innerHTML = '<p class="aw-vazio"></p>';
					grade.firstChild.textContent = erro.message || (AW.i18n && AW.i18n.erroGenerico) || '';
				}
			})
			.then(function () {
				form.classList.remove('is-carregando');
				grade.setAttribute('aria-busy', 'false');

				if (mais) {
					mais.removeAttribute('aria-disabled');
				}
			});
	}

	function aplicar(dados, acrescentar) {
		pagina = dados.pagina || 1;

		if (acrescentar) {
			var antes = grade.children.length;
			grade.insertAdjacentHTML('beforeend', dados.cards || '');

			// Leva o foco ao primeiro card novo (quem navega por teclado
			// continua de onde parou), sem pular a tela.
			var novo = grade.children[antes];
			Array.prototype.slice.call(grade.children, antes).forEach(function (card) {
				card.classList.add('is-novo');
			});

			if (novo && typeof novo.focus === 'function') {
				novo.focus({ preventScroll: true });
			}
		} else {
			grade.innerHTML = dados.cards || '';
		}

		total.textContent = (dados.total || 0).toLocaleString('pt-BR');
		resumo.textContent = dados.resumo || '';
		mostrando.textContent = dados.mostrando || '';
		progresso.style.width = (dados.progresso || 0) + '%';
		paginacao.hidden = !dados.total;

		if (mais) {
			mais.hidden = !dados.temMais;
			mais.href = urlMais();
		}

		atualizarUrl();
	}

	// href do "Carregar mais" (abre em nova aba / sem JS): estado + 1 página.
	function urlMais() {
		var params = new URLSearchParams();

		Array.prototype.forEach.call(form.querySelectorAll('input[type="checkbox"]:checked'), function (c) {
			params.append(c.name, c.value);
		});

		params.set('pagina', String(pagina + 1));

		return form.getAttribute('action') + '?' + params.toString();
	}

	/* ------------------------------------------------------------------
	 * Eventos
	 * ---------------------------------------------------------------- */

	form.addEventListener('change', function (e) {
		if (e.target.matches('input[type="checkbox"]')) {
			sincronizarAbas();
			contarFiltros();
			buscar(false);
		}
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		buscar(false);
	});

	abas.forEach(function (aba) {
		aba.addEventListener('click', function (e) {
			e.preventDefault();

			var slug = aba.dataset.erAba;

			caixas('categoria').forEach(function (c) {
				c.checked = slug !== '' && c.value === slug;
			});

			sincronizarAbas();
			contarFiltros();
			buscar(false);
		});
	});

	if (limpar) {
		limpar.addEventListener('click', function (e) {
			e.preventDefault();

			Array.prototype.forEach.call(form.querySelectorAll('input[type="checkbox"]'), function (c) {
				c.checked = false;
			});

			sincronizarAbas();
			contarFiltros();
			buscar(false);
		});
	}

	if (mais) {
		mais.addEventListener('click', function (e) {
			// Ctrl/Cmd+clique abre o link (?pagina=N) em outra aba, como link.
			if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) {
				return;
			}

			e.preventDefault();

			if (mais.getAttribute('aria-disabled') !== 'true') {
				buscar(true);
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Painel de filtros recolhível (< 1280px). No Desktop fica sempre aberto.
	 * ---------------------------------------------------------------- */

	function abrirPainel(abrir) {
		painel.classList.toggle('is-aberto', abrir);
		botaoFiltros.setAttribute('aria-expanded', abrir ? 'true' : 'false');
	}

	if (painel && botaoFiltros) {
		botaoFiltros.hidden = false;
		form.classList.add('tem-painel-recolhivel');

		botaoFiltros.addEventListener('click', function () {
			var abrir = !painel.classList.contains('is-aberto');
			abrirPainel(abrir);

			if (abrir && !desktop.matches) {
				painel.scrollIntoView({ behavior: reduzir.matches ? 'auto' : 'smooth', block: 'nearest' });
			}
		});
	}

	sincronizarAbas();
	contarFiltros();
})();
