$(document).ready(function () {
    // Delay loading untuk memastikan DOM siap
    setTimeout(function () {
        muatDataPendapatanHarian();
        MuatDataPendapatanPertahun();
        $("#tanggal").on("change", function () {
            muatDataPendapatanHarian();
        });
    }, 1000);
});

// ==================== FUNGSI MUAT DATA PENDAPATAN HARIAN ====================
function muatDataPendapatanHarian() {
    // Ambil value dari input date
    var tanggalVal = $("#tanggal").val(); // format: "2025-11-26"
    var bulan = null,
        tahun = null;
    if (tanggalVal) {
        var parts = tanggalVal.split("-");
        tahun = parts[0];
        bulan = parts[1];
    }

    $.ajax({
        url: "/static-data-pendapatan-harian",
        method: "GET",
        dataType: "json",
        data: {
            bulan: bulan,
            tahun: tahun,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                buatChartPendapatan(response.data);
            } else {
                $("#transaction-chart").html(
                    '<div class="text-center text-muted">Data tidak tersedia</div>'
                );
            }
        },
        error: function (xhr, status, error) {
            $("#transaction-chart").html(
                '<div class="text-center text-danger">Gagal memuat data</div>'
            );
            if (xhr.status === 401 || xhr.status === 500) {
                window.location.href = "/login";
            }
        },
    });
}

// ==================== FUNGSI MUAT DATA DISTRIBUSI PRODUK ====================
function MuatDataPendapatanPertahun() {
    $.ajax({
        url: "/static-data-pendapatan-pertahun",
        method: "GET",
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            // PERBAIKAN: Ambil data dari response.data.pendapatan_per_tahun
            if (
                response.success &&
                response.data &&
                response.data.pendapatan_per_tahun &&
                response.data.pendapatan_per_tahun.length > 0
            ) {
                buatChartPendapatanPertahun(response.data.pendapatan_per_tahun);
            } else {
                $("#distribusi-produk-chart")
                    .parent()
                    .html(
                        '<div class="text-center text-muted py-5">Data tidak tersedia</div>'
                    );
            }
        },
        error: function (xhr, status, error) {
            console.error("Error Ajax:", error);
            $("#distribusi-produk-chart")
                .parent()
                .html(
                    '<div class="text-center text-danger py-5">Gagal memuat data: ' +
                        error +
                        "</div>"
                );

            // Redirect ke login jika unauthorized atau server error
            if (xhr.status === 401 || xhr.status === 500) {
                window.location.href = "/login";
            }
        },
    });
}

// ==================== FUNGSI BUAT CHART DISTRIBUSI PRODUK ====================
function buatChartPendapatanPertahun(dataApi) {
    // Validasi canvas element
    var canvas = document.getElementById("distribusi-produk-chart");

    if (!canvas) {
        console.error(
            "ERROR: Canvas dengan id 'distribusi-produk-chart' tidak ditemukan!"
        );
        return;
    }

    var ctx = canvas.getContext("2d");

    // Hancurkan chart yang sudah ada
    const chartYangAda = Chart.getChart("distribusi-produk-chart");
    if (chartYangAda) {
        chartYangAda.destroy();
    }

    let labelChart = [];
    let dataChart = [];
    let warnaLatar = ["#F07124", "#FF5722"];

    // Proses data dari API
    if (Array.isArray(dataApi) && dataApi.length > 0) {
        dataApi.forEach(function (item, index) {
            if (
                item.tahun &&
                item.total_pendapatan !== null &&
                item.total_pendapatan !== undefined
            ) {
                labelChart.push("Tahun " + item.tahun.toString());
                dataChart.push(item.total_pendapatan);
            }
        });
    }

    // Validasi apakah ada data untuk ditampilkan
    if (labelChart.length === 0 || dataChart.length === 0) {
        console.error("ERROR: Tidak ada data valid untuk ditampilkan!");
        $("#distribusi-produk-chart")
            .parent()
            .html(
                '<div class="text-center text-muted py-5">Tidak ada data untuk ditampilkan</div>'
            );
        return;
    }

    var konfigurasiChart = {
        labels: labelChart,
        datasets: [
            {
                label: "Pendapatan Per Tahun",
                data: dataChart,
                backgroundColor: warnaLatar.slice(0, labelChart.length),
                borderWidth: 2,
                borderColor: "#fff",
                hoverOffset: 4,
            },
        ],
    };

    try {
        // Buat chart doughnut baru
        var chartBaru = new Chart(ctx, {
            type: "doughnut",
            data: konfigurasiChart,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            padding: 20,
                            font: { size: 12 },
                            usePointStyle: true,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const tahun = context.label;
                                const nilai = formatRupiah(context.parsed);
                                return `${tahun}: ${nilai}`;
                            },
                        },
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        cornerRadius: 6,
                    },
                },
            },
        });
    } catch (error) {
        console.error("ERROR saat membuat chart:", error);
    }
}

// ==================== FUNGSI BUAT CHART PENDAPATAN ====================
function buatChartPendapatan(data) {
    const canvas = document.getElementById("transaction-chart");

    if (!canvas) {
        console.error("Canvas transaction-chart tidak ditemukan");
        return;
    }

    // Hancurkan chart yang sudah ada
    const chartYangAda = Chart.getChart(canvas);
    if (chartYangAda) {
        chartYangAda.destroy();
    }

    // Inisialisasi data chart untuk 31 hari (semua mulai dari 0)
    let dataChart = new Array(31).fill(0);
    let nilaiMaksimal = 0;

    // Ambil bulan dan tahun dari filter untuk validasi
    var tanggalVal = $("#tanggal").val();
    var bulanFilter = null,
        tahunFilter = null;
    if (tanggalVal) {
        var parts = tanggalVal.split("-");
        tahunFilter = parts[0];
        bulanFilter = parts[1];
    }

    // Proses data dari API
    if (Array.isArray(data) && data.length > 0) {
        data.forEach(function (item) {
            if (item.tanggal && item.total_pendapatan !== undefined) {
                // Ekstrak hari dari tanggal (contoh: "2025-11-15" -> 15)
                const tanggal = new Date(item.tanggal);
                const hari = tanggal.getDate();
                const bulan = String(tanggal.getMonth() + 1).padStart(2, "0");
                const tahun = String(tanggal.getFullYear());

                // Filter: Hanya tampilkan data sesuai bulan dan tahun filter
                if (bulanFilter && tahunFilter) {
                    if (bulan !== bulanFilter || tahun !== tahunFilter) {
                        return; // Skip data yang tidak sesuai filter
                    }
                }

                // Hari 1-31, konversi ke index array 0-30
                const indeksHari = hari - 1;

                if (indeksHari >= 0 && indeksHari < 31) {
                    dataChart[indeksHari] = item.total_pendapatan;

                    // Lacak nilai maksimal untuk skala Y-axis
                    if (item.total_pendapatan > nilaiMaksimal) {
                        nilaiMaksimal = item.total_pendapatan;
                    }
                }
            }
        });
    }

    // Tentukan nilai maksimal Y-axis
    let batasMaksimalY = hitungBatasMaksimalY(nilaiMaksimal);

    // Buat mixed chart (bar + line)
    new Chart(canvas, {
        type: "bar",
        data: {
            labels: [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18,
                19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
            ],
            datasets: [
                {
                    label: "Total Penjualan",
                    data: dataChart,
                    backgroundColor: "#FF6C0C",
                    borderColor: "#ff9b58",
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                    hoverBackgroundColor: "#ff9b58",
                    hoverBorderColor: "#FF6C0C",
                    hoverBorderWidth: 2,
                    type: "bar",
                    order: 2,
                    barPercentage: 0.3,
                },
                {
                    label: "Trend",
                    data: dataChart,
                    type: "line",
                    borderColor: "#ff9b58",
                    backgroundColor: "rgba(255, 73, 0, 0.43)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointBackgroundColor: "#ff9b58",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 0,
                    pointHoverRadius: 0,
                    pointHoverBackgroundColor: "#ff9b58",
                    pointHoverBorderColor: "#fff",
                    pointHoverBorderWidth: 0,
                    order: 1,
                    hidden: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        filter: function (item, chart) {
                            // Hanya tampilkan "Total Penjualan" di legend
                            return item.text === "Total Penjualan";
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const nilai = formatRupiah(context.parsed.y);
                            return "Total Penjualan: " + nilai;
                        },
                    },
                    backgroundColor: "rgba(0, 0, 0, 0.8)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    cornerRadius: 6,
                    displayColors: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: "#999",
                        font: {
                            size: 11,
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    max: batasMaksimalY,
                    grid: {
                        color: "#f0f0f0",
                        drawBorder: false,
                    },
                    ticks: {
                        color: "#999",
                        font: {
                            size: 11,
                        },
                        callback: function (nilai) {
                            return formatAngka(nilai);
                        },
                    },
                },
            },
            elements: {
                bar: {
                    borderRadius: 4,
                },
            },
        },
    });
}

// ==================== FUNGSI HELPER ====================
function hitungBatasMaksimalY(nilaiMaksimal) {
    if (nilaiMaksimal === 0) {
        return 1000000; // Default 1 juta jika tidak ada data
    } else if (nilaiMaksimal < 100000) {
        return Math.ceil(nilaiMaksimal / 50000) * 50000; // Bulatkan ke 50K terdekat
    } else if (nilaiMaksimal < 1000000) {
        return Math.ceil(nilaiMaksimal / 100000) * 100000; // Bulatkan ke 100K terdekat
    } else {
        return Math.ceil(nilaiMaksimal / 1000000) * 1000000; // Bulatkan ke 1M terdekat
    }
}

function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka);
}

function formatAngka(angka) {
    return new Intl.NumberFormat("id-ID").format(angka);
}
