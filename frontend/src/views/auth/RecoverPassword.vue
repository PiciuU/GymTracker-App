<template>
    <div class="auth-container">
        <h1 class="auth__title">Recover your Account</h1>

        <form class="auth__form" @submit.prevent="submitForm" v-if="isRecovered.status == false">
                <label for="email" class="auth__label">Email</label>
                <div class="auth__input-group" :class="{ 'auth__has-error': errors.email }">
                    <input type="email" id="email" class="auth__input" v-model="credentials.email" />
                    <div v-if="errors.email" class="auth__error-message">{{ errors.email }}</div>
                </div>

                <button class="auth__button" :disabled='authStore.isLoading'>Recover Password</button>
                <div v-if="errors.critical" class="auth__critical-error-message">{{ errors.critical }}</div>
        </form>
        <div class="auth__message-box" v-else>
            <div class="auth__message">{{ isRecovered.message }}</div>
            <router-link to="/login">
                <button class="auth__success">Back to login</button>
            </router-link>
        </div>

        <div class="auth__options">
                <div class="auth__hint">Remembered your password? <router-link to="/login">Sign in here.</router-link></div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const credentials = reactive({
        email: ''
    });

    const errors = ref('');

    let isRecovered = reactive({
        status: false,
        message: ''
    });

    const submitForm = () => {
        errors.value = {};

        if (!credentials.email) errors.value.email = 'Please enter your email.';

        if (Object.keys(errors.value).length > 0) return;

        authStore.passwordRecover(credentials)
            .then((response) => {
                isRecovered.status = true;
                isRecovered.message = response.message;
            })
            .catch((error) => {
                if (error.data) {
                    const responseErrors = error.data;

                    Object.keys(responseErrors).forEach((field) => {
                        errors.value[field] = responseErrors[field][0];
                    });
                }
                else {
                    errors.value.critical = error.message
                }
            })
    };

</script>
