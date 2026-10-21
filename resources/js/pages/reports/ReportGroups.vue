<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-4 sm:py-6">
            <!-- Encabezado -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl sm:text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-folder-open text-rose-600 mr-3"></i>
                        Informes de inspección
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Agrupa varios puntos en un solo documento con portada, índice y firmas</p>
                </div>
                <div class="flex items-center gap-2">
                    <router-link
                        :to="{ name: 'reports' }"
                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </router-link>
                    <button
                        v-if="authStore.hasPermission('save report')"
                        type="button"
                        @click="openCreate"
                        class="flex items-center justify-center px-5 py-3 sm:py-2.5 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Nuevo informe
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-gray-400"></i>
                            Buscar
                        </label>
                        <input
                            v-model="filters.q"
                            type="text"
                            placeholder="Código, título, asunto o período"
                            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 px-4 py-2.5"
                            @keyup.enter="fetchGroups(1)"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-gray-400"></i>
                            Empresa
                        </label>
                        <select
                            v-model="filters.company_id"
                            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 px-4 py-2.5"
                            @change="fetchGroups(1)"
                        >
                            <option value="">Todas</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Listado -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div v-if="loading" class="p-8 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                </div>
                <div v-else-if="groups.length === 0" class="p-8 text-center text-gray-500">
                    No hay informes registrados.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Informe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Empresa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Período</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Puntos</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="group in groups" :key="group.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ group.code }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ group.title }}</p>
                                    <p class="text-xs text-gray-500">{{ group.subject || '—' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ group.company?.name || '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ group.period || '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(group.service_date) }}</td>
                                <td class="px-4 py-3 text-center text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-semibold">
                                        {{ group.reports_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-emerald-600 hover:bg-emerald-50"
                                        title="Descargar PDF"
                                        @click="downloadPdf(group)"
                                    >
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button
                                        v-if="authStore.hasPermission('edit report')"
                                        type="button"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-blue-600 hover:bg-blue-50"
                                        title="Editar"
                                        @click="openEdit(group)"
                                    >
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button
                                        v-if="authStore.hasPermission('delete report')"
                                        type="button"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:bg-red-50"
                                        title="Eliminar"
                                        @click="destroyGroup(group)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    <Pagination v-if="pagination && groups.length > 0" :pagination="pagination" @page-change="fetchGroups" />
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl my-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-folder-open text-rose-600 mr-2"></i>
                        {{ editingId ? 'Editar informe' : 'Nuevo informe' }}
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título del informe *</label>
                            <input v-model="form.title" type="text" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                            <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Asunto / instalación</label>
                            <input v-model="form.subject" type="text" placeholder="Ej: SUB-ESTACIÓN # 2" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                            <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empresa *</label>
                            <select v-model="form.company_id" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" @change="onCompanyChange">
                                <option value="">Seleccione</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                            </select>
                            <p v-if="errors.company_id" class="mt-1 text-sm text-red-600">{{ errors.company_id[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                            <select v-model="form.process_id" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5">
                                <option value="">Seleccione</option>
                                <option v-for="process in processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Localización</label>
                            <select v-model="form.ubication_id" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5">
                                <option value="">Seleccione</option>
                                <option v-for="ubication in ubications" :key="ubication.id" :value="ubication.id">{{ ubication.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                            <input v-model="form.period" type="text" placeholder="Ej: FEBRERO 2026" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha del servicio *</label>
                            <input v-model="form.service_date" type="date" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                            <p v-if="errors.service_date" class="mt-1 text-sm text-red-600">{{ errors.service_date[0] }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resumen</label>
                            <textarea v-model="form.summary" rows="3" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" placeholder="Objetivo o alcance del servicio"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Responsable *</label>
                            <input v-model="form.responsible_name" type="text" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                            <p v-if="errors.responsible_name" class="mt-1 text-sm text-red-600">{{ errors.responsible_name[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input v-model="form.responsible_position" type="text" placeholder="Ej: Ingeniero Mecánico" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Certificación</label>
                            <input v-model="form.responsible_certification" type="text" placeholder="Ej: Termógrafo Nivel 1 - Certificación SNT-TC-1A-2020" class="block w-full bg-gray-50 border border-gray-300 text-sm rounded-lg px-4 py-2.5" />
                        </div>
                    </div>

                    <!-- Puntos -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">Puntos del informe *</h4>
                                <p class="text-xs text-gray-500">Selecciona los reportes de termografía que formarán parte del documento.</p>
                            </div>
                            <span class="text-sm font-semibold text-rose-600">{{ form.report_ids.length }} seleccionados</span>
                        </div>

                        <p v-if="errors.report_ids" class="mb-2 text-sm text-red-600">{{ errors.report_ids[0] }}</p>

                        <div v-if="!form.company_id" class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4">
                            Selecciona una empresa para listar sus reportes.
                        </div>
                        <div v-else-if="loadingReports" class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Cargando reportes...
                        </div>
                        <div v-else-if="availableReports.length === 0" class="text-sm text-gray-500 bg-gray-50 rounded-lg p-4">
                            No hay reportes de termografía para esta empresa.
                        </div>
                        <div v-else class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100">
                            <label
                                v-for="report in availableReports"
                                :key="report.id"
                                class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                                    :checked="form.report_ids.includes(report.id)"
                                    @change="toggleReport(report.id)"
                                />
                                <span class="text-sm font-mono text-gray-700 w-28">{{ report.consecutivo }}</span>
                                <span class="text-sm text-gray-800 flex-1">{{ report.equipment?.name || '—' }}</span>
                                <span class="text-xs text-gray-500">{{ formatDate(report.measurement_date) }}</span>
                                <span
                                    v-if="report.report_group_id && report.report_group_id !== editingId"
                                    class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold"
                                >En otro informe</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <button type="button" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium" @click="closeModal">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        :disabled="saving"
                        class="px-5 py-2.5 bg-rose-500 text-white rounded-lg hover:bg-rose-600 font-medium disabled:opacity-60"
                        @click="save"
                    >
                        <i class="fas fa-save mr-2"></i>
                        {{ saving ? 'Guardando...' : 'Guardar informe' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import Swal from 'sweetalert2';
import api from '../../services/api';
import { useAuthStore } from '../../stores/auth';
import Pagination from '../../components/Pagination.vue';

const authStore = useAuthStore();

const groups = ref([]);
const loading = ref(false);
const pagination = ref(null);
const companies = ref([]);
const processes = ref([]);
const ubications = ref([]);

const availableReports = ref([]);
const loadingReports = ref(false);

const showModal = ref(false);
const saving = ref(false);
const editingId = ref(null);
const errors = ref({});

const filters = reactive({ q: '', company_id: '' });

const today = () => {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${now.getFullYear()}-${month}-${day}`;
};

const form = reactive({
    title: 'INSPECCIÓN TERMOGRAFÍA',
    report_type: 'thermography',
    company_id: '',
    process_id: '',
    ubication_id: '',
    subject: '',
    period: '',
    service_date: today(),
    summary: '',
    responsible_name: '',
    responsible_position: '',
    responsible_certification: '',
    report_ids: [],
});

const normalizeList = (response) => response.data?.data ?? response.data ?? [];

const formatDate = (value) => {
    if (!value) return '—';
    const [year, month, day] = String(value).slice(0, 10).split('-');
    return year && month && day ? `${day}/${month}/${year}` : value;
};

const fetchGroups = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: 10, report_type: 'thermography' };
        if (filters.q) params.q = filters.q.trim();
        if (filters.company_id) params.company_id = [filters.company_id];

        const response = await api.get('/report-groups', { params });
        groups.value = response.data.data ?? [];
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            to: response.data.to,
            total: response.data.total,
            last_page: response.data.last_page,
        };
    } catch (error) {
        Swal.fire('Error', error.response?.data?.message || 'No se pudieron cargar los informes.', 'error');
    } finally {
        loading.value = false;
    }
};

const loadCatalogs = async () => {
    try {
        const [companiesRes, processesRes, ubicationsRes] = await Promise.all([
            api.get('/catalogs/companies'),
            api.get('/catalogs/processes'),
            api.get('/catalogs/ubications'),
        ]);
        companies.value = normalizeList(companiesRes);
        processes.value = normalizeList(processesRes);
        ubications.value = normalizeList(ubicationsRes);
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
};

const loadReportsForSelection = async () => {
    if (!form.company_id) {
        availableReports.value = [];
        return;
    }

    loadingReports.value = true;
    try {
        const response = await api.get('/reports', {
            params: {
                report_type: 'thermography',
                company_id: [form.company_id],
                per_page: 200,
                sort_by: 'measurement_date',
                sort_order: 'asc',
            },
        });
        availableReports.value = response.data.data ?? [];
    } catch (error) {
        availableReports.value = [];
        Swal.fire('Error', 'No se pudieron cargar los reportes.', 'error');
    } finally {
        loadingReports.value = false;
    }
};

const onCompanyChange = () => {
    form.report_ids = [];
    loadReportsForSelection();
};

const toggleReport = (id) => {
    const index = form.report_ids.indexOf(id);
    if (index >= 0) {
        form.report_ids.splice(index, 1);
    } else {
        form.report_ids.push(id);
    }
};

const resetForm = () => {
    editingId.value = null;
    errors.value = {};
    Object.assign(form, {
        title: 'INSPECCIÓN TERMOGRAFÍA',
        report_type: 'thermography',
        company_id: filters.company_id || '',
        process_id: '',
        ubication_id: '',
        subject: '',
        period: '',
        service_date: today(),
        summary: '',
        responsible_name: authStore.user?.name || '',
        responsible_position: '',
        responsible_certification: '',
        report_ids: [],
    });
};

const openCreate = async () => {
    resetForm();
    showModal.value = true;
    await loadReportsForSelection();
};

const openEdit = async (group) => {
    resetForm();
    try {
        const response = await api.get(`/report-groups/${group.id}`);
        const data = response.data.data;

        editingId.value = data.id;
        Object.assign(form, {
            title: data.title,
            report_type: data.report_type,
            company_id: data.company?.id ?? '',
            process_id: data.process?.id ?? '',
            ubication_id: data.ubication?.id ?? '',
            subject: data.subject ?? '',
            period: data.period ?? '',
            service_date: data.service_date ?? today(),
            summary: data.summary ?? '',
            responsible_name: data.responsible_name ?? '',
            responsible_position: data.responsible_position ?? '',
            responsible_certification: data.responsible_certification ?? '',
            report_ids: (data.reports || []).map((report) => report.id),
        });

        showModal.value = true;
        await loadReportsForSelection();
    } catch (error) {
        Swal.fire('Error', 'No se pudo cargar el informe.', 'error');
    }
};

const closeModal = () => {
    showModal.value = false;
};

const save = async () => {
    errors.value = {};

    if (!form.report_ids.length) {
        errors.value = { report_ids: ['Selecciona al menos un punto para el informe.'] };
        return;
    }

    saving.value = true;
    try {
        const payload = { ...form };
        if (!payload.process_id) delete payload.process_id;
        if (!payload.ubication_id) delete payload.ubication_id;

        if (editingId.value) {
            await api.put(`/report-groups/${editingId.value}`, payload);
        } else {
            await api.post('/report-groups', payload);
        }

        showModal.value = false;
        await fetchGroups(pagination.value?.current_page ?? 1);

        Swal.fire({
            icon: 'success',
            title: editingId.value ? 'Informe actualizado' : 'Informe creado',
            timer: 1500,
            showConfirmButton: false,
        });
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {};
            const first = Object.values(errors.value)[0];
            Swal.fire('Revisa el formulario', Array.isArray(first) ? first[0] : 'Datos inválidos.', 'warning');
        } else {
            Swal.fire('Error', error.response?.data?.message || 'No se pudo guardar el informe.', 'error');
        }
    } finally {
        saving.value = false;
    }
};

const downloadPdf = async (group) => {
    try {
        const response = await api.get(`/report-groups/${group.id}/pdf`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${group.code} - Informe Termografía.pdf`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        Swal.fire('Error', 'No se pudo generar el PDF del informe.', 'error');
    }
};

const destroyGroup = async (group) => {
    const result = await Swal.fire({
        title: '¿Eliminar informe?',
        text: `El informe ${group.code} se eliminará. Los reportes no se borran.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
    });

    if (!result.isConfirmed) return;

    try {
        await api.delete(`/report-groups/${group.id}`);
        await fetchGroups(pagination.value?.current_page ?? 1);
        Swal.fire({ icon: 'success', title: 'Informe eliminado', timer: 1500, showConfirmButton: false });
    } catch (error) {
        Swal.fire('Error', 'No se pudo eliminar el informe.', 'error');
    }
};

onMounted(async () => {
    await loadCatalogs();
    await fetchGroups(1);
});
</script>
