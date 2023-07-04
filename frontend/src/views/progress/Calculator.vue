<template>
    <div class="container container-fixed" v-if="route.meta.calculatorType == 'bmi'">
        <h1>BMI Calculator</h1>

        <form class="form form--alternative" @submit.prevent="submitForm">
            <div class="form__group">
                <label for="weight">Weight (kg)</label>
                <div class="form__input-group">
                    <input type="number" id="weight" v-model="data.weight" autocomplete="off" required/>
                </div>
            </div>

            <div class="form__group">
                <label for="height">Height (cm)</label>
                <div class="form__input-group">
                    <input type="number" id="height" v-model="data.height" autocomplete="off" required/>
                </div>
            </div>

            <button>Calculate BMI</button>
        </form>
        <div class="form__result" v-if="data.bmi">
            <p>Your BMI is: {{ data.bmi }}</p>
            <p>Classification: {{ getBMICategory(data.bmi) }}</p>

            <p class="hint">
                BMI Definition: Body Mass Index (BMI) is a measure of body fat based on height and weight that applies to adult men and women.
            </p>
        </div>
    </div>

    <div class="container container-fixed" v-else>
        <h1>Calories Intake Calculator</h1>

        <form class="form form--alternative" @submit.prevent="submitForm">
            <div class="form__group">
                <label>Gender</label>
                <div class="form__switch-group">
                    <input type="radio" id="male" value="male" v-model="data.gender">
                    <label for="male">Male</label>

                    <input type="radio" id="female" value="female" v-model="data.gender">
                    <label for="female">Female</label>
                </div>
            </div>

            <div class="form__group">
                <label for="age">Age</label>
                <div class="form__input-group">
                    <input type="number" id="age" v-model="data.age" autocomplete="off" required/>
                </div>
            </div>

            <div class="form__group">
                <label for="weight">Weight (kg)</label>
                <div class="form__input-group">
                    <input type="number" id="weight" v-model="data.weight" autocomplete="off" required/>
                </div>
            </div>

            <div class="form__group">
                <label for="height">Height (cm)</label>
                <div class="form__input-group">
                    <input type="number" id="height" v-model="data.height" autocomplete="off" required/>
                </div>
            </div>

            <div class="form__group">
                <label for="activity">Physical activity: </label>
                <div class="form__input-group">
                    <select class="select--dark" id="activity" v-model="data.activity" required>
                       <option value="null" selected>Select your physical activity...</option>
                       <option value="1.2">Sedentary: little or no exercise</option>
                       <option value="1.3">Light: exercise 1-3 times/week</option>
                       <option value="1.4">Moderate: exercise 4-5 times/week</option>
                       <option value="1.6">Active: daily exercise or intense exercise 3-4 times/week</option>
                       <option value="1.7">Very Active: intense exercise 6-7 times/week</option>
                       <option value="1.9">Extra Active: very intense exercise daily, or physical job</option>
                    </select>
                </div>
            </div>

            <button>Calculate Intake</button>
        </form>
        <div class="form__result" v-if="data.bmr && data.intake">
            <p>Your BMR is: {{ data.bmr }} kcal</p>
            <p>Your Calories Intake is: {{ data.intake }} kcal</p>

            <p class="hint">
                BMR Definition: Your Basal Metabolic Rate (BMR) is the number of calories you burn as your body performs basic (basal) life-sustaining function.
            </p>
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

    const submitForm = () => {
        if (route.meta.calculatorType === 'bmi') {
            const heightInMeters = data.height / 100;
            data.bmi = (data.weight / (heightInMeters * heightInMeters)).toFixed(2);
        } else {
            if (data.gender === 'male') data.bmr = parseInt(66 + 13.7 * data.weight + 5 * data.height - 6.8 * data.age)
            else data.bmr = parseInt(665 + 9.6 * data.weight + 1.8 * data.height - 4.7 * data.age)
            data.intake = parseInt(data.bmr * data.activity);
        }
    };

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
    @import '@/assets/styles/forms.scss';
</style>