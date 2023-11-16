<template>
	<v-container fluid>
		
		<v-data-table
			:headers="headers"
			:items="clients"
			item-key="id"
			:server-items-length="pager.total"
			:options.sync="filterOptions"
			must-sort
			:loading="loading"
			fixed-header
			:footer-props="{'items-per-page-options': [10, 15, 25]}"
			@update:options="getClients(force)"
		>
			<template #top>
				<h1 class="mb-3">{{ $tc('titles.clients') }}</h1>

				<v-row class="table-filters">
					<v-col cols="auto">
						
						<v-text-field
							v-model="filter.name"
							dense outlined filled
							prepend-inner-icon="search"
							:label="$tc('name')"
							hide-details
							clearable
							size="20"
							@change.native="getClients()"
							@click:clear="getClients()"
						></v-text-field>
						
					</v-col>
					<v-col cols="auto">
						
						<v-text-field
							v-model="filter.phone"
							dense outlined filled
							prepend-inner-icon="phone"
							:label="$tc('phone')"
							hide-details
							clearable
							size="16"
							@change.native="getClients()"
							@click:clear="getClients()"
						></v-text-field>
						
					</v-col>
					<v-col cols="auto">
						
						<v-select
							v-model="filter.platform"
							dense outlined filled
							:label="$tc('platform')"
							:items="clientPlatforms"
							prepend-inner-icon="devices_other"
							hide-details
							size="6"
							clearable
							class="v-select--size"
							@input="getClients()"
						></v-select>
						
					</v-col>
					<v-spacer style="flex-basis: 100%"></v-spacer>
					<v-col cols="auto">
						
						<div class="v-label theme--light">
							<v-icon class="pb-1">date_range</v-icon> {{ $tc('registration') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtRegBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									hide-details
									clearable
									@input="getClients()"
									@click:clear="getClients()"
								/>
								
							</v-col>
							<v-col cols="auto">

								<DateInput
									v-model="filter.dtRegEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									hide-details
									clearable
									@input="getClients()"
									@click:clear="getClients()"
								/>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">
						
						<div class="v-label theme--light mb">
							<v-icon class="pb-1">date_range</v-icon> {{ $tc('lastLogin') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtLastLoginBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									hide-details
									clearable
									@input="getClients()"
									@click:clear="getClients()"
								/>
								
							</v-col>
							<v-col cols="auto">

								<DateInput
									v-model="filter.dtLastLoginEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									hide-details
									clearable
									@input="getClients()"
									@click:clear="getClients()"
								/>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto" class="table-filters__actions">

						<v-btn
							color="secondary"
							@click="
								$store.commit('general/RESET_CLIENTS_FILTER')
								getClients()
							"
						>
							<v-icon left>
								clear
							</v-icon>
							{{ $tc('clear') }}
						</v-btn>

						<v-btn
							color="primary"
							@click="getClients()"
						>
							<v-icon left>
								refresh
							</v-icon>
							{{ $tc('refresh') }}
						</v-btn>

					</v-col>
				</v-row>

			</template>

			<template #item.id="{ item }">

				<router-link :to="{name: 'ClientAccount', params: {id: item.id}}">
					{{ item.id }}
				</router-link>

			</template>
			<template #item.registrationDate="{ item }">

				<span>{{ new Date(item.registrationDate).toLocaleDateString() }}</span>

			</template>
		</v-data-table>
		
	</v-container>
</template>

<script>
import DateInput from '@bo/components/DateInput'
import {filterOptions, clientPlatforms} from '@bo/mixins.js'

export default {
	components: {
		DateInput,
	},

	mixins: [filterOptions, clientPlatforms],

	data() { return {
		loading: false,
		force: false,

		headers: [
			{
				text: this.$tc('id'),
				value: 'id',
				sortable: false,
			},
			{
				text: this.$tc('name'),
				value: 'name',
			},
			{
				text: this.$tc('phone'),
				value: 'phone',
				sortable: false,
			},
			{
				text: this.$tc('registration'),
				value: 'createdAt',
				align: 'start',
			},
			{
				text: this.$tc('platform'),
				value: 'lastPlatform',
				sortable: false,
			},
			{
				text: this.$tc('lastLogin'),
				value: 'lastLoginAt',
				align: 'end',
			},
		],
		
	}},
	
	methods: {
		getClients(force = true) {
			setTimeout(() => {
				this.$store.dispatch('general/getClients', {
					force: this.force,
					params: this.filter,
					start: () => this.loading = true,
					finish: () => this.loading = false,
				})
				this.force = true
			})
		},
		
	},
	
	computed: {
		pager() {
			return this.$store.state.general.clientsPager
		},

		filter() {
			return this.$store.state.general.clientsFilter
		},

		clients() {
			return this.$store.state.general.clients
		},
		
	},
	
}
</script>
