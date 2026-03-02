@extends('partials.header')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="bg-gray-50 min-h-screen">
    <!-- Top bar (no sidebar) -->
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500">Tracking overview</p>
            </div>

            <div class="flex items-center gap-2">
                <!-- <button class="px-3 py-2 text-sm rounded-lg border bg-white hover:bg-gray-50">
                    Export
                </button> -->
                <a href="/transaction" class="px-3 py-2 text-sm rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                    New Transaction
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6"
          x-data="dashboardCharts()"
          x-init="init()">

        <!-- KPI cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border p-4">
                <p class="text-sm text-gray-500">Total Transactions</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" x-text="kpi.total"></p>
                <p class="text-xs text-gray-500 mt-2">All-time</p>
            </div>

            <div class="bg-white rounded-xl border p-4">
                <p class="text-sm text-gray-500">Active Deliveries</p>
                <p class="text-2xl font-bold mt-1" x-text="kpi.active"></p>
                <p class="text-xs text-gray-500 mt-2">Currently in transit</p>
            </div>

            <div class="bg-white rounded-xl border p-4">
                <p class="text-sm text-gray-500">Completed Today</p>
                <p class="text-2xl font-bold mt-1" x-text="kpi.completedToday"></p>
                <p class="text-xs text-gray-500 mt-2">Based on today’s logs</p>
            </div>

            <div class="bg-white rounded-xl border p-4">
                <p class="text-sm text-gray-500">Available Drivers</p>
                <p class="text-2xl font-bold mt-1" x-text="kpi.availableDrivers"></p>
                <p class="text-xs text-gray-500 mt-2">Ready for assignment</p>
            </div>
        </section>

        <!-- Charts row -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
           <!-- Line chart -->
            <div class="bg-white rounded-xl border p-4 lg:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h2 class="font-semibold text-gray-900">Vehicle Trips</h2>
                        <p class="text-sm text-gray-500">Most used vehicles</p>
                    </div>
                </div>
                <div id="chartVehiclePie" class="w-full"></div>
            </div>

            <!-- Donut chart -->
            <div class="bg-white rounded-xl border p-4">
                <div class="mb-2">
                    <h2 class="font-semibold text-gray-900">Status Breakdown</h2>
                    <p class="text-sm text-gray-500">All transactions</p>
                </div>
                <div id="chartStatusDonut" class="w-full"></div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Pending</span>
                        <span class="font-semibold" x-text="status.pending"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Active</span>
                        <span class="font-semibold" x-text="status.active"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Completed</span>
                        <span class="font-semibold" x-text="status.completed"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Cancelled</span>
                        <span class="font-semibold" x-text="status.cancelled"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bar chart + table -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Bar chart -->
            <div class="bg-white rounded-xl border p-4">
                <div class="mb-2">
                    <h2 class="font-semibold text-gray-900">Driver Trips</h2>
                    <p class="text-sm text-gray-500">Most active drivers</p>
                </div>
                <div id="chartDriverBar" class="w-full"></div>
            </div>

            <!-- Recent trips table -->
            <div class="bg-white rounded-xl border p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="font-semibold text-gray-900">Recent Trips</h2>
                        <p class="text-sm text-gray-500">Latest activity</p>
                    </div>
                    <a href="/transaction" class="text-sm font-medium text-gray-900 hover:underline">View all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-gray-500 border-b">
                            <tr>
                                <th class="py-2 pr-2">Code</th>
                                <th class="py-2 pr-2">Driver</th>
                                <th class="py-2 pr-2">Drop-off</th>
                                <th class="py-2 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($transactions->take(3) as $transaction)
                            <tr>
                                <td class="py-2 pr-2 font-medium text-gray-900">{{ $transaction->transaction_code }}</td>
                                
                                <td class="py-2 pr-2 text-gray-700">{{ $transaction->dropoff_location }}</td>
                                <td class="py-2 text-right">
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">{{ $transaction->status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
function dashboardCharts() {

    const vehicleLabels = @json($topVehicles->pluck('plate_number'));
    const vehicleTrips  = @json($topVehicles->pluck('trips'));

    const driverLabels  = @json($topDrivers->map(fn($d) => $d->first_name.' '.$d->last_name));
    const driverTrips   = @json($topDrivers->pluck('trips'));

    const total_transaction = @json($transactions).length;
    const total_active = @json($totalActive);
    const total_completed = @json($totalComplete);
    const total_pending = @json($totalPending);
    const total_cancelled = @json($totalCancelled);
    const availableDrivers = @json($availableDrivers);

    return {
        kpi: { 
            total: total_transaction, 
            active: total_active, 
            completedToday: total_completed, 
            availableDrivers: availableDrivers 
        },

        status: { 
            pending: total_pending, 
            active: total_active, 
            completed: total_completed, 
            cancelled: total_cancelled 
        },

        init() {
            this.renderVehiclePie()
            this.renderDonut()
            this.renderDriverBar()
        },

        // 🔵 VEHICLE PIE CHART
        renderVehiclePie() {
            const options = {
                chart: { type: 'pie', height: 280 },
                series: vehicleTrips,
                labels: vehicleLabels,
                legend: { position: 'bottom' },
                dataLabels: { enabled: true },
                colors: ['#3B82F6', '#10B981', '#F59E0B']
            }

            const el = document.querySelector('#chartVehiclePie')
            if (el) new ApexCharts(el, options).render()
        },

        // 🟡 STATUS DONUT (UNCHANGED)
        renderDonut() {
            const options = {
                chart: { type: 'donut', height: 280 },
                series: [
                    this.status.pending,
                    this.status.active,
                    this.status.completed,
                    this.status.cancelled
                ],
                labels: ['Pending', 'Active', 'Completed', 'Cancelled'],
                legend: { position: 'bottom' },
            }

            const el = document.querySelector('#chartStatusDonut')
            if (el) new ApexCharts(el, options).render()
        },

        // 🟣 DRIVER BAR CHART
        renderDriverBar() {
            const options = {
                chart: { type: 'bar', height: 280, toolbar: { show: false } },
                series: [{ name: 'Trips', data: driverTrips }],
                xaxis: { categories: driverLabels },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '50%'
                    }
                },
                dataLabels: { enabled: false },
                colors: ['#6366F1'],
                grid: { strokeDashArray: 4 }
            }

            const el = document.querySelector('#chartDriverBar')
            if (el) new ApexCharts(el, options).render()
        },
    }
}
</script>
@endsection