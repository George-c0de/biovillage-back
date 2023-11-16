<template>
	<v-container fluid class="justify-center">
		<v-row align="center" justify="center">
			<v-col cols="12" sm="8" md="4">
				<v-card class="elevation-12">
					<v-form @submit.prevent="login">
						<v-toolbar color="primary" dark flat>
							<v-toolbar-title>BioVillage | {{ $tc('authorization') }}</v-toolbar-title>
						</v-toolbar>
						<v-card-text>
							<v-text-field
								v-model="phone"
								:label="$tc('phone')"
								prepend-icon="phone"
								type="text"
							/>
							<v-text-field
								v-model="password"
								:label="$tc('password')"
								prepend-icon="lock"
								type="password"
							/>
						</v-card-text>
						<v-card-actions>
							<v-spacer />
							<v-btn large block type="submit" color="primary" class min-width="140">{{ $tc('logIn') }}</v-btn>
						</v-card-actions>
					</v-form>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
export default {
	data() { return {
		phone: '',
		password: '',

	}},

	methods: {
		login() {
			this.$store.dispatch('auth/login', {
				phone: this.phone,
				password: this.password,
			})
		}
	},

}
</script>
