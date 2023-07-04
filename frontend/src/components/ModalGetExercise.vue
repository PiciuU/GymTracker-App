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
                    {{ exercise.isPublic ? 'Public exercise' : 'Private exercise' }}
                </div>
            </div>
        </div>
        <div class="modal__content" v-if="!editMode">
            <div class="content__group">
                <div class="content__title">
                    Name of exercise:
                </div>
                <div class="content__subtitle ">
                    {{ exercise.name }}
                </div>
            </div>
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
        </div>
        <div class="modal__content" v-else>
            <form class="form form--alternative" id="modalForm" @submit.prevent="submitForm">
                <div class="form__group">
                    <label for="name">Enter name of exercise: </label>
                    <div class="form__input-group">
                        <input type="text" id="name" v-model="formData.name" placeholder="Name" required/>
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

                <div class="form__group" v-if="props.exercise.isPublic == 0">
                    <label>Exercise availability: </label>
                    <div class="form__switch-group form__switch-group--alternative">
                        <input type="radio" id="public" value="1" v-model="formData.isPublic">
                        <label for="public">Public</label>

                        <input type="radio" id="private" value="0" v-model="formData.isPublic">
                        <label for="private">Private</label>
                    </div>
                </div>

                <div class="form__group">
                    <label>Upload thumbnail: </label>
                    <div class="form__input-group">
                        <div class="input__file">
                            <input type="file" id="thumbnail" @change="(event) => handleFileChange(event, 'thumbnail')" accept=".png, .jpg, .jpeg, .webp">
                            <label for="thumbnail">Choose a file</label>
                            <span>{{ files.thumbnail?.name || formData.thumbnailUrl }}</span>
                            <div v-if="formData.thumbnailUrl">
                                <font-awesome-icon icon="trash" @click="formData.thumbnailUrl = null" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form__group">
                    <label>Upload attachment: </label>
                    <div class="form__input-group">
                        <div class="input__file">
                            <input type="file" id="attachment" @change="(event) => handleFileChange(event, 'attachment')" accept=".png, .jpg, .jpeg, .webp, .mp4, .gif, .webm">
                            <label for="attachment">Choose a file</label>
                            <span>{{ files.attachment?.name || formData.attachmentUrl }}</span>
                            <div v-if="formData.attachmentUrl">
                                <font-awesome-icon icon="trash" @click="formData.attachmentUrl = null" />
                            </div>
                        </div>
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
        exercise: { type: Object, required: true, default: {} }
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

    let formData = reactive({});

    const error = ref('');

    function toggleEditMode(status) {
        editMode.value = status;
        if (status === true) {
            Object.assign(formData, {
                name: props.exercise.name,
                description: props.exercise.description,
                muscleGroup: props.exercise.muscleGroup,
                attachmentUrl: props.exercise.attachmentUrl,
                thumbnailUrl: props.exercise.thumbnailUrl,
                isPublic: props.exercise.isPublic
            });
            Object.assign(files, {
                thumbnail: null,
                attachment: null,
            })
        }
    }

    const submitForm = async () => {
        let isErrorOccured = false;
        await dataStore.updateExercise(props.exercise.id, formData)
            .catch((e) => {
                isErrorOccured = true;
                error.value = e.message
            })
        if (isErrorOccured) return;

        // Upload files
        if (files.thumbnail || files.attachment) {
            const filesData = new FormData();
            if (files.thumbnail) filesData.append('thumbnailFile', files.thumbnail);
            if (files.attachment) filesData.append('attachmentFile', files.attachment);
            await dataStore.uploadFiles(props.exercise.id, filesData);
        }
        // Delete files
        else if (props.exercise.thumbnailUrl !== formData.thumbnailUrl || props.exercise.attachmentUrl !== formData.attachmentUrl) {
            await dataStore.deleteFiles(props.exercise.id, { 'thumbnailUrl': formData.thumbnailUrl, 'attachmentUrl': formData.attachmentUrl });
        }
        toggleEditMode(false);
    };


    /* File Upload */
    const files = reactive({});

    const handleFileChange = (event, name) => {
        if (name == 'thumbnail') files.thumbnail = event.target.files[0];
        else files.attachment = event.target.files[0];
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/modals.scss';
    .modal__header .header__title {
        font-size: 2.5rem;
        letter-spacing: 0.2rem;
        overflow: hidden;
        padding: 0px 15px;
        text-align: center;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: 100%;
    }

    .modal__header .header__subtitle {
        font-size: 1.4rem;
    }
</style>