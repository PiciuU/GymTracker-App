<template>
    <div class="container">
        <h1>Recover your Account</h1>

        <form class="form" @submit.prevent="submitForm" v-if="isRecovered.status === false">
            <div class="form__group">
                <label for="password">New Password</label>
                <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.password }">
                    <input type="password" id="password" v-model="credentials.password" />
                    <p v-if="errors.password">{{ errors.password }}</p>
                </div>
            </div>

            <div class="form__group">
                <label for="passwordConfirmation">Confirm Password</label>
                <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.passwordConfirmation }">
                    <input type="password" id="passwordConfirmation" v-model="credentials.passwordConfirmation" />
                    <p v-if="errors.passwordConfirmation">{{ errors.passwordConfirmation }}</p>
                </div>
            </div>

            <button :disabled='authStore.isLoading'>Reset Password</button>
            <div v-if="errors.critical" class="form__error"><p>{{ errors.critical }}</p></div>
        </form>
        <div class="form__success" v-else>
            <p>{{ isRecovered.message }}</p>
            <router-link to="/login">
                <button>Back to login</button>
            </router-link>
        </div>

        <div class="form__hint">
            <p>Remembered your password? <router-link to="/login">Sign in here.</router-link></p>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useRoute } from 'vue-router';
    import { useAuthStore } from '@/stores/AuthStore';

    const route = useRoute();
    const authStore = useAuthStore();

    const credentials = reactive({
        hash: route.params.hash,
        password: '',
        passwordConfirmation: '',
    });

    const errors = ref({});

    const isRecovered = reactive({
        status: false,
        message: ''
    });

    const submitForm = () => {
        errors.value = {};

        if (!credentials.password) errors.value.password = 'Please enter your password.';
        if (!credentials.passwordConfirmation) errors.value.passwordConfirmation = 'Please confirm your password.';
        if (credentials.password !== credentials.passwordConfirmation) errors.value.passwordConfirmation = 'Passwords do not match.';

        if (Object.keys(errors.value).length > 0) return;

        authStore.passwordReset(credentials)
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
                    errors.value.critical = error.message;
                }
            })
    };
</script>

<style lang="scss" scoped>
	@import '@/assets/styles/forms.scss';
</style>