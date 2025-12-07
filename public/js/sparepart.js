// Sparepart Management JavaScript
// Store Alpine component reference globally
let sparepartComponent = null;

function sparepartData() {
    return {
        table: null,
        search: "",
        stats: { total: 0, tersedia: 0, minimum: 0, habis: 0 },
        showModal: false,
        showDetailModal: false,
        showAdjustModal: false,
        modalTitle: "Tambah Sparepart",
        editMode: false,
        editId: null,
        loading: false,
        detailData: null,
        adjustData: null,
        adjustLogs: [],
        adjustForm: {
            tipe: "tambah",
            jumlah: 0,
            keterangan: "",
        },
        form: {
            kode_sparepart: "",
            nama_sparepart: "",
            merk: "",
            spesifikasi: "",
            harga: 0,
            stok: 0,
            stok_minimum: 0,
            satuan: "",
            is_active: 1,
            keterangan: "",
        },

        async init() {
            // Store component reference
            sparepartComponent = this;
            this.initDataTable();
            await this.loadStats();
            await this.generateKodeSparepart();
        },

        async loadStats() {
            try {
                const response = await fetch(window.sparepartRoutes.data);
                const data = await response.json();

                if (data.data) {
                    this.stats.total = data.recordsTotal || 0;

                    // Count status from data
                    let tersedia = 0;
                    let minimum = 0;
                    let habis = 0;

                    data.data.forEach((item) => {
                        if (item.stok <= 0) {
                            habis++;
                        } else if (item.stok <= item.stok_minimum) {
                            minimum++;
                        } else {
                            tersedia++;
                        }
                    });

                    this.stats.tersedia = tersedia;
                    this.stats.minimum = minimum;
                    this.stats.habis = habis;
                }
            } catch (error) {
                console.error("Error loading stats:", error);
            }
        },

        initDataTable() {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable("#sparepart-table")) {
                $("#sparepart-table").DataTable().destroy();
            }

            this.table = $("#sparepart-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: window.sparepartRoutes.data,
                columns: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    { data: "kode_sparepart", name: "kode_sparepart" },
                    { data: "nama_sparepart", name: "nama_sparepart" },
                    { data: "merk", name: "merk" },
                    {
                        data: "harga_formatted",
                        name: "harga",
                        orderable: false,
                        className: "text-right",
                    },
                    { data: "stok", name: "stok", className: "text-center" },
                    {
                        data: "stok_minimum",
                        name: "stok_minimum",
                        className: "text-center",
                    },
                    {
                        data: "stok_status",
                        name: "stok_status",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    {
                        data: "status_badge",
                        name: "is_active",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                    {
                        data: "aksi",
                        name: "aksi",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                ],
                order: [[1, "asc"]],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"],
                ],
                language: {
                    processing: "Memuat...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir",
                    },
                },
                dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
            });
        },

        async openAddModal() {
            this.editMode = false;
            this.editId = null;
            this.modalTitle = "Tambah Sparepart";
            this.resetForm();
            await this.generateKodeSparepart();
            this.showModal = true;
        },

        async generateKodeSparepart() {
            if (!this.editMode) {
                try {
                    const response = await fetch(
                        window.sparepartRoutes.generateKode
                    );
                    const data = await response.json();

                    if (data.success) {
                        this.form.kode_sparepart = data.kode;
                    } else {
                        this.form.kode_sparepart = "SP0001";
                    }
                } catch (error) {
                    console.error("Error generating code:", error);
                    this.form.kode_sparepart = "SP0001";
                }
            }
        },

        openEditModal(id) {
            this.editMode = true;
            this.editId = id;
            this.modalTitle = "Edit Sparepart";
            this.loadSparepartData(id);
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.detailData = null;
        },

        async openAdjustModal(id) {
            try {
                // Load sparepart data
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id)
                );
                const data = await response.json();

                console.log("Sparepart data:", data);

                if (data.success) {
                    this.adjustData = data.data;

                    // Load logs
                    const logsResponse = await fetch(
                        window.sparepartRoutes.logs.replace(":id", id)
                    );
                    const logsData = await logsResponse.json();

                    console.log("Logs response:", logsData);
                    console.log("Logs data array:", logsData.data);
                    console.log(
                        "Logs length:",
                        logsData.data ? logsData.data.length : 0
                    );

                    if (logsData.success && logsData.data) {
                        this.adjustLogs = logsData.data;
                        console.log("adjustLogs assigned:", this.adjustLogs);
                    } else {
                        this.adjustLogs = [];
                        console.log("No logs data, set to empty array");
                    }

                    // Reset form
                    this.adjustForm = {
                        tipe: "tambah",
                        jumlah: 0,
                        keterangan: "",
                    };

                    this.showAdjustModal = true;
                }
            } catch (error) {
                console.error("Error loading adjust modal:", error);
                alert("Gagal memuat data penyesuaian stok");
            }
        },

        closeAdjustModal() {
            this.showAdjustModal = false;
            this.adjustData = null;
            this.adjustLogs = [];
            this.adjustForm = {
                tipe: "tambah",
                jumlah: 0,
                keterangan: "",
            };
        },

        async saveAdjustment() {
            if (!this.adjustForm.jumlah || this.adjustForm.jumlah <= 0) {
                alert("Jumlah harus lebih dari 0");
                return;
            }

            if (!this.adjustForm.keterangan) {
                alert("Keterangan harus diisi");
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(
                    window.sparepartRoutes.adjust.replace(
                        ":id",
                        this.adjustData.id_sparepart
                    ),
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: JSON.stringify(this.adjustForm),
                    }
                );

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closeAdjustModal();
                    this.table.ajax.reload(null, false);
                    await this.loadStats();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Error saving adjustment:", error);
                alert("Terjadi kesalahan saat menyimpan penyesuaian");
            } finally {
                this.loading = false;
            }
        },

        async openDetailModal(id) {
            try {
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id)
                );
                const data = await response.json();

                if (data.success) {
                    this.detailData = data.data;
                    this.showDetailModal = true;
                }
            } catch (error) {
                console.error("Error loading detail:", error);
                alert("Gagal memuat detail sparepart");
            }
        },

        resetForm() {
            this.form = {
                kode_sparepart: "",
                nama_sparepart: "",
                merk: "",
                spesifikasi: "",
                harga: 0,
                stok: 0,
                stok_minimum: 0,
                satuan: "",
                is_active: 1,
                keterangan: "",
            };
        },

        async loadSparepartData(id) {
            try {
                const response = await fetch(
                    window.sparepartRoutes.show.replace(":id", id)
                );
                const data = await response.json();

                if (data.success) {
                    this.form = {
                        kode_sparepart: data.data.kode_sparepart,
                        nama_sparepart: data.data.nama_sparepart,
                        merk: data.data.merk || "",
                        spesifikasi: data.data.spesifikasi || "",
                        harga: data.data.harga,
                        stok: data.data.stok,
                        stok_minimum: data.data.stok_minimum,
                        satuan: data.data.satuan,
                        is_active: data.data.is_active ? 1 : 0,
                        keterangan: data.data.keterangan || "",
                    };
                }
            } catch (error) {
                console.error("Error loading sparepart:", error);
                alert("Gagal memuat data sparepart");
            }
        },

        async saveSparepart() {
            this.loading = true;

            try {
                const url = this.editMode
                    ? window.sparepartRoutes.update.replace(":id", this.editId)
                    : window.sparepartRoutes.store;

                const method = this.editMode ? "PUT" : "POST";

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.closeModal();
                    this.table.ajax.reload(null, false);
                    await this.loadStats();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (error) {
                console.error("Error saving sparepart:", error);
                alert("Terjadi kesalahan saat menyimpan data");
            } finally {
                this.loading = false;
            }
        },

        showToast(message, type = "success") {
            alert(message);
        },
    };
}

// Global functions for DataTables action buttons
function viewDetail(id) {
    if (sparepartComponent && sparepartComponent.openDetailModal) {
        sparepartComponent.openDetailModal(id);
    }
}

function editSparepart(id) {
    if (sparepartComponent && sparepartComponent.openEditModal) {
        sparepartComponent.openEditModal(id);
    }
}

function adjustStok(id) {
    if (sparepartComponent && sparepartComponent.openAdjustModal) {
        sparepartComponent.openAdjustModal(id);
    }
}

function deleteSparepart(id) {
    if (
        !confirm(
            "Yakin ingin menghapus sparepart ini?\n\nData yang sudah dihapus tidak dapat dikembalikan."
        )
    ) {
        return;
    }

    fetch(window.sparepartRoutes.destroy.replace(":id", id), {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.message);
                const table = $("#sparepart-table").DataTable();
                table.ajax.reload(null, false);

                // Reload stats
                if (sparepartComponent && sparepartComponent.loadStats) {
                    sparepartComponent.loadStats();
                }
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat menghapus sparepart");
        });
}
