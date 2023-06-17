<template>
    <div class="workout-container">
        <div class="workout">
            <div class="workout__banner">
                <form class="modal__form" @submit.prevent="updateName">
                    <div class="modal__group">
                        <div class="modal__label">
                            Your name:
                        </div>
                        <div class="modal__input-group">
                            <input type="text" class="modal__input" v-model="formData.name" placeholder="Name" required/>
                        </div>
                    </div>
                    <div v-if="errors.nameForm" class="modal__error">{{ errors.nameForm }}</div>
                    <button class="modal__button" :disabled='formData.isNameLoading'>
                        Change name
                    </button>
                </form>
            </div>
            <div class="workout__banner">
                <form class="modal__form" @submit.prevent="updateMail">
                    <div class="modal__group">
                        <div class="modal__label">
                            Your email:
                        </div>
                        <div class="modal__input-group">
                            <input type="email" class="modal__input" v-model="formData.email" placeholder="Email" required/>
                        </div>
                    </div>
                    <div v-if="errors.emailForm" class="modal__error">{{ errors.emailForm }}</div>
                    <button class="modal__button" :disabled='formData.isMailLoading'>
                        Change email
                    </button>
                </form>
            </div>
            <div class="workout__banner">
                <form class="modal__form" @submit.prevent="updatePassword">
                    <div class="modal__group">
                        <div class="modal__label">
                            Change password:
                        </div>
                        <div class="modal__input-group">
                            <input type="password" class="modal__input" v-model="formData.passwordCurrent" placeholder="Current password" required/>
                            <input type="password" class="modal__input" v-model="formData.password" placeholder="New password" required/>
                            <input type="password" class="modal__input" v-model="formData.passwordConfirmation" placeholder="Confirm new password" required/>
                        </div>
                    </div>
                    <div v-if="errors.passwordForm" class="modal__error">{{ errors.passwordForm }}</div>
                    <button class="modal__button" :disabled='formData.isPasswordLoading'>
                        Change password
                    </button>
                </form>
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
        isNameLoading: false,
        isMailLoading: false,
        isPasswordLoading: false,
        name: authStore.user.name,
        email: authStore.user.email,
        passwordCurrent: null,
        password: null,
        passwordConfirmation: null
    });

    const updateName = () => {
        errors.value.nameForm = '';
        if (formData.name === authStore.user.name) return;

        formData.isNameLoading = true;
        authStore.changeName({ name: formData.name })
            .catch((e) => {
                errors.value.nameForm = e.data.name[0] ?? e.message;
            })
            .finally(() => {
                formData.isNameLoading = false;
            })
    };

    const updateMail = () => {
        errors.value.emailForm = '';
        if (formData.email === authStore.user.email) return;
        formData.isMailLoading = true;
        authStore.changeMail({ email: formData.email })
            .catch((e) => {
                errors.value.emailForm = e.data.email[0] ?? e.message;
            })
            .finally(() => {
                formData.isMailLoading = false;
            })
    };

    const updatePassword = () => {
        errors.value.passwordForm = '';
        if (formData.passwordConfirmation !== formData.password) {
            errors.value.passwordForm = 'Passwords do not match.';
            return;
        }
        formData.isPasswordLoading = true;
        authStore.changePassword({
            passwordCurrent: formData.passwordCurrent,
            password: formData.password,
            passwordConfirmation: formData.passwordConfirmation
        })
            .catch((e) => {
                errors.value.passwordForm = e.data.password[0] ?? e.message;
            })
            .finally(() => {
                formData.isPasswordLoading = false;
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';
.workout-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    align-content: center;
    gap: 50px;
    padding: 20px 0px;
}

.workout {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    max-width: 600px;
    width: 100%;
    gap: 10px;

    &__banner {
        background: var(--color-overlay);
        border-radius: 5px;
        padding: 10px 15px;

        &-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    }


    &__title {
        font-size: 1.6rem;
        font-weight: bold;
    }

    &__subtitle {
        color: var(--color-text-muted-2);
        font-size: 1.2rem;
    }

    &__label {
        display: inline-flex;
        margin-bottom: 15px;

        &:after {
            background: var(--color-primary);
            bottom: -5px;
            content: '';
            height: 1px;
            left: 0;
            position: absolute;
            width: 150%;
        }
    }

    &__items {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    &__icon {
        cursor: pointer;

        &--active {
            color:var(--color-primary);
        }
    }
}
</style>