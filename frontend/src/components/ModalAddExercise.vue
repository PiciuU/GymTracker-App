<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__header">
            <div class="header__close" @click="$emit('close')">
                <font-awesome-icon icon="xmark"/>
            </div>
            <div class="header__title">
                Create new exercise
            </div>
            <div class="header__subtitle">
                Fill in the details below to create a new exercise.
            </div>
        </div>
        <div class="modal__content">
            <form class="form form--alternative" id="modalForm" @submit.prevent="submitForm">
                <div class="form__group">
                    <label for="name">Enter name of exercise: </label>
                    <div class="form__input-group">
                        <input type="text" id="name" v-model="formData.name" placeholder="Name of exercise" required/>
                    </div>
                </div>

                <div class="form__group">
                    <label for="description">Enter description: </label>
                    <div class="form__input-group">
                        <textarea id="description" v-model="formData.description" placeholder="Description" required></textarea>
                    </div>
                </div>

                <div class="form__group">
                    <label for="muscle">Choose targeted muscle group: </label>
                    <div class="form__input-group">
                        <select id="muscle" v-model="formData.muscleGroup" required>
                            <option value="" selected>Select muscle group...</option>
                            <option v-for="(muscle, index) in dataStore.availableMuscleGroups" :key="index" :value="muscle">
                                {{ muscle }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form__group">
                    <label for="thumbnail">Enter thumbnail: </label>
                    <div class="form__input-group">
                        <input type="text" id="thumbnail" v-model="formData.thumbnailUrl" placeholder="Thumbnail">
                    </div>
                </div>

                <div class="form__group">
                    <label for="attachment">Enter attachment: </label>
                    <div class="form__input-group">
                        <input type="text" id="attachment" v-model="formData.attachmentUrl" placeholder="Attachment">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="footer__error">{{ error }}</div>
            <button class="footer__button" form="modalForm" :disabled='dataStore.isLoading'>
                Add exercise
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    onMounted(() => document.body.classList.add('disableScroll'));

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close'])

    const formData = reactive({
        name: null,
        description: null,
        muscleGroup: null,
        thumbnailUrl: null,
        attachmentUrl: null,
        isPublic: 1
    });

    const error = ref('');

    const submitForm = () => {
        dataStore.createExercise(formData)
            .then(() => {
                emit('close');
            })
            .catch((e) => {
                error.value = e.message
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/modals.scss';
</style>