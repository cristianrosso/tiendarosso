const productData = {
  docentech: {
    tab: 'tab-docentech',
    kicker: 'Para educación',
    title: 'Tecnología para acompañar el trabajo educativo.',
    description: 'Una plataforma tecnológica dirigida a docentes, centros educativos, instituciones y responsables de formación.',
    benefits: [
      'Una propuesta enfocada en el sector educativo',
      'Presentación clara para equipos e instituciones',
      'Demostración guiada antes de contratar'
    ],
    href: '/soluciones/docentech/',
    cta: 'Conocer Docentech',
    visual: `
      <div class="visual-label">Visual conceptual · P1</div>
      <div class="concept-ui concept-edu">
        <div class="concept-sidebar"><span>D</span><i></i><i></i><i></i><i></i></div>
        <div class="concept-main"><div class="concept-title"><span><small>Vista general</small><b>Espacio de trabajo</b></span><button tabindex="-1">＋ Crear</button></div><div class="concept-grid"><div class="concept-card card-chart"><b>Actividad</b><div class="line-chart"><i></i></div></div><div class="concept-card card-stack"><small>Próximo</small><b>Contenido reciente</b><span></span><span></span></div><div class="concept-card card-wide"><small>Resumen</small><div><i></i><i></i><i></i><i></i></div></div></div></div>
      </div>`
  },
  violet: {
    tab: 'tab-violet',
    kicker: 'Para conversaciones y gestión',
    title: 'Convierte conversaciones en oportunidades organizadas.',
    description: 'Violet Flow reúne en su propuesta WhatsApp Business, CRM, agentes de IA y marketing para empresas.',
    benefits: [
      'WhatsApp Business dentro de la propuesta SaaS',
      'CRM para organizar oportunidades',
      'Agentes de IA y marketing como capacidades declaradas'
    ],
    href: '/soluciones/violet-flow/',
    cta: 'Conocer Violet Flow',
    visual: `
      <div class="visual-label">Visual conceptual · P1</div>
      <div class="concept-ui concept-violet">
        <div class="concept-sidebar"><span style="background:#6927d8">V</span><i></i><i></i><i></i><i></i></div>
        <div class="concept-main"><div class="concept-title"><span><small>Bandeja visual</small><b>Conversaciones</b></span><button tabindex="-1" style="background:#6927d8">＋ Nuevo</button></div><div class="concept-grid"><div class="concept-card card-stack"><small>Contactos</small><b>Interacciones recientes</b><span></span><span></span><span></span></div><div class="concept-card card-chart"><b>Oportunidades</b><div class="line-chart"><i style="border-color:#6927d8"></i></div></div><div class="concept-card card-wide"><small>Flujo comercial</small><div><i style="background:#eadfff"></i><i></i><i style="background:#eadfff"></i><i></i></div></div></div></div>
      </div>`
  },
  rguilex: {
    tab: 'tab-rguilex',
    kicker: 'Para comercio electrónico',
    title: 'Tu propia tienda virtual, planteada como servicio.',
    description: 'Rguilex es una solución de comercio electrónico que se comercializará para empresas que buscan vender en su propio espacio digital.',
    benefits: [
      'Una propuesta dirigida a empresas que quieren vender online',
      'Tienda virtual presentada como servicio',
      'Alcance y contratación definidos antes de implementar'
    ],
    href: '/soluciones/rguilex/',
    cta: 'Conocer Rguilex',
    visual: `
      <div class="visual-label">Visual conceptual · P1</div>
      <div class="concept-ui concept-store">
        <div class="concept-sidebar"><span style="background:#f26a3d">R</span><i></i><i></i><i></i><i></i></div>
        <div class="concept-main"><div class="concept-title"><span><small>Panel de tienda</small><b>Catálogo visual</b></span><button tabindex="-1" style="background:#f26a3d">＋ Producto</button></div><div class="concept-grid"><div class="concept-card card-chart"><b>Vista comercial</b><div class="line-chart"><i style="border-color:#f26a3d"></i></div></div><div class="concept-card card-stack"><small>Catálogo</small><b>Productos</b><span></span><span></span><span style="background:#ffe4d8"></span></div><div class="concept-card card-wide"><small>Resumen de tienda</small><div><i style="background:#ffe4d8"></i><i></i><i></i><i style="background:#ffe4d8"></i></div></div></div></div>
      </div>`
  }
};

function initNavigation() {
  const header = document.querySelector('[data-header]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if (!header || !toggle || !nav) return;

  const closeNav = () => {
    toggle.setAttribute('aria-expanded', 'false');
    nav.classList.remove('is-open');
    document.body.classList.remove('nav-open');
  };

  toggle.addEventListener('click', () => {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isOpen));
    nav.classList.toggle('is-open', !isOpen);
    document.body.classList.toggle('nav-open', !isOpen);
  });

  nav.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeNav));
  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeNav();
  });
  window.addEventListener('scroll', () => header.classList.toggle('is-scrolled', window.scrollY > 80), { passive: true });
}

function initReveal() {
  const items = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window)) {
    items.forEach((item) => item.classList.add('is-visible'));
    return;
  }
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  items.forEach((item) => observer.observe(item));
}

function initExplorer() {
  const explorer = document.querySelector('[data-explorer]');
  if (!explorer) return;
  const tabs = [...explorer.querySelectorAll('[role="tab"]')];
  const panel = explorer.querySelector('[role="tabpanel"]');

  const selectProduct = (key, shouldFocus = false) => {
    const data = productData[key];
    if (!data) return;
    explorer.dataset.activeProduct = key;
    tabs.forEach((tab) => {
      const active = tab.dataset.product === key;
      tab.classList.toggle('is-active', active);
      tab.setAttribute('aria-selected', String(active));
      tab.tabIndex = active ? 0 : -1;
      if (active && shouldFocus) tab.focus();
    });
    panel.setAttribute('aria-labelledby', data.tab);
    panel.querySelector('[data-product-kicker]').textContent = data.kicker;
    panel.querySelector('[data-product-title]').textContent = data.title;
    panel.querySelector('[data-product-description]').textContent = data.description;
    panel.querySelector('[data-product-benefits]').innerHTML = data.benefits.map((item) => `<li>${item}</li>`).join('');
    const cta = panel.querySelector('[data-product-cta]');
    cta.href = data.href;
    cta.innerHTML = `${data.cta} <span aria-hidden="true">↗</span>`;
    panel.querySelector('[data-product-visual]').innerHTML = data.visual;
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => selectProduct(tab.dataset.product));
    tab.addEventListener('keydown', (event) => {
      if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'].includes(event.key)) return;
      event.preventDefault();
      let next = index;
      if (event.key === 'Home') next = 0;
      else if (event.key === 'End') next = tabs.length - 1;
      else if (event.key === 'ArrowRight' || event.key === 'ArrowDown') next = (index + 1) % tabs.length;
      else next = (index - 1 + tabs.length) % tabs.length;
      selectProduct(tabs[next].dataset.product, true);
    });
  });
}

function initDemoForms() {
  const params = new URLSearchParams(window.location.search);
  document.querySelectorAll('[data-demo-form]').forEach((form) => {
    ['utm_source', 'utm_medium', 'utm_campaign'].forEach((key) => {
      const field = form.elements.namedItem(key);
      if (field) field.value = params.get(key) || '';
    });
    const requestedSolution = params.get('solution');
    if (requestedSolution && form.elements.namedItem('solution')) {
      const select = form.elements.namedItem('solution');
      const match = [...select.options].find((option) => option.text.toLowerCase().replaceAll(' ', '-') === requestedSolution.toLowerCase());
      if (match) select.value = match.value;
    }

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const status = form.querySelector('[data-form-status]');
      status.className = 'form-status is-visible';
      if (!form.checkValidity()) {
        status.classList.add('is-error');
        status.textContent = 'Revisa los campos obligatorios y acepta el consentimiento para continuar.';
        form.reportValidity();
        return;
      }
      status.classList.add('is-success');
      status.textContent = 'Demostración completada: la interfaz funciona y ningún dato fue enviado ni almacenado.';
      form.querySelector('button[type="submit"]').textContent = 'Demostración completada ✓';
    });
  });
}

initNavigation();
initReveal();
initExplorer();
initDemoForms();

