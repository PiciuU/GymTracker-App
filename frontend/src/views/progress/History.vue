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
                        <div v-if="sortedHistory.length > 0">
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
                    <button class="card__button" @click="modal.mode = 'add'; modal.isVisible = true; modal.historyEntry = null">
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
    @import '@/assets/styles/modals.scss';

    .container {
        align-content: center;
        align-items: center;
        display: flex;
        flex-direction: column;
        gap: 50px;
        height: 100%;
        justify-content: center;
        padding: 20px 0px;
        width: 100%;
    }

    .section {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        gap: 10px;
        max-width: 600px;
        width: 100%;

        &__card {
            background: $--color-overlay;
            border-radius: 5px;
            padding: 10px 15px;

            .card {
                &__title {
                    align-items: center;
                    display: flex;
                    justify-content: space-between;

                    h1 {
                        font-size: 1.6rem;
                        margin-bottom: 0px;
                        font-weight: normal;
                    }
                }

                &__subtitle {
                    color: $--color-text-muted-2;
                    font-size: 1.2rem;
                }

                &__group {
                    margin-bottom: 10px;
                }

                &__entry {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 1px solid $--color-text-muted-3;
                    margin: 5px 0px;

                    svg {
                        font-size: 1.4rem;
                        color: $--color-text-muted-3;
                        cursor: pointer;

                        &:hover {
                            color: $--color-primary;
                        }

                        &:first-of-type {
                            margin-right: 30px;
                        }
                    }
                }

                &__button {
                    width: 100%;
                    background-color: $--color-primary;
                    border: none;
                    cursor: pointer;
                    font-size: 1.6rem;
                    font-weight: bold;
                    padding: 10px 20px;
                    transition: all .25s ease-in-out;

                    &:disabled {
                        background: rgba(255, 255, 255, 0.12);
                        color: rgba(255,255,255,0.5);
                        cursor: wait;
                        letter-spacing: 3px;

                        &:after {
                            animation: spin 1s linear infinite;
                            border: 2px solid white;
                            border-radius: 50%;
                            border-top-color: transparent;
                            content: '';
                            height: 20px;
                            left: 50%;
                            position: absolute;
                            top: 50%;
                            transform: translate(-50%, -50%);
                            width: 20px;
                        }
                    }
                }
            }
        }
    }
</style>