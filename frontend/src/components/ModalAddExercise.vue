<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__title">
                Create new exercise
            </div>
            <div class="modal__subtitle">
                Fill in the details below to create a new exercise.
            </div>
        </div>
        <div class="modal__content">
            <form class="modal__form" id="modalForm" @submit.prevent="submitForm">
                <div class="modal__group">
                    <div class="modal__label">
                        Enter name of exercise:
                    </div>
                    <div class="modal__input-group">
                        <input type="text" class="modal__input" v-model="formData.name" placeholder="Name of exercise" required/>
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Enter description:
                    </div>
                    <div class="modal__input-group">
                        <textarea class="modal__input modal__input-area" v-model="formData.description" placeholder="Description" required></textarea>
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Choose targeted muscle group:
                    </div>
                    <div class="modal__input-group">
                        <select class="modal__select" v-model="formData.muscleGroup" required>
                        <option value="" selected>Select muscle group...</option>
                        <option v-for="(muscle, index) in dataStore.availableMuscleGroups" :key="index" :value="muscle">
                            {{ muscle }}
                        </option>
                    </select>
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Enter thumbnail:
                    </div>
                    <div class="modal__input-group">
                        <input type="text" class="modal__input" v-model="formData.thumbnailUrl" placeholder="Thumbnail">
                    </div>
                </div>
                <div class="modal__group">
                    <div class="modal__label">
                        Enter attachment:
                    </div>
                    <div class="modal__input-group">
                        <input type="text" class="modal__input" v-model="formData.attachmentUrl" placeholder="Attachment">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <div v-if="error" class="modal__error">{{ error }}</div>
            <button class="modal__button" form="modalForm" :disabled='dataStore.isLoading'>
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
                console.log('THEN');
                emit('close');
            })
            .catch((e) => {
                console.log(e);
                error.value = e.message
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';
</style>