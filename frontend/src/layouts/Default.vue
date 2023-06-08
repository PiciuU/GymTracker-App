<template>
	<main>
		<div class="container">
			<router-view v-slot="{ Component }">
				<keep-alive>
					<component :is="Component"></component>
				</keep-alive>
			</router-view>
		</div>
	</main>
</template>

<style lang="scss">
	@import '@/assets/styles/authForm.scss';
</style>

<style scoped>
	main {
		display: flex;
		flex: 1;
		padding: 0px 20px;
	}

	.container {
		display: flex;
		max-width: 1280px;
		margin-left: auto;
		margin-right: auto;
		width: 100%;
		flex: 1;
	}
</style>