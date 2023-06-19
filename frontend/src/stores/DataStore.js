import { defineStore } from 'pinia';

import ApiService from '@/services/api.service';
import { setLocalStorage, getLocalStorage, deleteLocalStorage } from '@/services/storage.service';

import { useAuthStore as authStore } from '@/stores/AuthStore';

export const useDataStore = defineStore('dataStore', {
    state: () => ({
        workouts: [],
        exercises: [],
        loading: false
    }),
    getters: {
        getWorkouts: (state) => state.workouts,
        getExercises: (state) => state.exercises,
        getGroupedExercises: (state) => {
            const groups = {};
            state.exercises.forEach((exercise) => {
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
        },
        getUserExercises: (state) => state.exercises.filter(exercise => exercise.userId === authStore().user.id),
        isLoading: (state) => state.loading,
        availableDaysOfWeek: (state) => {
            const workouts = state.workouts;
            const daysOfWeek = [
              { id: 1, text: 'Monday' },
              { id: 2, text: 'Tuesday' },
              { id: 3, text: 'Wednesday' },
              { id: 4, text: 'Thursday' },
              { id: 5, text: 'Friday' },
              { id: 6, text: 'Saturday' },
              { id: 7, text: 'Sunday' },
            ];

            const takenDays = workouts.map((workout) => workout.dayOfWeek);
            const availableDays = daysOfWeek.filter(
                (day) => !takenDays.includes(day.text)
            );

            return availableDays;
        },
        availableMuscleGroups: () => {
            const muscleGroups = [
                'Traps', 'Shoulders', 'Chest', 'Biceps', 'Forearms',
                'Obliques', 'Abdominals', 'Quads', 'Calves', 'Lats',
                'Triceps', 'Lower Back', 'Glutes', 'Hamstrings',
                'Warmup', 'Full Body', 'Legs'
            ];

            return muscleGroups.sort();
        }
    },
    actions: {
        async fetchData() {
            try {
                this.loading = true;

                const [workoutsResponse, exercisesResponse] = await Promise.all([
                    ApiService.get('/workout'),
                    ApiService.get('/exercise'),
                ]);

                this.workouts = workoutsResponse.data;
                this.exercises = exercisesResponse.data;

            } catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async createWorkout(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/workout', { dayOfWeek: payload});
                this.workouts.push(response.data);
                this.sortWorkoutsAlphabetically();
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async addWorkoutExercise(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post(`/workout/${payload.workoutId}/exercise`, payload);
                const workout = this.workouts.find(w => w.id === payload.workoutId);
                if (workout) {
                    workout.exercises.push(response.data);
                }

            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateWorkoutExercise(workoutId, exerciseId, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/workout/${workoutId}/exercise/${exerciseId}`, payload);
                const workout = this.workouts.find(w => w.id === workoutId);
                if (workout) {
                    const exercise = workout.exercises.find(e => e.id === exerciseId);
                    Object.assign(exercise, response.data);
                }

            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async deleteWorkoutExercise(workoutId, exerciseId) {
            try {
                this.loading = true;
                await ApiService.delete(`/workout/${workoutId}/exercise/${exerciseId}`);
                const workout = this.workouts.find(w => w.id === workoutId);
                if (workout) {
                    const index = workout.exercises.findIndex((e) => e.id === exerciseId);
                    workout.exercises.splice(index, 1);
                }
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async createExercise(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/exercise', payload);
                this.exercises.push(response.data);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateExercise(exerciseId, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/exercise/${exerciseId}`, payload);
                this.workouts.forEach(workout => {
                    const workoutExercise = workout.exercises.find(e => e.id === exerciseId)
                    if (workoutExercise) Object.assign(workoutExercise, response.data);
                })
                const exercise = this.exercises.find(e => e.id === exerciseId)
                if (exercise) Object.assign(exercise, response.data);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async fetchExerciseHistory(exerciseId) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/history/${exerciseId}`);
                return Promise.resolve(response.data);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async addExerciseHistory(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/history', payload);
                return Promise.resolve(response.data);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateExerciseHistory(payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/history/${payload.exerciseId}`, payload);
                return Promise.resolve(response.data);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async deleteExerciseHistory(id) {
            try {
                this.loading = true;
                const response = await ApiService.delete(`/history/${id}`);
                return Promise.resolve(response.data);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        sortWorkoutsAlphabetically() {
            const daysOfWeek = [
                { id: 1, text: 'Monday' },
                { id: 2, text: 'Tuesday' },
                { id: 3, text: 'Wednesday' },
                { id: 4, text: 'Thursday' },
                { id: 5, text: 'Friday' },
                { id: 6, text: 'Saturday' },
                { id: 7, text: 'Sunday' },
            ];

            const sortedWorkouts = this.workouts.sort((a, b) => {
                const dayA = daysOfWeek.find(day => day.text === a.dayOfWeek);
                const dayB = daysOfWeek.find(day => day.text === b.dayOfWeek);

                if (dayA && dayB) {
                    return dayA.id - dayB.id;
                }

                // Jeśli nie znaleziono odpowiadających dni tygodnia, zachowaj oryginalną kolejność
                return 0;
            });

            this.workouts = sortedWorkouts;
        },
    }
});