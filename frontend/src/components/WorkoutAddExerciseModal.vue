<template>
    <div class="add-modal">
        <div class="add-modal__close" @click="$emit('close')">
            <font-awesome-icon icon="fa-solid fa-xmark" size="lg" />
        </div>
        <div class="add-modal__header">
            <div class="add-modal__title">
                Add exercise
            </div>
            <div class="add-modal__subtitle">
                Fill in the details below to add a new exercise to your workout plan.
            </div>
        </div>
        <div class="add-modal__content">
            <div class="add-modal__group">
                <div class="add-modal__label">
                    Choose exercise from list:
                </div>
                <select class="add-modal__select" v-model="selectedExercise">
                    <option value="">Select desired exercise...</option>
                    <optgroup v-for="(group, index) in groupedExercises" :key="index" :label="group.muscle_targeted">
                        <option v-for="item in group.items" :key="item.id" :value="item.id">{{ item.name }}</option>
                    </optgroup>
                </select>
                <div class="add-modal__hint">
                    Cant find your exercise? <router-link to="/">Add it by yourself.</router-link>
                </div>
            </div>
            <div class="add-modal__additional-data-container" v-if="selectedExercise">
                <div class="add-modal__group">
                    <div class="add-modal__label">
                        Enter number of sets and reps:
                    </div>
                    <div class="add-modal__input-group">
                        <input type="number" class="add-modal__input" v-model.number="sets" placeholder="Sets">
                        <input type="number" class="add-modal__input" v-model.number="reps" placeholder="Reps">
                    </div>
                </div>
                <div class="add-modal__group">
                    <div class="add-modal__label">
                        Enter your current weight record:
                    </div>
                    <div class="add-modal__input-group">
                        <input type="number" class="add-modal__input" v-model.number="weight" placeholder="Weight (kg)">
                    </div>
                </div>
            </div>
        </div>

        <div class="add-modal__footer">
            <div class="add-modal__button">
                Add exercise
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    let selectedExercise =  ref("");

    const exercises = [
        { id: 1, name: "Bicep Curl", muscle_targeted: "Biceps" },
        { id: 2, name: "Push-up", muscle_targeted: "Chest" },
        { id: 3, name: "Squat", muscle_targeted: "Legs" },
        { id: 4, name: "Crunch", muscle_targeted: "Abs" },
        { id: 5, name: "Deadlift", muscle_targeted: "Back" },
        { id: 6, name: "Dumbbell Fly", muscle_targeted: "Chest" },
        { id: 7, name: "Leg Press", muscle_targeted: "Legs" },
        { id: 8, name: "Plank", muscle_targeted: "Abs" },
        { id: 9, name: "Pull-up", muscle_targeted: "Back" },
    ];

    const groupedExercises = computed(() => {
        // Group exercises by muscle_targeted field
        const groups = {};
        exercises.forEach((exercise) => {
            if (!groups[exercise.muscle_targeted]) {
                groups[exercise.muscle_targeted] = {
                    muscle_targeted: exercise.muscle_targeted,
                    items: [],
                };
            }
            groups[exercise.muscle_targeted].items.push(exercise);
        });
        // Convert object to array and sort by muscle_targeted
        return Object.values(groups).sort((a, b) => {
            if (a.muscle_targeted < b.muscle_targeted) return -1;
            if (a.muscle_targeted > b.muscle_targeted) return 1;
            return 0;
        })
    });
</script>

<style lang="scss" scoped>
.add-modal {
    display: flex;
    flex-direction: column;
    position: absolute;
    width: 100%;
    min-height: 100%;
    box-shadow: 2px 2px 1px var(--color-primary);
    background: var(--color-overlay);
    border-radius: 5px;
    padding: 2rem;

    &__close {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        background: var(--color-primary);
        border-radius: 50%;
        box-shadow: 0 0 15px rgb(0 0 0 / 30%);
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1;

        svg {
            color: black;
        }
    }

    &__select {
        width: 100%;
        margin-top: 5px;
        padding: 5px 10px;
        border-radius: 5px;
        outline: none;
        background: var(--color-overlay);
        border: 2px solid var(--color-primary);
        background: url("data:image/svg+xml,<svg height='10px' width='10px' viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'><path d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/></svg>") no-repeat;
        background-position: calc(100% - 0.75rem) center;
        appearance: none;
        color: var(--color-text-muted-2);
        transition: all 0.25 ease-in-out;

        option {
            background: var(--color-overlay);
            font-size: 1.2rem;
            color: var(--color-text);
        }

        optgroup {
            color: var(--color-primary);
            background: var(--color-overlay);
        }
    }

    &__additional-data-container {
        margin-top: 10px;
    }

    &__input-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 5px;
    }

    &__input {
        padding: 5px;
        font-size: 1.4rem;
        color: var(--color-text-muted-1);
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--color-primary);
        outline: none;
        transition: all 0.25s ease-in-out;

        &::placeholder {
            color: var(--color-text-muted-3);
        }

        &:focus {
            border-color: var(--color-secondary);
        }

        &::-webkit-outer-spin-button, &::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    }

    &__hint {
        margin-top: 5px;
        color: var(--color-text-muted-2);
        font-size: 1rem;

        a {
            color: var(--color-secondary);
        }
    }

    &__group {
        margin-bottom: 10px;
    }

    &__header {
        margin-bottom: 10px;
    }

    &__title {
        font-size: 2rem;
        font-weight: bold;
    }

    &__subtitle {
        font-size: 1.2rem;
        color: var(--color-text-muted-1);
    }

    &__text {
        font-size: 1.4rem;
        color: var(--color-text-muted-2);
    }

    &__more {
        margin-top: 5px;
        font-size: 1.2rem;
        color:var(--color-secondary);
        opacity: .75;

        &:hover {
            opacity: 1;
        }
    }

    &__video {
        margin-top: 5px;
        width: 100%;
        border-radius: 10px;
    }

    &__footer {
        margin-top: auto;
    }

    &__button {
        padding: 10px 20px;
        margin: 0rem 20px;
        background: var(--color-primary);
        color: var(--color-on-primary);
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        box-shadow: 1px 1px 5px var(--color-background);
    }
}
</style>