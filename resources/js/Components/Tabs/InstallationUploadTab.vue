<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const page = usePage();
const form = useForm({
    file: null,
});

const showSummary = ref(false);
const summary = computed(() => page.props.upload_summary ?? null);

watch(
    summary,
    (value) => {
        if (value) {
            showSummary.value = true;
        }
    },
    { immediate: true }
);

const closeSummary = () => {
    showSummary.value = false;
};

const fileChanged = (event) => {
    form.file = event.target.files[0] ?? null;
};

const submit = () => {
    form.post(route('installations.bulk.upload'), {
        forceFormData: true,
        preserveState: true,
        onError: () => {
            showSummary.value = false;
        },
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>

                <p class="text-sm text-gray-600">Use the Excel template below to add multiple installations in one import. It includes the key fields for meter number, customer details, coordinates, feeders, seal, installer, state, and date of installation.</p>
            </div>
            <a href="/files/Installation_Template.csv" target="_blank" class="inline-flex items-center justify-center rounded bg-optimal px-4 py-2 text-sm font-semibold text-white hover:bg-opacity-90">
                Download CSV Template
            </a>
        </div>

        <div class="rounded-lg border border-gray-400 bg-white p-5 shadow-sm">
            <form @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <InputLabel for="file" value="Select Installation File" />
                        <TextInput
                            id="file"
                            type="file"
                            class="mt-1 block w-full"
                            @change="fileChanged"
                        />
                        <InputError class="mt-2" :message="form.errors.file" />
                    </div>
                    <div class="mt-5 flex justify-end">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center rounded bg-optimal px-4 py-2 text-sm font-semibold text-white transition hover:bg-opacity-90 disabled:cursor-not-allowed disabled:opacity-50">
                        Upload File
                    </button>
                </div>
                </div>


            </form>
        </div>
    </div>

    <div v-if="showSummary && summary" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
        <div class="max-h-[80vh] w-full max-w-5xl overflow-hidden rounded-xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ summary.failed > 0 ? 'Upload completed with issues' : 'Upload successful' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ summary.successful }} imported • {{ summary.failed }} failed • {{ summary.total_records }} total
                    </p>
                </div>
                <button type="button" @click="closeSummary" class="rounded-full bg-red-500 px-2 py-1 text-sm font-semibold text-white hover:bg-red-600">
                    ×
                </button>
            </div>

            <div class="overflow-auto p-5">
                <table class="min-w-full border border-gray-200 text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border-b px-3 py-2 font-semibold">Row</th>
                            <th class="border-b px-3 py-2 font-semibold">Meter No.</th>
                            <th class="border-b px-3 py-2 font-semibold">Issue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="summary.errors.length === 0">
                            <td colspan="3" class="px-3 py-4 text-center text-green-700">
                                No failed rows. Every record was imported successfully.
                            </td>
                        </tr>
                        <tr v-for="(errorItem, index) in summary.errors" :key="index" class="align-top">
                                                        <td class="border-b px-3 py-2">{{ errorItem.row - 1 }}</td>
                            <td class="border-b px-3 py-2">{{ errorItem.meter_number ?? '—' }}</td>
                            <td class="border-b px-3 py-2 text-red-600">{{ errorItem.error }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
