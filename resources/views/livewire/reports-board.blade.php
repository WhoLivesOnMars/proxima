<div
    x-data="reportsDashboard(@js($barData), @js($doughnutData))"
    x-init="init()"
    class="space-y-6"
>
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <div class="relative" x-data="{ open:false }">
                <button type="button"
                        class="inline-flex items-center gap-1 text-lg font-semibold text-slate-900 focus:outline-none"
                        @click="open = !open">
                    {{ $currentProject?->nom ?? 'Select project' }}
                    <svg class="w-4 h-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open"
                     x-transition
                     @click.outside="open = false"
                     class="absolute mt-2 w-56 bg-white rounded-md shadow border z-30">
                    @foreach($projects as $proj)
                        <button type="button"
                                wire:click="$set('currentProjectId', {{ $proj->id_projet }})"
                                @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100 {{ $currentProject && $currentProject->id_projet === $proj->id_projet ? 'font-semibold' : '' }}">
                            {{ $proj->nom }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="text-sm text-slate-500">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="text-center text-sm font-semibold text-slate-900 mb-4">
                Project progress statistics
            </div>
            <div class="h-72">
                <canvas x-ref="barChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="text-center text-sm font-semibold text-slate-900 mb-4">
                Pie chart by status
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas x-ref="pieChart" class="max-h-64 max-w-64"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">To do</div>
            <div class="text-lg font-semibold text-slate-900">
                {{ $stats['to_do'] }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">In progress</div>
            <div class="text-lg font-semibold text-slate-900">
                {{ $stats['in_progress'] }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">Done</div>
            <div class="text-lg font-semibold text-slate-900">
                {{ $stats['done'] }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">Overdue</div>
            <div class="text-lg font-semibold text-rose-600">
                {{ $stats['overdue'] }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function reportsDashboard(barData, doughnutData) {
            return {
                barData,
                doughnutData,
                barChart: null,
                pieChart: null,

                init() {
                    const barCtx = this.$refs.barChart.getContext('2d');
                    const pieCtx = this.$refs.pieChart.getContext('2d');

                    this.barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: this.barData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        borderDash: [4, 4],
                                        color: '#e5e7eb'
                                    },
                                    ticks: {
                                        stepSize: 5,
                                        font: { size: 10 }
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });

                    this.pieChart = new Chart(pieCtx, {
                        type: 'doughnut',
                        data: this.doughnutData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });

                    window.addEventListener('reports-updated', (event) => {
                        this.updateCharts(event.detail.barData, event.detail.doughnutData);
                    });
                },

                updateCharts(barData, doughnutData) {
                    if (this.barChart) {
                        this.barChart.data = barData;
                        this.barChart.update();
                    }
                    if (this.pieChart) {
                        this.pieChart.data = doughnutData;
                        this.pieChart.update();
                    }
                }
            }
        }
    </script>
@endpush
