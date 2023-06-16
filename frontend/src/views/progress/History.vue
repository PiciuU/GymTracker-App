<template>
    <div class="workout-container">
        <div class="workout">
            <div class="workout__banner">
                <div class="modal__group">
                    <div class="modal__label">
                        Choose exercise from list:
                    </div>
                    <select class="modal__select" v-model="formData.exercise" required>
                        <option value="">Select desired exercise...</option>
                        <optgroup v-for="(group, index) in groupedExercises" :key="index" :label="group.muscleGroup">
                            <option v-for="item in group.items" :key="item.id" :value="item">{{ item.name }}</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="workout" v-if="formData.exercise">
                <div class="workout__banner">
                    <div class="modal__group" v-if="!isFetching">
                        <div class="modal__label">
                            Name of exercise:
                        </div>
                        <div class="modal__text">
                            {{ formData.exercise.name }}
                        </div>
                        <div class="modal__label">
                            History:
                        </div>
                        <div class="modal__text" v-for="(entry, index) in sortedHistory" :key="index">
                            {{ entry.weight }} kg ({{ entry.date }})
                            <font-awesome-icon class="workout__icon" icon="fa-solid fa-pen" @click="modal.mode = 'edit'; modal.isVisible = true; modal.historyEntry = entry"/>
                        </div>
                        <button class="modal__button" @click="modal.mode = 'add'; modal.isVisible = true">
                            Add entry
                        </button>
                    </div>
                    <div class="modal__group" v-else>
                        <div class="modal__label">
                            Data fetching...
                        </div>
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

    let exerciseHistory = ref(null);

    let isFetching = ref(false);

    const formData = reactive({
        exercise: null,
    })

    function addHistoryObject(obj) {
       exerciseHistory.value.push(obj);
    }

    function updateHistoryObject(obj) {
        const exerciseToUpdate = exerciseHistory.value.find((exercise) => exercise.id === obj.id);
        Object.assign(exerciseToUpdate, obj);
    }

    const sortedHistory = computed(() => {
        return exerciseHistory.value.sort((a, b) => new Date(b.date) - new Date(a.date));
    })

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
    })

    const groupedExercises = computed(() => {
        const groups = {};
        dataStore.getExercises.forEach((exercise) => {
            if (!groups[exercise.muscleGroup]) {
                groups[exercise.muscleGroup] = {
                muscleGroup: exercise.muscleGroup,
                items: [],
                };
            }
            groups[exercise.muscleGroup].items.push(exercise);
        });

        return Object.values(groups).sort((a, b) => {
            if (a.muscleGroup < b.muscleGroup) return -1;
            if (a.muscleGroup > b.muscleGroup) return 1;
            return 0;
        });
    });

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