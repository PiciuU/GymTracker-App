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
                            <span>{{ files.thumbnail?.name }}</span>
                        </div>
                    </div>
                </div>

                <div class="form__group">
                    <label>Upload attachment: </label>
                    <div class="form__input-group">
                        <div class="input__file">
                            <input type="file" id="attachment" @change="(event) => handleFileChange(event, 'attachment')" accept=".png, .jpg, .jpeg, .webp, .mp4, .gif, .webm">
                            <label for="attachment">Choose a file</label>
                            <span>{{ files.attachment?.name }}</span>
                        </div>
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
        isPublic: 1,
    });

    const files = reactive({
        thumbnail: null,
        attachment: null,
    })

    const error = ref('');

    const submitForm = async () => {
        let isErrorOccured = false;
        let exerciseId = null;
        await dataStore.createExercise(formData)
            .then((response) => {
                exerciseId = response.data.id;
            })
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
            await dataStore.uploadFiles(exerciseId, filesData);
        }
        emit('close');
    };

    const handleFileChange = (event, name) => {
        if (name == 'thumbnail') files.thumbnail = event.target.files[0];
        else files.attachment = event.target.files[0];
    };
</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/modals.scss';
</style>