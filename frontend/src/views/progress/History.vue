<template>
    <div class="container">
        <div class="section">
            <div class="section__card">
                <div class="card__title">
                    <h1>Choose exercise from list:</h1>
                </div>
                <form class="form form--alternative">
                    <select v-model="formData.exercise" required>
                        <option value="">Select desired exercise...</option>
                        <optgroup v-for="(group, index) in dataStore.getGroupedExercises" :key="index" :label="group.muscleGroup">
                            <option v-for="item in group.items" :key="item.id" :value="item">{{ item.name }}</option>
                        </optgroup>
                    </select>
                </form>
            </div>
            <div class="section" v-if="formData.exercise">
                <div class="section__card" v-if="!isFetching">
                    <div class="card__group">
                        <div class="card__title">
                            Name of exercise:
                        </div>
                        <div class="card__subtitle">
                            {{ formData.exercise.name }}
                        </div>
                    </div>
                    <div class="card__group">
                        <div class="card__title">
                            History:
                        </div>
                        <div class="card__list" v-if="sortedHistory.length > 0">
                            <div class="card__entry" v-for="(entry, index) in sortedHistory" :key="index">
                                <div class="card__subtitle">{{ entry.weight }} kg ({{ entry.date }}) {{ entry.status }}</div>
                                <div class="card__icon">
                                    <font-awesome-icon icon="pen" @click="modal.mode = 'edit'; modal.isVisible = true; modal.historyEntry = entry"/>
                                    <font-awesome-icon icon="trash" @click="deleteHistoryObject(entry)"/>
                                </div>
                            </div>
                        </div>
                        <div class="card__subtitle" v-else>You don't have any history for this exercise.</div>
                    </div>
                    <button class="card__button btn-small" @click="modal.mode = 'add'; modal.isVisible = true; modal.historyEntry = null">
                        Add entry
                    </button>
                </div>
                <div class="section__card" v-else>
                    <div class="card__title">
                        Data fetching...
                    </div>
                </div>
            </div>
        </div>

        <ModalAddHistory v-if="modal.isVisible && formData.exercise" :mode="modal.mode" :exercise="formData.exercise" :historyEntry="modal.historyEntry" @close="modal.isVisible = false" @add="addHistoryObject" @update="updateHistoryObject"/>
    </div>
</template>

<script setup>
    import { ref, reactive, computed, watch } from 'vue';
    import { useDataStore } from '@/stores/DataStore';
    import ModalAddHistory from '@/components/ModalAddHistory.vue';

    const dataStore = useDataStore();

    const modal = reactive({
        mode: null,
        isVisible: false,
        historyEntry: null,
    });

    const exerciseHistory = ref(null);

    const isFetching = ref(false);

    const formData = reactive({
        exercise: null,
    })

    const addHistoryObject = (obj) => {
       exerciseHistory.value.push(obj);
    };

    const updateHistoryObject = (obj) => {
        const exerciseToUpdate = exerciseHistory.value.find((exercise) => exercise.id === obj.id);
        Object.assign(exerciseToUpdate, obj);
    };

    const deleteHistoryObject = (obj) => {
        obj.status = "- Trying to delete entry...";
        dataStore.deleteExerciseHistory(obj.id)
            .then(() => {
                const index = exerciseHistory.value.findIndex((exercise) => exercise.id === obj.id);
                if (index !== -1) exerciseHistory.value.splice(index, 1);
            })
            .catch(() => {
                obj.status = "- Couldn't delete entry, try again later.";
            })
    };

    const sortedHistory = computed(() => {
        return exerciseHistory.value.sort((a, b) => new Date(b.date) - new Date(a.date));
    });

    watch(formData, async() => {
        if (formData.exercise) {
            isFetching.value = true;
            dataStore.fetchExerciseHistory(formData.exercise.id)
                .then((data) => {
                    exerciseHistory.value = data;
                })
                .finally(() => {
                    isFetching.value = false;
                })
        }
    });

</script>

<style lang="scss" scoped>
    @import '@/assets/styles/forms.scss';
    @import '@/assets/styles/sections.scss';

    .section__card {
        padding-bottom: 20px;
    }
</style>