<template>
    <div class="w-full max-w-lg mx-auto p-4">
        <div class="bg-white shadow-md rounded-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Zoho CRM Record</h1>

            <!-- Step indicator -->
            <div class="flex items-center mb-8">
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                            step === 1 ? 'bg-blue-600 text-white' : 'bg-green-500 text-white'
                        ]"
                    >
                        <span v-if="step > 1">&#10003;</span>
                        <span v-else>1</span>
                    </div>
                    <span class="ml-2 text-sm font-medium" :class="step === 1 ? 'text-blue-600' : 'text-green-600'">Account</span>
                </div>
                <div class="flex-1 h-0.5 mx-4" :class="step > 1 ? 'bg-green-500' : 'bg-gray-300'"></div>
                <div class="flex items-center">
                    <div
                        :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                            step === 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'
                        ]"
                    >
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium" :class="step === 2 ? 'text-blue-600' : 'text-gray-500'">Deal</span>
                </div>
            </div>

            <!-- Flash messages -->
            <div v-if="successMessage" class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                {{ successMessage }}
            </div>

            <div v-if="flash.error" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                {{ flash.error }}
            </div>

            <!-- Step 1: Account -->
            <form v-if="step === 1" @submit.prevent="submitAccount">
                <div class="mb-4">
                    <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                    <input
                        id="account_name"
                        v-model="accountForm.account_name"
                        type="text"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': accountForm.errors.account_name }"
                        placeholder="Enter account name"
                    />
                    <p v-if="accountForm.errors.account_name" class="text-red-500 text-sm mt-1">{{ accountForm.errors.account_name }}</p>
                </div>

                <div class="mb-4">
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input
                        id="website"
                        v-model="accountForm.website"
                        type="url"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': accountForm.errors.website }"
                        placeholder="https://example.com"
                    />
                    <p v-if="accountForm.errors.website" class="text-red-500 text-sm mt-1">{{ accountForm.errors.website }}</p>
                </div>

                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input
                        id="phone"
                        v-model="accountForm.phone"
                        type="tel"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': accountForm.errors.phone }"
                        placeholder="+1 (555) 123-4567"
                    />
                    <p v-if="accountForm.errors.phone" class="text-red-500 text-sm mt-1">{{ accountForm.errors.phone }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="accountForm.processing"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="accountForm.processing">Creating Account...</span>
                    <span v-else>Next: Create Account</span>
                </button>
            </form>

            <!-- Step 2: Deal -->
            <form v-if="step === 2" @submit.prevent="submitDeal">
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <p class="text-sm text-gray-600">Account created. Now create a deal linked to it.</p>
                </div>

                <div class="mb-4">
                    <label for="deal_name" class="block text-sm font-medium text-gray-700 mb-1">Deal Name</label>
                    <input
                        id="deal_name"
                        v-model="dealForm.deal_name"
                        type="text"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': dealForm.errors.deal_name }"
                        placeholder="Enter deal name"
                    />
                    <p v-if="dealForm.errors.deal_name" class="text-red-500 text-sm mt-1">{{ dealForm.errors.deal_name }}</p>
                </div>

                <div class="mb-6">
                    <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                    <select
                        id="stage"
                        v-model="dealForm.stage"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': dealForm.errors.stage }"
                    >
                        <option value="">Select a stage</option>
                        <option v-for="s in stages" :key="s" :value="s">{{ s }}</option>
                    </select>
                    <p v-if="dealForm.errors.stage" class="text-red-500 text-sm mt-1">{{ dealForm.errors.stage }}</p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="step = 1"
                        class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors"
                    >
                        Back
                    </button>
                    <button
                        type="submit"
                        :disabled="dealForm.processing"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="dealForm.processing">Creating Deal...</span>
                        <span v-else>Create Deal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const zohoConnected = computed(() => page.props.zohoConnected);

const step = ref(1);
const accountId = ref(null);
const successMessage = ref('');

const stages = [
    'Qualification',
    'Needs Analysis',
    'Value Proposition',
    'Identify Decision Makers',
    'Proposal/Price Quote',
    'Negotiation/Review',
    'Closed Won',
    'Closed Lost',
];

const accountForm = useForm({
    account_name: '',
    website: '',
    phone: '',
});

const dealForm = useForm({
    deal_name: '',
    stage: '',
    account_id: '',
});

onMounted(() => {
    if (!zohoConnected.value) {
        router.visit('/auth');
        return;
    }

    // Check if we have a flash success message (e.g., after full wizard completion)
    if (flash.value.success && !flash.value.accountId) {
        successMessage.value = flash.value.success;
    }
});

function submitAccount() {
    accountForm.post('/crm/account', {
        preserveScroll: true,
        onSuccess: () => {
            const flashData = page.props.flash;
            if (flashData?.accountId) {
                accountId.value = flashData.accountId;
                step.value = 2;
                successMessage.value = '';
            }
        },
    });
}

function submitDeal() {
    dealForm.account_id = accountId.value;
    dealForm.post('/crm/deal', {
        preserveScroll: true,
        onSuccess: () => {
            step.value = 1;
            accountId.value = null;
            accountForm.reset();
            dealForm.reset();
            successMessage.value = page.props.flash?.success || 'Account and Deal created successfully!';
        },
    });
}
</script>
