<template>
    <div class="container">
        <div class="section">
            <div class="section__card">
                <form class="form form--alternative" @submit.prevent="updateName" v-if="!formData.isNameLoaded">
                    <div class="form__group">
                        <label for="name">Your name:</label>
                        <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.name }">
                            <input type="text" id="name" v-model="formData.name" placeholder="Name" required/>
                            <p v-if="errors.name">{{ errors.name }}</p>
                        </div>
                    </div>
                    <button class="btn-small" :disabled='formData.isNameLoading'>Change name</button>
                </form>
                <div class="form__success" v-else>
                    <p>Name has been successfully updated.</p>
                </div>
            </div>

            <div class="section__card">
                <form class="form form--alternative" @submit.prevent="updateMail" v-if="!formData.isMailLoaded">
                    <div class="form__group">
                        <label for="email">Your email:</label>
                        <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.email }">
                            <input type="email" id="email" v-model="formData.email" placeholder="Email" required/>
                            <p v-if="errors.email">{{ errors.email }}</p>
                        </div>
                    </div>
                    <button class="btn-small" :disabled='formData.isMailLoading'>Change email</button>
                </form>
                <div class="form__success" v-else>
                    <p>Email has been successfully updated.</p>
                </div>
            </div>

            <div class="section__card">
                <form class="form form--alternative" @submit.prevent="updatePassword" v-if="!formData.isPasswordLoaded">
                    <div class="form__group">
                        <label for="passwordCurrent">Change password:</label>
                        <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.passwordCurrent }">
                            <input type="password" id="passwordCurrent" v-model="formData.passwordCurrent" placeholder="Current password" required/>
                            <p v-if="errors.passwordCurrent">{{ errors.passwordCurrent }}</p>
                        </div>
                        <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.password }">
                            <input type="password" id="password" v-model="formData.password" placeholder="New password" required/>
                            <p v-if="errors.password">{{ errors.password }}</p>
                        </div>
                        <div class="form__input-group-with-error" :class="{ 'form__input-group-has-error': errors.passwordConfirmation }">
                            <input type="password" id="passwordConfirmation" v-model="formData.passwordConfirmation" placeholder="Confirm new password" required/>
                            <p v-if="errors.passwordConfirmation">{{ errors.passwordConfirmation }}</p>
                        </div>
                    </div>
                    <button class="btn-small" :disabled='formData.isPasswordLoading'>Change password</button>
                </form>
                <div class="form__success" v-else>
                    <p>Password has been successfully updated.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const errors = ref({});

    const formData = reactive({
        name: authStore.user.name,
        isNameLoading: false,
        isNameLoaded: false,

        email: authStore.user.email,
        isMailLoading: false,
        isMailLoaded: false,

        passwordCurrent: null,
        password: null,
        passwordConfirmation: null,
        isPasswordLoading: false,
        isPasswordLoaded: false,
    });

    const updateName = () => {
        errors.value.name = '';
        if (formData.name === authStore.user.name) return;

        formData.isNameLoading = true;
        authStore.changeName({ name: formData.name })
            .then(() => {
                formData.isNameLoaded = true;
            })
            .catch((e) => {
                errors.value.name = e.data.name[0] ?? e.message;
            })
            .finally(() => {
                formData.isNameLoading = false;
            })
    };

    const updateMail = () => {
        errors.value.email = '';
        if (formData.email === authStore.user.email) return;

        formData.isMailLoading = true;
        authStore.changeMail({ email: formData.email })
            .then(() => {
                formData.isMailLoaded = true;
            })
            .catch((e) => {
                errors.value.email = e.data.email[0] ?? e.message;
            })
            .finally(() => {
                formData.isMailLoading = false;
            })
    };

    const updatePassword = () => {
        errors.value.passwordCurrent = '';
        errors.value.password = '';
        errors.value.passwordConfirmation = '';
        if (formData.passwordConfirmation !== formData.password) {
            errors.value.passwordConfirmation = 'Passwords do not match.';
            return;
        }

        formData.isPasswordLoading = true;
        authStore.changePassword({
            passwordCurrent: formData.passwordCurrent,
            password: formData.password,
            passwordConfirmation: formData.passwordConfirmation
        })
            .then(() => {
                formData.isPasswordLoaded = true;
            })
            .catch((e) => {
                if (e.data) {
                    const responseErrors = e.data;

                    Object.keys(responseErrors).forEach((field) => {
                        errors.value[field] = responseErrors[field][0];
                    });
                }
                else {
                    errors.value.passwordCurrent = e.message;
                }
            })
            .finally(() => {
                formData.isPasswordLoading = false;
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/sections.scss';

    .section__card {
        padding-bottom: 15px;
        margin-bottom: 10px;
    }
</style>