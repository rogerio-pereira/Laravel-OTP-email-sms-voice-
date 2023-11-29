<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
// import { props } from 'vue'

const props = defineProps({
    email: {
        type: String,
        required: false,
        default: 'rogerio@test.com'
    }
})

const form = useForm({
    email: props.email,
    otp: '',
});

const submit = () => {
    form.post(
            route('otp.login'), 
            {
                onFinish: () => form.reset('otp'),
            }
        );
};
</script>

<template>
    <GuestLayout>
        <Head title="One Time Password" />

        <form @submit.prevent="submit">
            <div class="mt-4">
                <InputLabel for="otp" value="OTP Code" />

                <TextInput
                    id="otp"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.otp"
                    required
                />

                <InputError class="mt-2" :message="form.errors.otp" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Login
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
