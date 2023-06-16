<template>
    <div class="card-container">
        <SectionCard :path="'progress/history'" :image="`overlay/history`">
            History
        </SectionCard>
        <SectionCard :path="'progress/calculator/bmi'" :image="`overlay/bmi-calculator`">
            BMI
        </SectionCard>
        <SectionCard :path="'progress/calculator/calorie'" :image="`overlay/calories-calculator`">
            Calorie Intake
        </SectionCard>
    </div>
</template>

<script setup>
    import SectionCard from "@/components/SectionCard.vue";
</script>

<style lang="scss" scoped>
    .card-container {
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

    @media screen and (min-width: $--breakpoint-large-devices) {
        .card-container {
            flex-direction: row;
            flex-wrap: wrap;
        }
    }
</style>
