<script setup>
import { computed } from 'vue';
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Layouts/Guest.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600">
Dziękujemy za zarejestrowanie się! Czy przed rozpoczęciem możesz zweryfikować swój adres e-mail, klikając link, który właśnie do Ciebie wysłaliśmy? Jeśli nie otrzymałeś wiadomości e-mail, chętnie wyślemy Ci kolejną.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600" v-if="verificationLinkSent" >
Nowy link weryfikacyjny został wysłany na adres e-mail podany podczas rejestracji.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <BreezeButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Wysłać ponownie email weryfikacyjny
                </BreezeButton>

                <Link :href="route('logout')" method="post" as="button" class="underline text-sm text-gray-600 hover:text-gray-900">Wyloguj</Link>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
