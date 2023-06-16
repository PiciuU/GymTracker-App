<template>
    <div class="progress-container" v-if="route.meta.calculatorType == 'bmi'">
        <h1 class="progress__title">BMI Calculator</h1>

        <form class="progress__form" @submit.prevent="submitBmiForm">
            <div class="progress__input-group">
                <label for="weight" class="progress__label">Weight (kg)</label>
                <input type="number" id="weight" class="progress__input" v-model="data.weight" required/>
            </div>

            <div class="progress__input-group">
                <label for="height" class="progress__label">Height (cm)</label>
                <input type="number" id="height" class="progress__input" v-model="data.height" required/>
            </div>

            <button class="progress__button">Calculate BMI</button>
        </form>
        <div class="progress__result" v-if="data.bmi">
            <div>Your BMI is: {{ data.bmi }}</div>
            <div>Classification: {{ getBMICategory(data.bmi) }}</div>

            <div class="progress__hint">
                BMI Definition: Body Mass Index (BMI) is a measure of body fat based on height and weight that applies to adult men and women.
            </div>
        </div>
    </div>
    <div class="progress-container" v-else>
        <h1 class="progress__title">Calories Intake Calculator</h1>

        <form class="progress__form" @submit.prevent="submitIntakeForm">
            <div class="progress__input-group">
                <label for="weight" class="progress__label">Gender</label>
                <div class="progress__switch-group">
                    <input type="radio" id="male" class="progress__switch" value="male" v-model="data.gender">
                    <label for="male" class="progress__switch-label">Male</label>

                    <input type="radio" id="female" class="progress__switch" value="female" v-model="data.gender">
                    <label for="female" class="progress__switch-label">Female</label>
                </div>
            </div>

            <div class="progress__input-group">
                <label for="age" class="progress__label">Age</label>
                <input type="number" id="age" class="progress__input" v-model="data.age" required/>
            </div>

            <div class="progress__input-group">
                <label for="weight" class="progress__label">Weight (kg)</label>
                <input type="number" id="weight" class="progress__input" v-model="data.weight" required/>
            </div>

            <div class="progress__input-group">
                <label for="height" class="progress__label">Height (cm)</label>
                <input type="number" id="height" class="progress__input" v-model="data.height" required/>
            </div>

            <div class="progress__input-group">
                    <label for="activity" class="progress__label">
                        Physical activity:
                    </label>
                    <div class="modal__input-group">
                        <select class="progress__select" v-model="data.activity" required>
                        <option value="" selected>Select your physical activity...</option>
                        <option value="1.2">Sedentary: little or no exercise</option>
                        <option value="1.3">Light: exercise 1-3 times/week</option>
                        <option value="1.4">Moderate: exercise 4-5 times/week</option>
                        <option value="1.6">Active: daily exercise or intense exercise 3-4 times/week</option>
                        <option value="1.7">Very Active: intense exercise 6-7 times/week</option>
                        <option value="1.9">Extra Active: very intense exercise daily, or physical job</option>
                    </select>
                    </div>
                </div>

            <button class="progress__button">Calculate Intake</button>
        </form>
        <div class="progress__result" v-if="data.bmr && data.intake">
            <div>Your BMR* is: {{ data.bmr }} kcal</div>
            <div>Your Calories Intake is: {{ data.intake }} kcal</div>

            <div class="progress__hint">
                BMR Definition: Your Basal Metabolic Rate (BMR) is the number of calories you burn as your body performs basic (basal) life-sustaining function.
            </div>
        </div>
    </div>
</template>

<script setup>
    import { reactive } from 'vue';
    import { useRoute } from 'vue-router';

    const route = useRoute();
    const data = reactive({
        gender: 'male',
        age: null,
        weight: null,
        height: null,
        bmi: null,
        bmr: null,
        intake: null,
        activity: null,
    });

    const submitBmiForm = () => {
        const heightInMeters = data.height / 100;
        data.bmi = (data.weight / (heightInMeters * heightInMeters)).toFixed(2);
    };

    const submitIntakeForm = () => {
        if (data.gender === 'male')
            data.bmr = parseInt(66 + 13.7 * data.weight + 5 * data.height - 6.8 * data.age)
        else
            data.bmr = parseInt(665 + 9.6 * data.weight + 1.8 * data.height - 4.7 * data.age)

        data.intake = parseInt(data.bmr * data.activity);
    }

    const getBMICategory = (bmi) => {
        const bmiCategories = {
            16: "Severe Thinness",
            17: "Moderate Thinness",
            18.5: "Mild Thinness",
            25: "Normal",
            30: "Overweight",
            35: "Obese Class I",
            40: "Obese Class II",
            Infinity: "Obese Class III",
        };

        for (let threshold in bmiCategories) {
            if (bmi < parseFloat(threshold)) return bmiCategories[threshold];
        }

        return "Unknown";
    };

</script>

<style lang="scss" scoped>
    .progress-container {
        max-width: 500px;
        display: flex;
        width: 100%;
        height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: $--color-background;
        color: $--color-text;
        padding: 10px;
        margin: 0 auto;

    }
    .progress {

        &__title {
            text-align: center;
            font-size: 3.2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        &__form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        &__input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }

        &__select {
            width: 100%;
            margin-top: 5px;
            padding: 5px;
            border-radius: 5px;
            outline: none;
            background: $--color-overlay;
            border: 2px solid $--color-primary;
            color: $--color-text-muted-2;

            option {
                font-size: 1.2rem;
                color: $--color-text;
            }

            optgroup {
                color: $--color-primary;
                background: $--color-overlay;
            }
        }

        &__input {
            padding: 5px;
            font-size: 1.4rem;
            color: $--color-text-muted-1;
            background: transparent;
            border: none;
            border-bottom: 2px solid $--color-primary;
            outline: none;
            transition: all 0.25s ease-in-out;

            &::placeholder {
                color: $--color-text-muted-3;
            }

            &:focus {
                border-color: $--color-secondary;
            }

            &::-webkit-outer-spin-button, &::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        }

        &__switch {
            position: absolute !important;
            clip: rect(0, 0, 0, 0);
            height: 1px;
            width: 1px;
            border: 0;
            overflow: hidden;

            &-group {
                display: flex;
                flex-direction: row;
            }

            &-label {
                background-color: $--color-background;
                color: $--color-text;
                font-size: 1.6rem;
                line-height: 1;
                text-align: center;
                padding: 8px 16px;
                margin-right: -1px;
                border: 1px solid $--color-primary;
                box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
                transition: all 0.1s ease-in-out;

                &:hover {
                    cursor: pointer;
                }

                &:first-of-type {
                    border-radius: 4px 0 0 4px;
                }

                &:last-of-type {
                    border-radius: 0 4px 4px 0;
                }
            }

            &:checked + &-label {
                background-color: $--color-primary;
	            color: $--color-on-primary;
                font-weight: bold;
                box-shadow: none;
            }
        }

        &__button {
            margin-top: 20px;
            width: 100%;
            padding: 10px 20px;
            background-color: $--color-primary;
            color: $--color-on-primary;
            border: none;
            cursor: pointer;
            text-align: center;
            font-weight: bold;
            font-size: 1.6rem;
            transition: all 0.25s ease-in-out;

            &--secondary {
                background-color: $--color-secondary;
            }

        }

        &__result {
            margin: 10px 0px;
            color: $--color-secondary;
            width: 100%;
            text-align: center;
        }

        &__hint {
            color: $--color-primary;
            font-size: 1rem;
            margin-top: 20px;
            text-align: left;
        }

    }



</style>