// Production Management JavaScript
(function () {
    "use strict";

    // State management
    const state = {
        selectedOutlet: null,
        products: [],
        materials: [],
        dataTable: null,
    };

    // Initialize
    document.addEventListener("DOMContentLoaded", function () {
        initializeOutletSelector();
        initializeDataTable();
        initializeEventListeners();
        loadProducts();
        loadMaterials();
        loadStatistics();
    });

    // Initialize outlet selector
    function initializeOutletSelector() {
        const outletSelect = document.getElementById("outletSelect");
        if (outletSelect) {
            state.selectedOutlet = outletSelect.value;
            outletSelect.addEventListener("change", function () {
                state.selectedOutlet = this.value;
                reloadData();
            });
        }
    }

    // Initialize DataTable
    function initializeDataTable() {
        const table = $("#productionTable");
        if (table.length === 0) return;

        state.dataTable = table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: productionDataUrl,
                data: function (d) {
                    d.outlet_id = state.selectedOutlet;
                    d.status = $("#filterStatus").val();
                    d.production_line = $("#filterLine").val();
                    d.start_date = $("#filterStartDate").val();
                    d.end_date = $("#filterEndDate").val();
                },
            },
            columns: [
                { data: "production_code", name: "production_code" },
                { data: "product_name", name: "product.nama_produk" },
                { data: "production_line", name: "production_line" },
                {
                    data: "target_quantity",
                    name: "target_quantity",
                    render: $.fn.dataTable.render.number(",", ".", 0),
                },
                {
                    data: "realized_quantity",
                    name: "realized_quantity",
                    render: $.fn.dataTable.render.number(",", ".", 0),
                },
                {
                    data: "progress",
                    name: "progress",
                    render: function (data, type, row) {
                        return `
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-slate-100 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: ${data}%"></div>
                                </div>
                                <span class="text-sm text-slate-600">${data}%</span>
                            </div>
                        `;
                    },
                },
                {
                    data: "status_badge",
                    name: "status",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "start_date",
                    name: "start_date",
                    render: function (data) {
                        return new Date(data).toLocaleDateString("id-ID", {
                            day: "2-digit",
                            month: "short",
                            year: "numeric",
                        });
                    },
                },
                {
                    data: "actions",
                    name: "actions",
                    orderable: false,
                    searchable: false,
                },
            ],
            order: [[0, "desc"]],
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
            },
        });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Create button
        const createBtn = document.getElementById("createProductionBtn");
        if (createBtn) {
            createBtn.addEventListener("click", openCreateModal);
        }

        // Form submission
        const productionForm = document.getElementById("productionForm");
        if (productionForm) {
            productionForm.addEventListener("submit", handleFormSubmit);
        }

        // Filter buttons
        $("#filterStatus, #filterLine, #filterStartDate, #filterEndDate").on(
            "change",
            function () {
                if (state.dataTable) {
                    state.dataTable.ajax.reload();
                }
            }
        );

        // Close modal on ESC
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                closeCreateModal();
            }
        });
    }

    // Load products - not needed anymore since we use autocomplete
    function loadProducts() {
        // Products are now loaded via autocomplete search
        // No need to preload all products
        state.products = [];
    }

    // Load materials
    function loadMaterials() {
        fetch(materialsUrl + "?outlet_id=" + state.selectedOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    state.materials = data.data;
                    populateMaterialSelects();
                }
            })
            .catch((error) => console.error("Error loading materials:", error));
    }

    // Load statistics
    function loadStatistics() {
        fetch(statisticsUrl + "?outlet_id=" + state.selectedOutlet)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    updateStatistics(data.data);
                }
            })
            .catch((error) =>
                console.error("Error loading statistics:", error)
            );
    }

    // Product autocomplete
    let productSearchTimeout;
    const productSearchInput = document.getElementById("product_search");
    const productIdInput = document.getElementById("product_id");
    const productResults = document.getElementById("product_results");

    if (productSearchInput) {
        productSearchInput.addEventListener("input", function () {
            clearTimeout(productSearchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                productResults.classList.add("hidden");
                productIdInput.value = "";
                return;
            }

            productSearchTimeout = setTimeout(() => {
                fetch(
                    `${productsUrl}?search=${encodeURIComponent(
                        query
                    )}&outlet_id=${state.selectedOutlet}`
                )
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success && data.data.length > 0) {
                            productResults.innerHTML = data.data
                                .map(
                                    (p) => `
                                <div class="px-4 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0" 
                                     onclick="selectProduct(${p.id}, '${
                                        p.code
                                    } - ${p.name}')">
                                    <div class="font-medium text-slate-800">${
                                        p.code
                                    }</div>
                                    <div class="text-sm text-slate-600">${
                                        p.name
                                    }</div>
                                    <div class="text-xs text-slate-500 mt-1">Stok: ${
                                        p.stock
                                    } | HPP: Rp ${Number(p.hpp).toLocaleString(
                                        "id-ID"
                                    )}</div>
                                </div>
                            `
                                )
                                .join("");
                            productResults.classList.remove("hidden");
                        } else {
                            productResults.innerHTML =
                                '<div class="px-4 py-2 text-slate-500 text-sm">Tidak ada hasil</div>';
                            productResults.classList.remove("hidden");
                        }
                    })
                    .catch((error) => {
                        console.error("Error searching products:", error);
                        productResults.classList.add("hidden");
                    });
            }, 300);
        });

        // Close results when clicking outside
        document.addEventListener("click", function (e) {
            if (
                !productSearchInput.contains(e.target) &&
                !productResults.contains(e.target)
            ) {
                productResults.classList.add("hidden");
            }
        });
    }

    window.selectProduct = function (id, text) {
        productIdInput.value = id;
        productSearchInput.value = text;
        productResults.classList.add("hidden");
    };

    // Populate material selects
    function populateMaterialSelects() {
        const selects = document.querySelectorAll(
            'select[name^="materials"][name$="[material_id]"]'
        );
        selects.forEach((select) => {
            populateSingleMaterialSelect(select);
        });
    }

    function populateSingleMaterialSelect(select) {
        select.innerHTML = '<option value="">Pilih Bahan</option>';
        state.materials.forEach((material) => {
            const option = document.createElement("option");
            option.value = material.id;

            // Show stock in option text
            const stockText =
                material.stock > 0
                    ? `(Stok: ${material.stock} ${material.unit})`
                    : "(STOK HABIS)";
            option.textContent = `${material.name} ${stockText}`;

            option.dataset.type = material.type;
            option.dataset.unit = material.unit;
            option.dataset.stock = material.stock;

            // Disable if stock is 0
            if (material.stock === 0) {
                option.disabled = true;
                option.style.color = "#ef4444";
            }

            select.appendChild(option);
        });
    }

    // Update statistics
    function updateStatistics(stats) {
        const activeCount = document.getElementById("activeCount");
        if (activeCount) {
            activeCount.textContent = stats.active || 0;
        }
    }

    // Modal functions
    window.openCreateModal = function () {
        const modal = document.getElementById("createModal");
        if (modal) {
            modal.classList.remove("hidden");
            document.body.style.overflow = "hidden";
        }
    };

    window.closeCreateModal = function () {
        const modal = document.getElementById("createModal");
        if (modal) {
            modal.classList.add("hidden");
            document.body.style.overflow = "auto";
            document.getElementById("productionForm").reset();
        }
    };

    // Material management
    let materialCount = 1;

    window.addMaterial = function () {
        const container = document.getElementById("materialRequirements");
        const newRow = document.createElement("div");
        newRow.className = "flex items-center gap-3 material-row";
        newRow.innerHTML = `
            <input type="hidden" name="materials[${materialCount}][material_type]" value="">
            <select name="materials[${materialCount}][material_id]" 
                    class="flex-1 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    onchange="updateMaterialUnit(this, ${materialCount})">
                <option value="">Pilih Material</option>
            </select>
            <input type="number" name="materials[${materialCount}][quantity]" min="1" step="0.01"
                   class="w-32 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Qty">
            <select name="materials[${materialCount}][unit]"
                    class="w-24 border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="kg">kg</option>
                <option value="pcs">pcs</option>
                <option value="roll">roll</option>
                <option value="unit">unit</option>
            </select>
            <button type="button" onclick="removeMaterial(this)" class="p-2 text-red-500 hover:bg-red-50 rounded">
                <i class='bx bx-trash'></i>
            </button>
        `;
        container.appendChild(newRow);

        // Populate the new select
        const newSelect = newRow.querySelector('select[name$="[material_id]"]');
        populateSingleMaterialSelect(newSelect);

        materialCount++;
    };

    window.removeMaterial = function (button) {
        if (document.querySelectorAll(".material-row").length > 1) {
            button.closest(".material-row").remove();
        }
    };

    window.updateMaterialUnit = function (select, index) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.dataset.unit) {
            const unitSelect = document.querySelector(
                `select[name="materials[${index}][unit]"]`
            );
            const typeInput = document.querySelector(
                `input[name="materials[${index}][material_type]"]`
            );
            if (unitSelect) {
                unitSelect.value = selectedOption.dataset.unit;
            }
            if (typeInput && selectedOption.dataset.type) {
                typeInput.value = selectedOption.dataset.type;
            }
        }
    };

    // Form submission
    function handleFormSubmit(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = {};

        // Convert FormData to object
        for (let [key, value] of formData.entries()) {
            if (key.startsWith("materials[")) {
                if (!data.materials) data.materials = [];
                const match = key.match(/materials\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    const field = match[2];
                    if (!data.materials[index]) data.materials[index] = {};
                    data.materials[index][field] = value;
                }
            } else {
                data[key] = value;
            }
        }

        // Add outlet_id
        data.outlet_id = state.selectedOutlet;

        // Filter out empty materials
        if (data.materials) {
            data.materials = data.materials.filter(
                (m) => m && m.material_id && m.quantity
            );
        }

        // Validate material stock
        let stockWarnings = [];
        document.querySelectorAll(".material-row").forEach((row) => {
            const select = row.querySelector('select[name$="[material_id]"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');

            if (select && select.value && qtyInput && qtyInput.value) {
                const option = select.options[select.selectedIndex];
                const stock = parseFloat(option.dataset.stock || 0);
                const required = parseFloat(qtyInput.value);
                const materialName = option.textContent.split("(")[0].trim();

                if (stock === 0) {
                    showNotification(
                        `Stok ${materialName} habis! Silahkan lakukan pembelian bahan di menu PO.`,
                        "error"
                    );
                    return;
                }

                if (stock < required) {
                    stockWarnings.push(
                        `${materialName}: Tersedia ${stock}, Dibutuhkan ${required}`
                    );
                }
            }
        });

        // Show warning if stock insufficient
        if (stockWarnings.length > 0) {
            const message =
                "Peringatan stok kurang:\n" +
                stockWarnings.join("\n") +
                "\n\nLanjutkan?";
            if (!confirm(message)) {
                return;
            }
        }

        // Submit
        console.log("Sending data:", data); // Debug log
        fetch(storeUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify(data),
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((err) => {
                        console.error("Validation errors:", err);
                        throw err;
                    });
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    showNotification("Produksi berhasil dibuat!", "success");
                    closeCreateModal();
                    if (state.dataTable) {
                        state.dataTable.ajax.reload();
                    }
                    loadStatistics();
                } else {
                    showNotification(
                        data.message || "Gagal membuat produksi",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);

                // Display validation errors if available
                if (error.errors) {
                    let errorMsg = "Validasi gagal:\n";
                    for (let field in error.errors) {
                        errorMsg += `- ${error.errors[field].join(", ")}\n`;
                    }
                    showNotification(errorMsg, "error");
                } else {
                    showNotification(
                        error.message ||
                            "Terjadi kesalahan saat menyimpan data",
                        "error"
                    );
                }
            });
    }

    // Action handlers
    window.viewProduction = function (id) {
        // Show modal with loading state
        const modal = document.getElementById("detailModal");
        const content = document.getElementById("detailContent");
        modal.classList.remove("hidden");
        content.innerHTML = `
            <div class="text-center py-8">
                <i class='bx bx-loader-alt bx-spin text-4xl text-slate-400'></i>
                <p class="text-slate-500 mt-2">Memuat data...</p>
            </div>
        `;

        fetch(showUrl.replace(":id", id))
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const prod = data.data;
                    const statusColors = {
                        draft: "slate",
                        approved: "blue",
                        in_progress: "yellow",
                        completed: "green",
                        cancelled: "red",
                    };
                    const statusLabels = {
                        draft: "Draft",
                        approved: "Disetujui",
                        in_progress: "Sedang Berjalan",
                        completed: "Selesai",
                        cancelled: "Dibatalkan",
                    };
                    const color = statusColors[prod.status] || "slate";
                    const label = statusLabels[prod.status] || prod.status;

                    content.innerHTML = `
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm text-slate-500">Kode Produksi</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.production_code
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Status</label>
                                <p><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${color}-100 text-${color}-700">${label}</span></p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Produk</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.product?.nama_produk || "-"
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Lini Produksi</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.production_line
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Target Quantity</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.target_quantity
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Actual Quantity</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.actual_quantity || 0
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Stok Produk Saat Ini</label>
                                <p class="font-semibold text-green-600">${
                                    prod.product_stock || 0
                                } unit</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">HPP Per Unit</label>
                                <p class="font-semibold text-slate-800">Rp ${Number(
                                    prod.product_hpp || 0
                                ).toLocaleString("id-ID")}</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Tanggal Mulai</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.start_date
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Tanggal Selesai</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.end_date
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Prioritas</label>
                                <p class="font-semibold text-slate-800 capitalize">${
                                    prod.priority
                                }</p>
                            </div>
                            <div>
                                <label class="text-sm text-slate-500">Progress</label>
                                <p class="font-semibold text-slate-800">${
                                    prod.progress_percentage
                                }%</p>
                            </div>
                        </div>
                        ${
                            prod.notes
                                ? `
                        <div class="mt-4">
                            <label class="text-sm text-slate-500">Catatan</label>
                            <p class="text-slate-800">${prod.notes}</p>
                        </div>
                        `
                                : ""
                        }
                        ${
                            prod.materials && prod.materials.length > 0
                                ? `
                        <div class="mt-6">
                            <h4 class="font-semibold text-slate-800 mb-3">Material yang Dibutuhkan</h4>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Material</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Tipe</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500">Qty</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        ${prod.materials
                                            .map(
                                                (m) => `
                                        <tr>
                                            <td class="px-4 py-2 text-sm">${
                                                m.material?.nama_bahan ||
                                                m.material?.nama_produk ||
                                                "-"
                                            }</td>
                                            <td class="px-4 py-2 text-sm capitalize">${
                                                m.material_type
                                            }</td>
                                            <td class="px-4 py-2 text-sm text-right">${
                                                m.quantity_required
                                            }</td>
                                            <td class="px-4 py-2 text-sm">${
                                                m.unit
                                            }</td>
                                        </tr>
                                        `
                                            )
                                            .join("")}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        `
                                : ""
                        }
                        ${
                            prod.realizations && prod.realizations.length > 0
                                ? `
                        <div class="mt-6">
                            <h4 class="font-semibold text-slate-800 mb-3">Riwayat Realisasi</h4>
                            <div class="space-y-2">
                                ${prod.realizations
                                    .map(
                                        (r) => `
                                <div class="border border-slate-200 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm"><span class="font-medium">Diproduksi:</span> ${
                                                r.quantity_produced
                                            }</p>
                                            <p class="text-sm"><span class="font-medium">Reject:</span> ${
                                                r.quantity_rejected
                                            }</p>
                                            ${
                                                r.notes
                                                    ? `<p class="text-sm text-slate-600 mt-1">${r.notes}</p>`
                                                    : ""
                                            }
                                        </div>
                                        <span class="text-xs text-slate-500">${
                                            r.created_at
                                        }</span>
                                    </div>
                                </div>
                                `
                                    )
                                    .join("")}
                            </div>
                        </div>
                        `
                                : ""
                        }
                    `;
                } else {
                    content.innerHTML = `
                        <div class="text-center py-8">
                            <i class='bx bx-error text-4xl text-red-400'></i>
                            <p class="text-slate-500 mt-2">Gagal memuat data</p>
                        </div>
                    `;
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i class='bx bx-error text-4xl text-red-400'></i>
                        <p class="text-slate-500 mt-2">Terjadi kesalahan</p>
                    </div>
                `;
            });
    };

    window.editProduction = function (id) {
        // TODO: Implement edit functionality
        console.log("Edit production:", id);
    };

    window.deleteProduction = function (id) {
        if (!confirm("Apakah Anda yakin ingin menghapus produksi ini?")) return;

        fetch(deleteUrl.replace(":id", id), {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showNotification("Produksi berhasil dihapus", "success");
                    if (state.dataTable) {
                        state.dataTable.ajax.reload();
                    }
                    loadStatistics();
                } else {
                    showNotification(
                        data.message || "Gagal menghapus produksi",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showNotification("Terjadi kesalahan", "error");
            });
    };

    window.approveProduction = function (id) {
        if (!confirm("Apakah Anda yakin ingin menyetujui produksi ini?"))
            return;

        fetch(approveUrl.replace(":id", id), {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showNotification("Produksi berhasil disetujui", "success");
                    if (state.dataTable) {
                        state.dataTable.ajax.reload();
                    }
                } else {
                    showNotification(
                        data.message || "Gagal menyetujui produksi",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showNotification("Terjadi kesalahan", "error");
            });
    };

    window.startProduction = function (id) {
        if (!confirm("Apakah Anda yakin ingin memulai produksi ini?")) return;

        fetch(startUrl.replace(":id", id), {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showNotification("Produksi berhasil dimulai", "success");
                    if (state.dataTable) {
                        state.dataTable.ajax.reload();
                    }
                    loadStatistics();
                } else {
                    showNotification(
                        data.message || "Gagal memulai produksi",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showNotification("Terjadi kesalahan", "error");
            });
    };

    window.addRealization = function (id) {
        document.getElementById("realization_production_id").value = id;
        document.getElementById("realizationModal").classList.remove("hidden");
        document.getElementById("realizationForm").reset();
        document.getElementById("realization_production_id").value = id;
    };

    window.closeRealizationModal = function () {
        document.getElementById("realizationModal").classList.add("hidden");
        document.getElementById("realizationForm").reset();
    };

    window.handleRealizationSubmit = function (e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const productionId = formData.get("production_id");
        const data = {
            quantity_produced: formData.get("quantity_produced"),
            quantity_rejected: formData.get("quantity_rejected") || 0,
            notes: formData.get("notes"),
        };

        fetch(addRealizationUrl.replace(":id", productionId), {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showNotification(
                        "Realisasi berhasil ditambahkan",
                        "success"
                    );
                    closeRealizationModal();
                    if (state.dataTable) {
                        state.dataTable.ajax.reload();
                    }
                    loadStatistics();
                } else {
                    showNotification(
                        data.message || "Gagal menambahkan realisasi",
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showNotification("Terjadi kesalahan", "error");
            });
    };

    window.closeDetailModal = function () {
        document.getElementById("detailModal").classList.add("hidden");
    };

    // Log that functions are loaded
    console.log("Production.js loaded successfully");
    console.log("startProduction function:", typeof window.startProduction);
    console.log("approveProduction function:", typeof window.approveProduction);

    // Utility functions
    function reloadData() {
        if (state.dataTable) {
            state.dataTable.ajax.reload();
        }
        loadProducts();
        loadMaterials();
        loadStatistics();
    }

    function showNotification(message, type = "info") {
        // Simple notification - you can replace with your preferred notification library
        alert(message);
    }
})();
