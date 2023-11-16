<template>
	<v-container fluid>
		
		<v-data-table
			:headers="headers"
			:items="orders"
			item-key="id"
			disable-pagination
			must-sort
			sort-by="id"
			:loading="loading"
			hide-default-footer
			fixed-header
		>
			<template #top>
				<h1 class="mb-3">{{ $tc('titles.packOrders') }}</h1>

				<v-row class="table-filters">
					<v-col cols="auto">
								
						<div class="v-label theme--light">
							<v-icon class="pb-1">delivery_dining</v-icon> {{ $tc('delivery') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtDelivery"
									dense outlined filled
									:label="$tc('date')"
									hide-details
									@input="getOrders()"
								/>
								
							</v-col>
							<v-col cols="auto" class="mr-n3">

								<v-text-field
									v-model="filter.deliveryHourBegin"
									dense outlined filled
									:label="$tc('hour') + ' ' + $tc('dateStarts')"
									clearable
									hide-details
									size="4"
									@change.native="getOrders()"
									@click:clear="getOrders()"
								></v-text-field>
								
							</v-col>
							<v-col cols="auto">

								<v-text-field
									v-model="filter.deliveryHourEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									clearable
									hide-details
									size="4"
									@change.native="getOrders()"
									@click:clear="getOrders()"
								></v-text-field>

							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">
								
						<div class="v-label theme--light">
							<v-icon class="pb-1">info</v-icon> {{ $tc('general') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<v-select
									v-model="filter.status"
									dense outlined filled
									:label="$tc('status')"
									:items="orderStatuses"
									hide-details
									size="10"
									clearable
									class="v-select--size"
									@input="getOrders()"
								></v-select>
								
							</v-col>
							<v-col cols="auto">

								<v-text-field
									v-model="filter.clientPhone"
									dense outlined filled
									:label="$tc('phone')"
									hide-details
									clearable
									size="16"
									@change.native="getOrders()"
									@click:clear="getOrders()"
								></v-text-field>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto" class="ml-auto">

						<v-btn
							color="secondary"
							@click="
								$store.commit('orders/RESET_PACK_ORDERS_FILTER')
								getOrders()
							"
						>
							<v-icon left>
								clear
							</v-icon>
							{{ $tc('clear') }}
						</v-btn>

						<v-btn
							color="primary"
							@click="getOrders()"
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
				
				<router-link :to="{name: 'PackOrderDetails', params: {id: item.id}}">{{ item.id }}</router-link>
				
			</template>
			<template #item.clientName="{ item }">
				
				{{ item.clientName }}
				<br v-if="item.clientName">
				{{ item.clientPhone }}
				
			</template>
		</v-data-table>
		
	</v-container>
</template>

<script>
import DateInput from '@bo/components/DateInput'
import {orderStatuses, deliveryIntervals, autoRefresh, } from '@bo/mixins.js'

export default {
	components: {
		DateInput,
	},

	mixins: [orderStatuses, deliveryIntervals, autoRefresh, ],

	data() { return {
		orders: [],
		loading: false, 

		headers: [
			{
				text: this.$tc('id'),
				value: 'id',
			},
			{
				text: this.$tc('client'),
				value: 'clientName',
			},
			{
				text: this.$tc('deliveryDate'),
				value: 'deliveryDate',
				sortable: false,
			},
			{
				text: this.$tc('deliveryInterval'),
				value: 'interval',
				align: 'end',
			},
			{
				text: '',
				value: 'actions',
				align: 'end',
			},
		],

	}},
	
	methods: {
		getOrders(force = true) {
			setTimeout(() => {
				this.$store.dispatch('orders/getPackOrders', {
					force,
					params: this.filter,
					start: () => this.loading = true,
					finish: () => this.loading = false,
				})	
			})
		},
		
	},
	
	computed: {
		filter() {
			return this.$store.state.orders.packOrdersFilter
		},
		
	},

	watch: {
		'$store.state.orders.packOrders': {
			immediate: true,
			handler(v) {
				let orders = JSON.parse(JSON.stringify(v))

				orders.forEach(item => {
					item.interval = this.deliveryIntervalPropertiesToString(item)
				})

				this.orders = orders
			}
		},

	},
	
	created() {
		this.getOrders(false)
		this.autoRefresh(this.getOrders)
		
	},
	
}
</script>
