<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__header">
            <div class="header__close" @click="$emit('close')">
                <font-awesome-icon icon="xmark"/>
            </div>
            <div class="header__image">
                <img :src="setImage()" @error="setAltImage"/>
                <div class="header__title">
                    {{ exercise.name }}
                </div>
                <div class="header__subtitle">
                    {{ exercise.isPublic ? 'Public exercise' : 'Private exercise' }}
                </div>
            </div>
        </div>
        <div class="modal__content" v-if="!editMode">
            <div class="content__group">
                <div class="content__title">
                    Description:
                </div>
                <div class="content__subtitle  content__subtitle--sized">
                    {{ exercise.description }}
                </div>
            </div>
            <div class="content__group">
                <div class="content__title">
                    Muscles Targeted:
                </div>
                <div class="content__subtitle ">
                    {{ exercise.muscleGroup }}
                </div>
            </div>
            <div class="content__group" v-if="exercise.attachmentUrl">
                <div class="content__title">
                    Demonstration:
                </div>
                <video class="content__video" controls>
                    <source :src="setVideo()" type="video/mp4" loading="lazy"/>
                    Your browser does not support mp4 videos.
                </video>
            </div>
        </div>
        <div class="modal__content" v-else>
            <form class="form form--alternative" id="modalForm" @submit.prevent="submitForm">
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
            <div v-if="authStore.user.id === exercise.userId">
                <button class="footer__button" @click="toggleEditMode(true)" v-if="!editMode">
                    Edit details
                </button>
                <div class="footer__group" v-else>
                    <div v-if="error" class="footer__error">{{ error }}</div>
                    <button class="footer__button footer__button--secondary" @click="toggleEditMode(false)" :disabled="dataStore.isLoading">
                        Discard changes
                    </button>
                    <button class="footer__button" form="modalForm" :disabled="dataStore.isLoading" >
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

    const modal = ref(null);
    onMounted(() => {
        document.body.classList.add('disableScroll');
        let elementRect = modal.value.getBoundingClientRect();
        let newHeight = Math.ceil(elementRect.height) % 2 === 0 ? Math.ceil(elementRect.height) : Math.ceil(elementRect.height) + 1;
        modal.value.style.height = newHeight.toString() + "px";
        modal.value.style.maxHeight = newHeight.toString() + "px";
    });

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const authStore = useAuthStore();
    const dataStore = useDataStore();

    const emit = defineEmits(['close'])

    const props = defineProps({
        exercise: { type: Object, required: true, default: '{}' }
    });

    const setImage = () => {
        return `${import.meta.env.BASE_URL}images/exercises/${props.exercise.thumbnailUrl}.jpg`;
    };

    const setAltImage = (event) => {
        event.target.src = `${import.meta.env.BASE_URL}images/missing.jpg`;
    };

    const setVideo = () => {
        return `${import.meta.env.BASE_URL}videos/exercises/${props.exercise.attachmentUrl}.mp4`;
    }

    const editMode = ref(false);

    let formData = reactive({
        description: props.exercise.description,
        muscleGroup: props.exercise.muscleGroup,
        attachmentUrl: props.exercise.attachmentUrl,
        thumbnailUrl: props.exercise.thumbnailUrl
    });

    const error = ref('');

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

    const submitForm = () => {
        dataStore.updateExercise(props.exercise.id, formData)
            .then(() => {
                toggleEditMode(false);
            })
            .catch((e) => {
                error.value = e.message
            })
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/modals.scss';
    .modal__header .header__title {
        font-size: 2.5rem;
        letter-spacing: 0.2rem;
    }

    .modal__header .header__subtitle {
        font-size: 1.4rem;
    }
</style>