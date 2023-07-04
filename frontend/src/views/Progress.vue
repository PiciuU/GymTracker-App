<template>
    <div class="container container-wrap">
        <SectionCard path="progress/history" image="overlay/history">
            History
        </SectionCard>

        <SectionCard path="progress/calculator/bmi" image="overlay/bmi-calculator">
            BMI
        </SectionCard>

        <SectionCard path="progress/calculator/calorie" image="overlay/calories-calculator">
            Calorie Intake
        </SectionCard>
    </div>
</template>

<script setup>
    import SectionCard from "@/components/SectionCard.vue";
</script>