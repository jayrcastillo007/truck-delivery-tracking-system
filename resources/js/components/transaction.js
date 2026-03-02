export default () => ({
    activeModal: null,
    errors: {},
    loading: false,

    // Drivers & Vehicles
    drivers: [],
    selectedDriver: '',
    vehicles: [],
    selectedVehicle: '',

    // Transaction data
    selectedTransaction: {
        id: '',
        customer_name: '',
        pickup_location: '',
        dropoff_location: '',
        cargo_details: '',
    },

    successMessage: '',

    pickupAddress:'',
    pickupLat:'',
    pickupLong:'',

    dropoffAddress:'',
    dropoffLat: '',
    dropoffLong: '',

    // table
    transactions: [],
    search: '',
    page: 1,
    perPage: 10,
    meta: { current_page: 1, last_page: 1, total: 0 },

    // ================= MAP LOGIC =================

    

    async init() {

        await this.fetchTransactions();
        this.renderRows();

        const searchEl = document.getElementById('transactionSearch');
        const btnEl = document.getElementById('btnTransactionSearch');

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

        // console.log(this.transactions);

        try {
            const [driversRes, vehiclesRes] = await Promise.all([
                fetch('/drivers_list'),
                fetch('/vehicles_list'),
                // fetch('/province_list') 
            ])

            this.drivers = await driversRes.json()
            this.vehicles = await vehiclesRes.json()
            // this.provinces = await provincesRes.json()
            // console.log(this.vehicles)
        } catch (error) {
            console.error('Failed to load data', error)
        }

    },

        //TABLE DATA

    async fetchTransactions() {
        this.loading = true;
        try {
            const url = `/transaction/data?search=${encodeURIComponent(this.search)}&page=${this.page}&per_page=${this.perPage}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            const json = await res.json();

            this.transactions = json.data || [];
            this.meta = json.meta || { current_page: 1, last_page: 1, total: 0 };
        } finally {
            this.loading = false;
        }
    },

    async refresh() {
        await this.fetchTransactions();
        this.renderRows();
    },

    async goPage(p) {
        if (p < 1 || p > this.meta.last_page) return;
        this.page = p;
        await this.refresh();
    },

    badgeClass(status) {
        const s = (status || '').toLowerCase();
        if (s === 'completed') return 'bg-emerald-100 text-emerald-700';
        if (['in_transit', 'in_transit', 'in transit'].includes(s)) return 'bg-sky-100 text-sky-700';
        if (s === 'scheduled') return 'bg-amber-100 text-amber-800';
        return 'bg-gray-100 text-gray-700';
    },

    renderRows() {
        const tbody = document.querySelector('#transactionTable tbody');
        if (!tbody) return;

        tbody.innerHTML = this.transactions.map(t => `
        <tr class="bg-white hover:bg-gray-50">
            <td class="px-6 py-4 font-semibold text-gray-900">${t.transaction_code }</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.customer_name}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.pickup_location}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.dropoff_location}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.cargo_details}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.vehicle?.vehicle_type ?? 'N/A'}</td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.driver?.first_name ?? 'N/A'}</td>
            <td class="px-6 py-4">
                <span class="text-xs font-bold px-3 py-1 rounded-full ${this.badgeClass(t.status)}">${t.status}</span>
            </td>
            <td class="px-6 py-4 font-semibold text-gray-900">${t.user?.name ?? 'N/A'}</td>
           
            <td class="px-6 py-4">
                <div class="flex justify-end gap-2">
                ${
                    (t.status || '').toLowerCase() === 'pending'
                    ? `<button type="button"
                            class="bg-sky-600 text-white rounded px-3 py-1"
                            data-action="schedule"
                            data-id="${t.id}">
                        Schedule
                        </button>`
                    : ''
                }
                <a href="/transaction/information/${t.id}"
                    class="bg-gray-600 text-white rounded px-3 py-1">
                    View
                </a>
                </div>
            </td>
        </tr>
        `).join('');

        this.bindRowActions();
    },

    openSchedule(t) {
        this.activeModal = 'schedule';
        this.selectedTransaction = {
            id: t.id,
            customer_name: t.customer_name,
            pickup_location: t.pickup_location,
            dropoff_location: t.dropoff_location,
            cargo_details: t.cargo_details,
        };
    },

    bindRowActions() {
        const tbody = document.querySelector('#transactionTable tbody');
        if (!tbody) return;

        // prevent duplicate listener
        if (this._actionsBound) return;
        this._actionsBound = true;

        tbody.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action="schedule"]');
            if (!btn) return;

            const id = Number(btn.dataset.id);
            const t = this.transactions.find(x => x.id === id);
            if (!t) return;

            this.openSchedule(t);
        });
    },


        //TABLE DATA END


        //Pickup Map
    initPickupMap() {
        // prevent double init
        if (this.pickupMap) return

        this.pickupMap = L.map('pickupMap').setView([14.5995, 120.9842], 13)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.pickupMap)

        // click to pin
        this.pickupMap.on('click', (e) => {
            this.setPickupMarker(e.latlng.lat, e.latlng.lng)
        })
    },

    async geocodePickup() {
        // console.log(this.pickupAddress);
        if (!this.pickupAddress) return

        const res = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.pickupAddress)}`
        )
        const data = await res.json()

        if (!data.length) {
            alert('Location not found. Please refine the address.')
            return
        }

        const lat = parseFloat(data[0].lat)
        const lng = parseFloat(data[0].lon)

        this.pickupMap.setView([lat, lng], 16)
        this.setPickupMarker(lat, lng)
    },

    setPickupMarker(lat, lng) {
        this.pickupLat = lat
        this.pickupLong = lng

        if (this.pickupMarker) {
            this.pickupMarker.setLatLng([lat, lng])
        } else {
            this.pickupMarker = L.marker([lat, lng], { draggable: true })
                .addTo(this.pickupMap)

            this.pickupMarker.on('dragend', (e) => {
                const pos = e.target.getLatLng()
                this.pickupLat = pos.lat
                this.pickupLong = pos.lng
            })
        }
    },

        //Drop-off Map
    initDropoffMap() {
        // prevent double init
        if (this.dropoffMap) return

        this.dropoffMap = L.map('dropoffMap').setView([14.5995, 120.9842], 13)

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(this.dropoffMap)

        // click to pin
        this.dropoffMap.on('click', (e) => {
            this.setDropoffMarker(e.latlng.lat, e.latlng.lng)
        })
    },

    async geocodeDropoff() {
        // console.log(this.pickupAddress);
        if (!this.dropoffAddress) return

        const res = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.dropoffAddress)}`
        )
        const data = await res.json()

        if (!data.length) {
            alert('Location not found. Please refine the address.')
            return
        }

        const lat = parseFloat(data[0].lat)
        const lng = parseFloat(data[0].lon)

        this.dropoffMap.setView([lat, lng], 16)
        this.setDropoffMarker(lat, lng)
    },

    setDropoffMarker(lat, lng) {
        this.dropoffLat = lat
        this.dropoffLong = lng

        if (this.dropoffMarker) {
            this.dropoffMarker.setLatLng([lat, lng])
        } else {
            this.dropoffMarker = L.marker([lat, lng], { draggable: true })
                .addTo(this.dropoffMap)

            this.dropoffMarker.on('dragend', (e) => {
                const pos = e.target.getLatLng()
                this.dropoffLat = pos.lat
                this.dropoffLong = pos.lng
            })
        }
    },

    
    // CREATE BOOKING
    async submitCreate(form) {
        this.loading = true
        this.errors = {}

        const formData = new FormData(form)
        // console.log(pickupLat);
        const pickup_location = this.pickupAddress;
        const dropoff_location = this.dropoffAddress;

        // APPEND TO FORMDATA
        formData.append('pickup_location', pickup_location)
        formData.append('dropoff_location', dropoff_location)
        formData.append('pickup_lat', this.pickupLat)
        formData.append('pickup_long', this.pickupLong)
        formData.append('dropoff_lat', this.dropoffLat)
        formData.append('dropoff_long', this.dropoffLong)

        // SEND TO BACKEND
        const response = await fetch('/create_transaction', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        })

        this.loading = false

        if (response.status === 422) {
            this.errors = (await response.json()).errors
            return
        }

        if (response.ok) {
            this.activeModal = null
            form.reset()
            window.location.reload()
        }
    },


    // SCHEDULE TRANSACTION (unchanged)
    async submitSchedule(form) {
        this.loading = true
        this.errors = {}

        const response = await fetch('/schedule_transaction', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })

        this.loading = false

        if (response.status === 422) {
            this.errors = (await response.json()).errors
            return
        }

        if (response.ok) {
            const data = await response.json()
            this.successMessage = data.message
            this.activeModal = null

            setTimeout(() => window.location.reload(), 3000)
        }
    },
    
})
