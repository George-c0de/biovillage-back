<template>
	<v-container fluid>
		
		<v-data-table
			:headers="headers"
			:items="orders"
			item-key="id"
			:server-items-length="pager.total"
			:options.sync="filterOptions"
			must-sort
			:loading="loading"
			fixed-header
			:footer-props="{'items-per-page-options': [10, 15, 25]}"
			@update:options="getOrders(force)"
		>
			<template #top>

				<h1 class="mb-3">{{ $tc('titles.orders') }}</h1>
				
				<v-row class="table-filters">
					<v-col cols="auto">
						
						<div class="v-label theme--light">
							<v-icon class="pb-1">date_range</v-icon> {{ $tc('placed') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtCreatedBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
							<v-col cols="auto">

								<DateInput
									v-model="filter.dtCreatedEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">
						
						<div class="v-label theme--light mb">
							<v-icon class="pb-1">date_range</v-icon> {{ $tc('packed') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtPackedBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
							<v-col cols="auto">

								<DateInput
									v-model="filter.dtPackedEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">

						<div class="v-label theme--light">
							<v-icon class="pb-1">date_range</v-icon> {{ $tc('completed') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtFinishedBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
							<v-col cols="auto">

								<DateInput
									v-model="filter.dtFinishedEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">

						<div class="v-label theme--light">
							<v-icon class="pb-1">date_range</v-icon> {{ $tc('delivery') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<DateInput
									v-model="filter.dtDeliveryBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
							<v-col cols="auto">

								<DateInput
									v-model="filter.dtDeliveryEnd"
									dense outlined filled
									:label="$tc('dateEnds')"
									clearable
									hide-details
									@input="getOrders()"
									@clear="getOrders()"
								/>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">

						<div class="v-label theme--light">
							<v-icon class="pb-1">access_time</v-icon> {{ $tc('deliveryHours', 2)}}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<v-text-field
									v-model="filter.deliveryHourBegin"
									dense outlined filled
									:label="$tc('dateStarts')"
									clearable
									hide-details
									size="4"
									@change.native="getOrders()"
									@click:clear="getOrders()"
								></v-text-field>
								
							</v-col>
							<v-col>

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
							<v-icon class="pb-1">search</v-icon> {{ $tc('order') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<v-text-field
									v-model="filter.id"
									dense outlined filled
									label="id"
									hide-details
									clearable
									size="10"
									@change.native="getOrders()"
									@click:clear="getOrders()"
								></v-text-field>
								
							</v-col>
							<v-col cols="auto">

								<v-select
									v-model="filter.status"
									:label="$tc('status')"
									:items="orderStatuses"
									dense outlined filled
									hide-details
									size="10"
									clearable
									class="v-select--size"
									@input="getOrders()"
								></v-select>
								
							</v-col>
						</v-row>
						
					</v-col>
					<v-col cols="auto">

						<div class="v-label theme--light">
							<v-icon class="pb-1">perm_identity</v-icon> {{ $tc('client') }}
						</div>
						<v-row align="end" class="mt-n1">
							<v-col cols="auto" class="mr-n3">

								<v-text-field
									v-model="filter.clientId"
									dense outlined filled
									label="id"
									hide-details
									clearable
									size="10"
									@change.native="getOrders()"
									@click:clear="getOrders()"
								></v-text-field>
								
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
					<v-col cols="auto" class="table-filters__actions">

						<v-btn
							color="secondary"
							@click="
								$store.commit('orders/RESET_ORDERS_FILTER')
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
				
				<router-link :to="{name: 'OrderDetails', params: {id: item.id}}">{{ item.id }}</router-link>
				
			</template>
			<template #item.clientName="{ item }">
				
				{{ item.clientName }}
				<br v-if="item.clientName">
				<router-link :to="{name: 'ClientAccount', params: {id: item.clientId}}">{{ item.clientPhone }}</router-link>
				
			</template>
			<template #item.registrationDate="{ item }">

				{{ new Date(item.registrationDate).toLocaleDateString() }}

			</template>
			<template #item.deliveryDate="{ item }">

				{{ item.deliveryDate }} ({{ timeToString(item.diStartHour) }}:{{ timeToString(item.diStartMinute) }} — {{ timeToString(item.diEndHour) }}:{{ timeToString(item.diEndMinute) }})

			</template>
			<template #item.status="{ item }">

				{{ orderStatuses.find(s => s.value === item.status).text }}

			</template>
			<template #item.total="{ item }">

				{{ numberByThousands(item.total) }}

			</template>
			<template v-if="totalProductsSum" #body.append="{ isMobile }">

				<tr :class="{'d-block': isMobile}">
					<template v-for="n in headers">
						<td
							:key="n.value"
							v-if="n.value === 'total'"
							align="right"
							:class="{'d-flex align-center justify-end': isMobile}"
						>
							<div>
								<span class="font-weight-black">{{ $tc('total') }}: </span> {{ totalProductsSum }}
							</div>
						</td>
						<td :key="n.value" v-else :class="{'d-none': isMobile}"></td>
					</template>
				</tr>

      	</template>
		</v-data-table>
		
	</v-container>
</template>

<script>
import DateInput from '@bo/components/DateInput'
import {filterOptions, orderStatuses, numberByThousands, totalSum, timeFormats, autoRefresh, } from '@bo/mixins.js'

export default {
	components: {
		DateInput,
	},
	
	mixins: [filterOptions, orderStatuses, numberByThousands, totalSum, timeFormats, autoRefresh, ],

	data() { return {
		orders: [],
		loading: false,
		force: false,

		headers: [
			{
				text: this.$tc('id'),
				value: 'id',
				sortable: false,
			},
			{
				text: this.$tc('client'),
				value: 'clientName',
			},
			{
				text: this.$tc('created'),
				value: 'createdAt',
			},
			{
				text: this.$tc('packed'),
				value: 'packedAt'
			},
			{
				text: this.$tc('delivery'),
				value: 'deliveryDate',
			},
			{
				text: this.$tc('completed'),
				value: 'finishedAt',
			},
			{
				text: this.$tc('status'),
				value: 'status',
			},
			{
				text: this.$tc('sum') + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'total',
				sortable: false,
				align: 'end',
			},
		],

	}},
	
	methods: {
		getOrders(force = true) {
			setTimeout(() => {
				this.$store.dispatch('orders/getOrders', {
					force,
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
			return this.$store.state.orders.ordersPager
		},

		filter() {
			return this.$store.state.orders.ordersFilter
		},

		totalProductsSum() {
			return this.totalSumText(this.orders, 'total')
		},
		
	},

	watch: {
		'$store.state.orders.orders': {
			immediate: true,
			handler(v) {
				this.orders = JSON.parse(JSON.stringify(v))
			}
		},

	},

	created() {
		this.autoRefresh(this.getOrders)

	},
	
}
</script>
