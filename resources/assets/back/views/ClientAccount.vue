<template>
	<v-container fluid v-if="client.id || !id">
		<h1 class="mb-3">{{ $tc('client') + ' №' + client.id }}</h1>
				
		<v-card
			outlined
			class="pa-4"
		>
			<v-row>
				<v-col cols="12" sm="4" md="3" lg="2">

					<v-card outlined>
						<v-img
							:src="client.avatar"
							contain
							aspect-ratio="1"
							color="secondary"
							style="background-color: #ddd"
						></v-img>
					</v-card>

				</v-col>
				<v-col>
					<div class="row-table">
						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('phone') }}:
							</v-col>
							<v-col>
								<a :href="'tel:' + String(client.phone).replace(/\D/g, '')">{{ client.phone }}</a>
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('name') }}:
							</v-col>
							<v-col>
								{{ client.name }}
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('mail') }}:
							</v-col>
							<v-col>
								<a :href="'mailto:' + String(client.email).replace(/\D/g, '')">{{ client.email }}</a>
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('mailing') }}:
							</v-col>
							<v-col>
								<v-simple-checkbox
									:value="client.allowMailing ? true : false"
									disabled
									class="d-inline"
								></v-simple-checkbox>
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('platform') }}:
							</v-col>
							<v-col>
								{{ client.lastPlatform }}
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('birthday') }}:
							</v-col>
							<v-col>
								<v-form @submit.prevent="updateInfo($event.target)">
									<v-row align="center" class="mx-n1">
										<v-col cols="auto" class="px-1">

											<DateInput
												v-model="client.birthday"
												dense outlined filled
												name="birthday"
												hide-details
												inputStyle="width: 200px; max-width: 100%"
											/>

										</v-col>
										<v-col cols="auto" class="px-1">

											<v-btn
												icon
												type="submit"
												color="success"
											>
												<v-icon>
													save
												</v-icon>
											</v-btn>
											
										</v-col>
									</v-row>
								</v-form>
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('bonus', 2) }}:
							</v-col>
							<v-col>
								<v-form @submit.prevent="updateBonuses($event.target)">
									<v-row align="center" class="mx-n1">
										<v-col cols="auto" class="px-1">

											<v-text-field
												dense outlined filled
												:value="client.allBonuses"
												name="bonuses"
												hide-details
												style="width: 200px; max-width: 100%"
											></v-text-field>

										</v-col>
										<v-col cols="auto" class="px-1">

											<v-btn
												icon
												type="submit"
												color="success"
											>
												<v-icon>
													save
												</v-icon>
											</v-btn>
											
										</v-col>
									</v-row>
								</v-form>
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('blockedBonus', 2) }}:
							</v-col>
							<v-col>
								{{ client.lockedBonuses }}
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('updated') }}:
							</v-col>
							<v-col>
								{{ client.updatedAt }}
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('registration') }}:
							</v-col>
							<v-col>
								{{ client.createdAt }}
							</v-col>
						</v-row>

						<v-row dense>
							<v-col cols="12" md="5" lg="3" class="font-weight-bold">
								{{ $tc('lastLogin') }}:
							</v-col>
							<v-col>
								{{ client.lastLoginAt }}
							</v-col>
						</v-row>

					</div>
				</v-col>
			</v-row>

			<template v-if="client.invitedBy">
				<v-divider class="my-6"></v-divider>

				<h2 class="mb-6">{{ $tc('blockedBonus', 2) + ' №' + client.invitedBy }}</h2>

				<div class="text-h6 mb-3">
					{{ client.invitedByName }}
				</div>

				<div>
					<a :href="'tel:' + String(client.invitedByPhone).replace(/\D/g, '')">{{ client.invitedByPhone }}</a>
				</div>
			</template>

		</v-card>

	</v-container>
</template>

<script>
import DateInput from '@bo/components/DateInput'

export default {
	props: ['id'],

	components: {
		DateInput,
	},
	
	data() { return {
		client: {},

	}},
	
	methods: {
		updateInfo(form) {
			let data = new FormData(form)
			this.$store.dispatch('general/updateClientInfo', {id: this.id, data})
		},

		updateBonuses(form) {
			let data = new FormData(form)
			this.$store.dispatch('general/updateClientBonuses', {id: this.id, data})
		},

	},
	
	watch: {
		'$store.state.general.clientAccount': {
			immediate: true,
			handler(v) {
				this.client = JSON.parse(JSON.stringify(v))
			},
		},

	},
	
	created() {
		this.$store.dispatch('general/getClientAccount', {id: this.id})
		
	},
	
}
</script>
