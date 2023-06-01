<template>
    <div class="container">
        <div class="banner">
            <h1 class="banner__title">{{ currentDay }}</h1>
            <h2 class="banner__subtitle">{{ musclesTargeted }}</h2>
        </div>

        <div class="muscle-group" v-for="(group, index) in groupedItems" :key="index">
            <div class="muscle-group__heading">{{ group.muscle_targeted }}</div>
            <div class="exercise-group">
                <WorkoutItemPreview v-for="item in group.items" :key="item.id" :data="[item.muscle_targeted, item.name]" @click="showDetails(item)">
                    <template v-slot:title>{{ item.name }}</template>
                    <template v-slot:subtitle>{{ item.sets }} series, {{ item.reps }} reps</template>
                </WorkoutItemPreview>
            </div>
        </div>

        <ActionButton @click="showAddModal()"/>

        <WorkoutAddExerciseModal v-if="!isAddModalVisisble" @close="isAddModalVisisble = false"/>

        <WorkoutItemDetails v-if="isDetailsVisible" :item="selectedItem" @close="isDetailsVisible = false"/>
    </div>
</template>

<script setup>
    import WorkoutItemPreview from "@/components/WorkoutItemPreview.vue";
    import WorkoutItemDetails from "@/components/WorkoutItemDetails.vue";
    import WorkoutAddExerciseModal from "@/components/WorkoutAddExerciseModal.vue";
    import ActionButton from "@/components/BaseButton.vue";
    import { ref, computed } from 'vue';
    import { useRoute } from 'vue-router';

    const items = [
        {
          "id": 1,
          "name": "Bench Press",
          "description": "Lie flat on your back on a bench. Grab the barbell with a grip that is slightly wider than shoulder-width apart. Lower the bar down to your chest, then push it back up until your arms are fully extended.",
          "muscle_targeted": "Chest",
          "thumbnail_url": "https://example.com/bench_press_thumbnail.jpg",
          "attachment_url": "https://example.com/bench_press_attachment.jpg",
          "sets": 3,
          "reps": 10,
          "user_id": 1,
          "max_weight": 50,
          "max_weight_date": "01.03.2023",
          "is_public": true,
          "is_approved": true
        },
        {
          "id": 2,
          "name": "Squat",
          "description": "Stand with your feet shoulder-width apart. Keep your back straight and your chest up. Bend your knees and lower your body until your thighs are parallel to the ground. Push back up to the starting position.",
          "muscle_targeted": "Legs",
          "thumbnail_url": "https://example.com/squat_thumbnail.jpg",
          "attachment_url": "https://example.com/squat_attachment.jpg",
          "sets": 3,
          "reps": 8,
          "user_id": 1,
          "max_weight": 80,
          "max_weight_date": "10.04.2023",
          "is_public": true,
          "is_approved": true
        },
        {
          "id": 3,
          "name": "Pull-up",
          "description": "Hang from a pull-up bar with your palms facing away from you. Pull your body up until your chin is above the bar, then lower yourself back down to the starting position.",
          "muscle_targeted": "Legs",
          "thumbnail_url": "https://example.com/pull-up_thumbnail.jpg",
          "attachment_url": "https://example.com/pull-up_attachment.jpg",
          "sets": 3,
          "reps": 12,
          "user_id": 1,
          "max_weight": 30,
          "max_weight_date": "04.03.2023",
          "is_public": true,
          "is_approved": true
        }
    ]

    const groupedItems = computed(() => {
        if (!items) return {};
        return items.reduce((groups, item) => {
            const group = groups.find(group => group.muscle_targeted === item.muscle_targeted)
            if (group) {
                group.items.push(item)
            }
            else {
                groups.push({
                    muscle_targeted: item.muscle_targeted,
                    items: [item]
                })
            }
            return groups
        }, [])
    })

    const currentDay = computed(() => useRoute().meta.breadcrumb.split("/").pop())

    const musclesTargeted = computed(() => groupedItems.value.filter((group) => group.muscle_targeted !== "Warmup").map((group) => group.muscle_targeted).join(", "))

    let selectedItem = ref(null);
    let isDetailsVisible = ref(false);

    function showDetails(item) {
        selectedItem.value = item;
        isDetailsVisible.value = true;
    }

    let isAddModalVisisble = ref(false);

    function showAddModal() {
        isAddModalVisisble.value = true;
    }

</script>

<style lang="scss" scoped>

.container {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    gap: 10px;
    margin: 10px 0px 20px 0px;
}

.banner {
    background: var(--color-overlay);
    border-radius: 5px;
    padding: 10px 15px;

    &__title {
        font-size: 1.6rem;
        font-weight: bold;
    }

    &__subtitle {
        color: var(--color-text-muted-2);
        font-size: 1.2rem;
    }
}

.muscle-group {
    &__heading {
        display: inline-flex;
        margin-bottom: 10px;

        &:after {
            background: var(--color-primary);
            bottom: 0px;
            content: '';
            height: 1px;
            left: 0;
            position: absolute;
            width: 150%;
        }
    }
}

.exercise-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
</style>