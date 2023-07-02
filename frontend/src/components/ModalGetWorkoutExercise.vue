<template>
    <div class="modal-overlay" @click="$emit('close')"></div>
    <div class="modal" ref="modal">
        <div class="modal__header">
            <div class="header__close" @click="$emit('close')">
                <font-awesome-icon icon="xmark"/>
            </div>
            <div class="header__image">
                <img :src="setImage(exercise.thumbnailUrl)" @error="setAltImage"/>
                <div class="header__title">
                    {{ exercise.name }}
                </div>
                <div class="header__subtitle">
                    {{ exercise.sets ?? 0 }} series, {{ exercise.reps ?? 0 }} reps
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
                <video class="content__video" controls v-if="!isImageFile(exercise.attachmentUrl)">
                    <source :src="setVideo()" type="video/mp4" loading="lazy"/>
                    Your browser does not support mp4 videos.
                </video>
                <img class="content__image" :src="setImage(exercise.attachmentUrl)" @error="setAltImage" v-else />
            </div>
            <div class="content__group" v-if="exercise.restTime">
                <div class="content__title">
                    Rest time:
                </div>
                <div class="content__subtitle">
                    {{ exercise.restTime }} min
                </div>
            </div>
            <div class="content__group" v-if="exercise.weight">
                <div class="content__title">
                    Weight for exercise:
                </div>
                <div class="content__subtitle">
                    {{ exercise.weight }} kg
                </div>
            </div>
            <div class="content__group" v-if="exercise.personalBest">
                <div class="content__title">
                    Personal best:
                </div>
                <div class="content__subtitle">
                    {{ exercise.personalBest.weight }} kg ({{ exercise.personalBest.date }})
                </div>
                <div class="content__hint">
                    <p>Want to check your progress? <router-link to="/progress/history">View personal history.</router-link></p>
                </div>
            </div>
        </div>
        <div class="modal__content" v-else>
            <form class="form form--alternative" id="modalForm" @submit.prevent="submitForm">
                <div class="form__group">
                    <label for="sets">Enter number of sets and reps: </label>
                    <div class="form__input-group">
                        <input type="number" id="sets" v-model.number="formData.sets" placeholder="Sets" required>
                    </div>
                    <div class="form__input-group">
                        <input type="number" id="reps" v-model.number="formData.reps" placeholder="Reps" required>
                    </div>
                </div>

                <div class="form__group">
                    <label for="restTime">Enter rest time between sets: </label>
                    <div class="form__input-group">
                        <input type="text" id="restTime" v-model="formData.restTime" pattern="[0-5][0-9]:[0-5][0-9]" placeholder="(Optional) Rest Time (mm:ss)">
                    </div>
                </div>

                <div class="form__group">
                    <label for="weight">Enter weight for exercise: </label>
                    <div class="form__input-group">
                        <input type="number" id="weight" v-model.number="formData.weight" placeholder="(Optional) Weight (kg)" step="any">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
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
</template>

<script setup>
    import { ref, reactive, onMounted, onUnmounted } from 'vue';
    import { useDataStore } from '@/stores/DataStore';

    const modal = ref(null);
    onMounted(() => {
        document.body.classList.add('disableScroll');
        let elementRect = modal.value.getBoundingClientRect();
        let newHeight = Math.ceil(elementRect.height) % 2 === 0 ? Math.ceil(elementRect.height) : Math.ceil(elementRect.height) + 1;
        modal.value.style.height = newHeight.toString() + "px";
        modal.value.style.maxHeight = newHeight.toString() + "px";
    });

    onUnmounted(() => document.body.classList.remove('disableScroll'));

    const dataStore = useDataStore();

    const emit = defineEmits(['close'])

    const props = defineProps({
        workout: { type: Object, required: true, default: '{}' },
        exercise: { type: Object, required: true, default: '{}' }
    });

    const setImage = (imageName) => {
        if (props.exercise.thumbnailUrl) return `${dataStore.getPath}/${props.exercise.id}/${imageName}`;
        else return `${dataStore.getPath}/missing.jpg`;
    };

    const setAltImage = (event) => {
        event.target.src = `${dataStore.getPath}/missing.jpg`;
    };

    const isImageFile = (url) => {
      const imageExtensions = ['.gif', '.png', '.jpeg', '.jpg', '.webp'];
      return imageExtensions.some(ext => url.toLowerCase().endsWith(ext));
    }

    const setVideo = () => {
        return `${dataStore.getPath}/${props.exercise.id}/${props.exercise.attachmentUrl}`;
    }

    const editMode = ref(false);

    let formData = reactive({
        sets: props.exercise.sets,
        reps: props.exercise.reps,
        weight: props.exercise.weight,
        restTime: props.exercise.restTime
    });

    const error = ref('');

    function toggleEditMode(status) {
        editMode.value = status;
        if (status === true) {
            formData = {
                sets: props.exercise.sets,
                reps: props.exercise.reps,
                weight: props.exercise.weight,
                restTime: props.exercise.restTime
            };
        }
    }

    const submitForm = () => {
        dataStore.updateWorkoutExercise(props.workout.id, props.exercise.id, formData)
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