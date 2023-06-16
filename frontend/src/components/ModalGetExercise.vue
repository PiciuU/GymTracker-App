<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="modal__header">
            <div class="modal__image">
                <div class="modal__title">
                    {{ exercise.name }}
                </div>
                <div class="modal__subtitle">
                    {{ exercise.isPublic ? 'Public exercise' : 'Private exercise' }}
                </div>
            </div>
        </div>
        <div class="modal__content" v-if="!editMode">
            <div class="modal__group">
                <div class="modal__label">
                    Description:
                </div>
                <div class="modal__text modal__text-area">
                    {{ exercise.description }}
                </div>
            </div>
            <div class="modal__group">
                <div class="modal__label">
                    Muscles Targeted:
                </div>
                <div class="modal__text">
                    {{ exercise.muscleGroup }}
                </div>
            </div>
            <div class="modal__group" v-if="exercise.attachmentUrl">
                <div class="modal__label">
                    Demonstration:
                </div>
                <video class="modal__video" controls>
                    <source src="@/assets/videos/exercises/biceps/uginanie.mp4" type="video/mp4" loading="lazy"/>
                    Your browser does not support mp4 videos.
                </video>
            </div>
        </div>
        <div class="modal__content" v-else>
            <form class="modal__form" id="modalForm" @submit.prevent="submitForm">
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
            <div v-if="authStore.user.id === exercise.userId">
                <button class="modal__button" @click="toggleEditMode(true)" v-if="!editMode">
                    Edit details
                </button>
                <div class="modal__button-group" v-else>
                    <button class="modal__button modal__button--secondary" @click="toggleEditMode(false)" :disabled="dataStore.isLoading">
                        Discard changes
                    </button>
                    <button class="modal__button" form="modalForm" :disabled="dataStore.isLoading" >
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const dataStore = useDataStore();

    let editMode = ref(false);

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const emit = defineEmits(['close'])

    const props = defineProps({
        exercise: { type: Object, required: true, default: '{}' }
    });

    let formData = reactive({
        description: props.exercise.description,
        muscleGroup: props.exercise.muscleGroup,
        attachmentUrl: props.exercise.attachmentUrl,
        thumbnailUrl: props.exercise.thumbnailUrl
    });

    let modal = ref(null);

    function toggleEditMode(status) {
        editMode.value = status;
        if (status === true) {
            formData = {
                description: props.exercise.description,
                muscleGroup: props.exercise.muscleGroup,
                attachmentUrl: props.exercise.attachmentUrl,
                thumbnailUrl: props.exercise.thumbnailUrl
            };
        }
    }

    onMounted(() => {
        document.body.classList.add('disableScroll');
        let elementRect = modal.value.getBoundingClientRect();
        let newHeight = Math.ceil(elementRect.height) % 2 === 0 ? Math.ceil(elementRect.height) : Math.ceil(elementRect.height) + 1;
        modal.value.style.height = newHeight.toString() + "px";
        modal.value.style.maxHeight = newHeight.toString() + "px";
    });

    const submitForm = () => {
        dataStore.updateExercise(props.exercise.id, formData)
            .then(() => {
                toggleEditMode(false);
            })
            .catch((e) => {
                console.log(e);
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/modal.scss';

    .modal__title {
        font-size: 2.5rem;
        font-weight: bold;
        letter-spacing: 0.2rem;
    }

    .modal__subtitle {
        color: var(--color-text-muted-1);
    }
</style>