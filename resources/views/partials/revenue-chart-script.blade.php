<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalRevenueEl = document.querySelector('#{{ $chartId }}');

        if (totalRevenueEl) {
            const totalRevenueData = @json($chartTotalRevenue['series']);
            const totalRevenueLabels = @json($chartTotalRevenue['labels']);
            const cardColor = config.colors.cardColor;
            const borderColor = config.colors.borderColor;
            const labelColor = config.colors.textMuted;
            const legendColor = config.colors.bodyColor;
            const fontFamily = config.fontFamily;
            const formatRupiah = function (value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(value));
            };
            const formatRupiahCompact = function (value) {
                const formatted = new Intl.NumberFormat('id-ID', {
                    notation: 'compact',
                    maximumFractionDigits: 1
                }).format(Math.abs(value));

                return (value < 0 ? '-Rp ' : 'Rp ') + formatted;
            };

            new ApexCharts(totalRevenueEl, {
                series: totalRevenueData,
                chart: {
                    height: 300,
                    stacked: true,
                    type: 'bar',
                    toolbar: { show: false },
                    fontFamily: fontFamily
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 8,
                        startingShape: 'rounded',
                        endingShape: 'rounded',
                        borderRadiusApplication: 'around'
                    }
                },
                colors: [config.colors.primary, config.colors.info],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    lineCap: 'round',
                    colors: [cardColor]
                },
                legend: {
                    show: true,
                    horizontalAlign: 'left',
                    position: 'top',
                    markers: {
                        size: 4,
                        radius: 12,
                        shape: 'circle',
                        strokeWidth: 0
                    },
                    fontSize: '13px',
                    fontFamily: fontFamily,
                    fontWeight: 400,
                    labels: {
                        colors: legendColor,
                        useSeriesColors: false
                    },
                    itemMargin: {
                        horizontal: 10
                    }
                },
                grid: {
                    strokeDashArray: 7,
                    borderColor: borderColor,
                    padding: {
                        top: 0,
                        bottom: -8,
                        left: 20,
                        right: 20
                    }
                },
                fill: { opacity: [1, 1] },
                xaxis: {
                    categories: totalRevenueLabels,
                    labels: {
                        style: {
                            fontSize: '13px',
                            fontFamily: fontFamily,
                            colors: labelColor
                        }
                    },
                    axisTicks: { show: false },
                    axisBorder: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: formatRupiahCompact,
                        style: {
                            fontSize: '13px',
                            fontFamily: fontFamily,
                            colors: labelColor
                        }
                    }
                },
                tooltip: {
                    y: { formatter: formatRupiah }
                },
                states: {
                    hover: { filter: { type: 'none' } },
                    active: { filter: { type: 'none' } }
                }
            }).render();
        }

        const growthEl = document.querySelector('#{{ $growthId }}');

        if (growthEl) {
            const growthLabel = @json($chartTotalRevenue['growth_label']);
            const growthTone = @json($chartTotalRevenue['growth_tone']);
            const growthColor = config.colors[growthTone] || config.colors.secondary;
            const legendColor = config.colors.bodyColor;
            const headingColor = config.colors.headingColor;
            const fontFamily = config.fontFamily;

            new ApexCharts(growthEl, {
                series: [{{ $chartTotalRevenue['growth_arc'] }}],
                labels: ['Pertumbuhan'],
                chart: {
                    height: 200,
                    type: 'radialBar'
                },
                plotOptions: {
                    radialBar: {
                        size: 150,
                        offsetY: 10,
                        startAngle: -150,
                        endAngle: 150,
                        hollow: { size: '55%' },
                        track: {
                            background: config.colors.borderColor,
                            strokeWidth: '100%'
                        },
                        dataLabels: {
                            name: {
                                offsetY: 15,
                                color: legendColor,
                                fontSize: '15px',
                                fontWeight: '500',
                                fontFamily: fontFamily
                            },
                            value: {
                                offsetY: -25,
                                color: headingColor,
                                fontSize: '22px',
                                fontWeight: '500',
                                fontFamily: fontFamily,
                                formatter: function () {
                                    return growthLabel;
                                }
                            }
                        }
                    }
                },
                colors: [growthColor],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        shadeIntensity: 0.5,
                        gradientToColors: [growthColor],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 0.6,
                        stops: [30, 70, 100]
                    }
                },
                stroke: { dashArray: 5 },
                grid: {
                    padding: {
                        top: -35,
                        bottom: -10
                    }
                },
                states: {
                    hover: { filter: { type: 'none' } },
                    active: { filter: { type: 'none' } }
                }
            }).render();
        }
    });
</script>
