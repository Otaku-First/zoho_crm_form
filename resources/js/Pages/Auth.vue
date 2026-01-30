<template>
    <div class="w-full max-w-lg mx-auto p-4">
        <div class="bg-white shadow-md rounded-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Connect to Zoho CRM</h1>

            <p v-if="sessionExpired" class="text-amber-600 mb-4">
                Your session has expired. Please reconnect to continue.
            </p>

            <p v-if="flash.error" class="text-red-600 mb-4">
                {{ flash.error }}
            </p>

            <p class="text-gray-500 mb-8">
                Authorize this application to access your Zoho CRM account before creating records.
            </p>

            <a
                href="/zoho/auth"
                class="inline-block bg-blue-600 text-white py-3 px-8 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium"
            >
                Connect Zoho Account
            </a>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const sessionExpired = ref(false);

onMounted(() => {
    if (page.props.flash?.error) {
        sessionExpired.value = true;
    }
});
</script>
