import Chart from 'chart.js/auto';

// NOTA: o Alpine é fornecido pelo Livewire (@livewireScripts). Não importar
// 'alpinejs' aqui nem chamar Alpine.start() — isso criava uma segunda
// instância ("Detected multiple instances of Alpine running").

window.Chart = Chart;

/* ============================================================
 * FarmManager Charts — helper central `window.fm`
 * Paletas, formatação pt-BR, presets e suporte a dark mode.
 * Compatível com os scripts inline existentes (fm.make/setData).
 * ============================================================ */

const BRL = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
const BRL2 = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', minimumFractionDigits: 2 });
const NUM = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 });

const isDark = () => document.documentElement.classList.contains('dark');

const themeColors = () => (isDark()
    ? { grid: 'rgba(255,255,255,0.08)', tick: '#a8a29e', legend: '#d6d3d1' }
    : { grid: 'rgba(120,113,108,0.12)', tick: '#78716c', legend: '#57534e' });

Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.font.size = 11;
Chart.defaults.font.weight = 500;
Chart.defaults.maintainAspectRatio = false;
Chart.defaults.responsive = true;
// Mutação (nunca substituição do objeto): trocar a referência quebra
// a cadeia de fallback interna do Chart.js (animations.* herda de animation)
// e faz a animação de cores/gradientes lançar "this._fn is not a function".
Chart.defaults.animation.duration = 750;
Chart.defaults.animation.easing = 'easeOutQuart';
Chart.defaults.color = themeColors().tick;

/** Plugin global: mensagem elegante quando não há dados. */
Chart.register({
    id: 'fmEmpty',
    afterDraw(chart, _args, opts) {
        const sets = chart.data?.datasets ?? [];
        const hasData = sets.some((ds) => (ds.data ?? []).some((v) => Number(v) > 0));
        if (hasData || opts === false) return;
        const { ctx, chartArea } = chart;
        if (!chartArea) return;
        ctx.save();
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = isDark() ? '#78716c' : '#a8a29e';
        ctx.font = "700 13px Inter, system-ui, sans-serif";
        ctx.fillText('Sem dados para exibir', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2 - 8);
        ctx.font = "500 11px Inter, system-ui, sans-serif";
        ctx.fillText('Ajuste os filtros ou registre movimentações', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2 + 12);
        ctx.restore();
    },
});

const baseTip = {
    backgroundColor: '#1c1917',
    borderColor: 'rgba(255,255,255,0.08)',
    borderWidth: 1,
    padding: 12,
    cornerRadius: 12,
    caretSize: 6,
    displayColors: true,
    boxWidth: 8,
    boxHeight: 8,
    boxPadding: 4,
    usePointStyle: true,
    titleFont: { weight: '800', size: 12 },
    bodyFont: { weight: '600', size: 12 },
    footerFont: { weight: '500', size: 11 },
};

const doughnutPct = (c) => {
    const ds = c.dataset.data ?? [];
    const total = ds.reduce((a, b) => a + (Number(b) || 0), 0);
    if (!total) return '';
    return ` · ${((Number(c.parsed) / total) * 100).toFixed(1).replace('.', ',')}%`;
};

window.fm = {
    /* Paletas */
    palette: ['#336e3c', '#458a4e', '#67a76f', '#b08954', '#96744a', '#0ea5e9', '#f59e0b', '#8b5cf6', '#ef4444', '#14b8a6'],
    greens: ['#1d4a24', '#2c6134', '#336e3c', '#458a4e', '#67a76f', '#93c19a', '#bfe0c4'],
    earth: ['#336e3c', '#b08954', '#96744a', '#0ea5e9', '#f59e0b', '#8b5cf6'],
    status: { ok: '#336e3c', info: '#0ea5e9', warn: '#f59e0b', danger: '#ef4444', muted: '#a8a29e' },
    money: { in: '#336e3c', out: '#dc2626' },

    grid: themeColors().grid,
    tip: { ...baseTip },

    /* Tooltips prontos */
    moneyTip: {
        ...baseTip,
        callbacks: {
            label: (c) => ` ${c.dataset.label ? c.dataset.label + ': ' : ''}${BRL.format(c.parsed.y ?? c.parsed)}${['doughnut', 'pie'].includes(c.chart.config.type) ? doughnutPct(c) : ''}`,
        },
    },
    countTip: {
        ...baseTip,
        callbacks: {
            label: (c) => {
                const v = c.parsed.y ?? c.parsed;
                const base = ` ${c.dataset.label ? c.dataset.label + ': ' : ''}${NUM.format(v)}`;
                return ['doughnut', 'pie'].includes(c.chart.config.type) ? base + doughnutPct(c) : base;
            },
        },
    },
    unitTip(unit) {
        return {
            ...baseTip,
            callbacks: { label: (c) => ` ${NUM.format(c.parsed.y ?? c.parsed)}${unit ? ' ' + unit : ''}` },
        };
    },

    /* Formatação */
    brl: (v) => BRL.format(v ?? 0),
    brl2: (v) => BRL2.format(v ?? 0),
    num: (v) => NUM.format(v ?? 0),
    brlK: (v) => (v >= 1000 || v <= -1000 ? `R$ ${(v / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 1 })}k` : `R$ ${v}`),

    registry: {},

    theme() { return themeColors(); },
    dark() { return isDark(); },

    /* Gradiente vertical para áreas/linhas. */
    gradient(hex, from = 1, to = 0.25) {
        const h = hex.replace('#', '');
        const r = parseInt(h.slice(0, 2), 16), g = parseInt(h.slice(2, 4), 16), b = parseInt(h.slice(4, 6), 16);
        return (ctx) => {
            const { chart } = ctx;
            const { ctx: c, chartArea } = chart;
            if (!chartArea) return hex;
            const gr = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
            gr.addColorStop(0, `rgba(${r},${g},${b},${from})`);
            gr.addColorStop(1, `rgba(${r},${g},${b},${to})`);
            return gr;
        };
    },

    /* Plugin inline: total no centro do doughnut. */
    center(main, sub = 'total') {
        return {
            id: 'fmCenter',
            afterDraw(chart) {
                const { ctx, chartArea } = chart;
                if (!chartArea) return;
                const x = (chartArea.left + chartArea.right) / 2;
                const y = (chartArea.top + chartArea.bottom) / 2;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = isDark() ? '#fafaf9' : '#1c1917';
                ctx.font = "800 17px Inter, system-ui, sans-serif";
                ctx.fillText(main, x, y - 8);
                ctx.fillStyle = isDark() ? '#a8a29e' : '#78716c';
                ctx.font = "600 10px Inter, system-ui, sans-serif";
                ctx.fillText(sub, x, y + 12);
                ctx.restore();
            },
        };
    },

    /* Cria/substitui gráfico (compatível com scripts existentes). */
    make(id, cfg) {
        const el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') return null;
        if (this.registry[id]) { try { this.registry[id].destroy(); } catch (e) {} }
        const t = themeColors();
        cfg.options = cfg.options ?? {};
        cfg.options.plugins = cfg.options.plugins ?? {};
        cfg.options.plugins.legend = cfg.options.plugins.legend ?? {};
        if (cfg.options.plugins.legend.labels && !cfg.options.plugins.legend.labels.color) {
            cfg.options.plugins.legend.labels.color = t.legend;
        }
        this.registry[id] = new Chart(el, cfg);
        return this.registry[id];
    },

    destroy(id) {
        if (this.registry[id]) { try { this.registry[id].destroy(); } catch (e) {} delete this.registry[id]; }
    },

    setData(id, labels, values, label) {
        const ch = this.registry[id];
        if (!ch) return;
        ch.data.labels = labels;
        ch.data.datasets.forEach((ds, i) => {
            ds.data = Array.isArray(values) && Array.isArray(values[0]) ? values[i] : values;
            if (label && i === 0 && !ds.label) ds.label = label;
        });
        ch.update();
    },

    /* ---- Presets ---- */

    /** Linha/área multi-série (ex.: fluxo de caixa). series: [{label,data,color,fill}] */
    area(id, labels, series, { money = false, tension = 0.42, legend = false } = {}) {
        const t = themeColors();
        const fmt = money ? (v) => this.brlK(v) : (v) => NUM.format(v);
        return this.make(id, {
            type: 'line',
            data: {
                labels,
                datasets: series.map((s) => ({
                    label: s.label,
                    data: s.data,
                    borderColor: s.color,
                    backgroundColor: this.gradient(s.color, 0.3, 0),
                    fill: s.fill ?? true,
                    tension,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: s.color,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    borderWidth: 2.5,
                })),
            },
            options: {
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: legend
                        ? { display: true, position: 'bottom', labels: { boxWidth: 10, boxHeight: 8, padding: 14, usePointStyle: true, font: { size: 10, weight: 600 } } }
                        : { display: false },
                    tooltip: money ? this.moneyTip : this.tip,
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: t.tick, maxTicksLimit: 8 } },
                    y: { grid: { color: t.grid }, border: { display: false }, beginAtZero: true, ticks: { color: t.tick, maxTicksLimit: 6, callback: fmt } },
                },
            },
        });
    },

    /** Barras verticais. */
    bars(id, labels, values, { colors = null, color = '#336e3c', money = false, suffix = '', thickness = 22 } = {}) {
        const t = themeColors();
        const fmt = money ? (v) => this.brlK(v) : (v) => `${NUM.format(v)}${suffix}`;
        return this.make(id, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors ?? this.gradient(color, 1, 0.55),
                    hoverBackgroundColor: colors ?? color,
                    borderRadius: 8,
                    borderSkipped: 'start',
                    barThickness: thickness,
                    maxBarThickness: 34,
                }],
            },
            options: {
                plugins: { legend: { display: false }, tooltip: money ? this.moneyTip : suffix ? this.unitTip(suffix.trim()) : this.tip },
                scales: {
                    x: { grid: { display: false }, ticks: { color: t.tick } },
                    y: { grid: { color: t.grid }, border: { display: false }, beginAtZero: true, ticks: { color: t.tick, maxTicksLimit: 6, callback: fmt } },
                },
            },
        });
    },

    /** Barras horizontais (ranking). */
    hbars(id, labels, values, { colors = null, color = '#336e3c', money = false, suffix = '' } = {}) {
        const t = themeColors();
        const fmt = money ? (v) => this.brlK(v) : (v) => `${NUM.format(v)}${suffix}`;
        return this.make(id, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors ?? this.gradient(color, 1, 0.55),
                    hoverBackgroundColor: colors ?? color,
                    borderRadius: 8,
                    borderSkipped: 'start',
                    barThickness: 18,
                    maxBarThickness: 30,
                }],
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: money ? this.moneyTip : suffix ? this.unitTip(suffix.trim()) : this.tip },
                scales: {
                    x: { grid: { color: t.grid }, border: { display: false }, beginAtZero: true, ticks: { color: t.tick, maxTicksLimit: 6, callback: fmt } },
                    y: { grid: { display: false }, ticks: { color: t.tick } },
                },
            },
        });
    },

    /** Doughnut com % no tooltip e opcional total no centro. */
    doughnut(id, labels, values, { colors = null, money = false, center = null, centerSub = 'total', cutout = '68%' } = {}) {
        const nums = (values ?? []).map((v) => Number(v) || 0);
        const total = nums.reduce((a, b) => a + b, 0);
        const cfg = {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: nums,
                    backgroundColor: colors ?? this.palette,
                    hoverOffset: 8,
                    borderWidth: 3,
                    borderColor: isDark() ? '#1c1917' : '#ffffff',
                    spacing: 1,
                }],
            },
            options: {
                cutout,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 8, padding: 14, usePointStyle: true, font: { size: 10, weight: 600 } } },
                    tooltip: money ? this.moneyTip : this.countTip,
                },
            },
        };
        if (center !== null) cfg.plugins = [this.center(center, centerSub)];
        else if (total > 0) cfg.plugins = [this.center(money ? this.brl(total) : NUM.format(total), money ? 'total' : centerSub)];
        return this.make(id, cfg);
    },

    /** Reaplica tema (dark/light) em todos os gráficos registrados. */
    applyTheme() {
        const t = themeColors();
        this.grid = t.grid;
        Chart.defaults.color = t.tick;
        Object.values(this.registry).forEach((ch) => {
            try {
                const o = ch.options;
                if (o.scales) {
                    Object.values(o.scales).forEach((s) => {
                        if (s.grid && s.grid.display !== false) s.grid.color = t.grid;
                        if (s.ticks) s.ticks.color = t.tick;
                    });
                }
                if (o.plugins?.legend?.labels) o.plugins.legend.labels.color = t.legend;
                ch.update('none');
            } catch (e) {}
        });
    },
};

/* Re-tema os gráficos ao alternar dark/light. */
new MutationObserver((muts) => {
    if (muts.some((m) => m.attributeName === 'class')) window.fm?.applyTheme();
}).observe(document.documentElement, { attributes: true });

// Registra a store de toasts no Alpine do Livewire assim que ele inicializa.
document.addEventListener('alpine:init', () => {
    window.Alpine.store('toast', {
        items: [],
        push(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });
});

window.addEventListener('notify', (e) => {
    try {
        window.Alpine.store('toast').push(e.detail.message, e.detail.type ?? 'success');
    } catch (err) {
        // Alpine ainda não inicializou; ignora silenciosamente.
    }
});

document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('open-global-search'));
    }
});
