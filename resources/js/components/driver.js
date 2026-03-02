export default () => ({
    open: false, 
    errors: {},
    loading: false,

    // table
    drivers: [],
    search: '',
    page: 1,
    perPage: 10,
    meta: { current_page: 1, last_page: 1, total: 0 },

    async init() {
        await this.fetchDrivers();
        this.renderRows();

        const searchEl = document.getElementById('driverSearch');
        const btnEl = document.getElementById('btnDriverSearch');

        if (searchEl) {
        searchEl.addEventListener('input', (e) => {
            this.search = e.target.value;
            this.page = 1;
            this.refresh();
        });
        }
        if (btnEl) {
        btnEl.addEventListener('click', () => {
            this.page = 1;
            this.refresh();
        });
        }
    },

    async fetchDrivers() {
        this.loading = true;
        try {
            const url = `/driver/data?search=${encodeURIComponent(this.search)}&page=${this.page}&per_page=${this.perPage}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            const json = await res.json();

            this.drivers = json.data || [];
            this.meta = json.meta || { current_page: 1, last_page: 1, total: 0 };
        } finally {
            this.loading = false;
        }
    },

    async refresh() {
        await this.fetchDrivers();
        this.renderRows();
    },

    async goPage(p) {
        if (p < 1 || p > this.meta.last_page) return;
        this.page = p;
        await this.refresh();
    },

    badgeClass(status) {
        const s = (status || '').toLowerCase();
        if (s === 'available') return 'bg-emerald-100 text-emerald-700';
        if (['on_delivery', 'in_use', 'in use'].includes(s)) return 'bg-sky-100 text-sky-700';
        if (s === 'assigned') return 'bg-amber-100 text-amber-800';
        return 'bg-gray-100 text-gray-700';
    },

    renderRows() {
        const tbody = document.querySelector('#driverTable tbody');
        if (!tbody) return;

        tbody.innerHTML = this.drivers.map(d => `
        <tr class="bg-white hover:bg-gray-50">
            <td class="px-6 py-4 font-semibold text-gray-900">${d.first_name}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${d.last_name}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${d.address}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${d.license_number}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${d.phone}</td>
            <td class="px-6 py-4">
                <span class="text-xs font-bold px-3 py-1 rounded-full ${this.badgeClass(d.status)}">${d.status}</span>
            </td>
            <td class="px-6 py-4 text-right">
            <a href="/driver/edit/${d.id}"
                class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl bg-sky-600 text-white hover:bg-sky-500">
                View
            </a>
            </td>
        </tr>
        `).join('');
    },

    async submit(form) {
        this.loading = true
        this.errors = {}

        const response = await fetch('/add_driver', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })

        this.loading = false
        
        if (response.status === 422) {
            const data = await response.json()
            this.errors = data.errors
            return
        }

        if (response.ok) {
            this.open = false
            form.reset()
            window.location.reload() // or update table dynamically
        }
    },
})
