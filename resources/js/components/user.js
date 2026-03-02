export default () => ({
    open: false, 
    errors: {},
    loading: false,

    // table
    users: [],
    search: '',
    page: 1,
    perPage: 10,
    meta: { current_page: 1, last_page: 1, total: 0 },

    async init() {
        await this.fetchUsers();
        this.renderRows();

        const searchEl = document.getElementById('userSearch');
        const btnEl = document.getElementById('btnUserSearch');

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

    async fetchUsers() {
        this.loading = true;
        try {
            const url = `/user/data?search=${encodeURIComponent(this.search)}&page=${this.page}&per_page=${this.perPage}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            const json = await res.json();

            this.users = json.data || [];
            this.meta = json.meta || { current_page: 1, last_page: 1, total: 0 };
        } finally {
            this.loading = false;
        }
    },

    async refresh() {
        await this.fetchUsers();
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
        const tbody = document.querySelector('#userTable tbody');
        if (!tbody) return;

        tbody.innerHTML = this.users.map(u => `
        <tr class="bg-white hover:bg-gray-50">
            <td class="px-6 py-4 font-semibold text-gray-900">${u.name}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${u.email}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${u.role}</td>
            <td class="px-6 py-4 text-right">
            <a href="/user/edit/${u.id}"
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

        const response = await fetch('/store', {
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
