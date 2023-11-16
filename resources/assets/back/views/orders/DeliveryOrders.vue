<template>
	<v-container fluid>
		<h1 class="mb-3">{{ $tc('titles.deliveryOrders') }}</h1>

		<v-row class="table-filters align-end">
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
			<v-col cols="auto">

				<v-tabs
					v-model="displayType"
				>
					<v-tabs-slider color="primary"></v-tabs-slider>

					<v-tab>{{ $tc('table') }}</v-tab>
					<v-tab>{{ $tc('map') }}</v-tab>
				</v-tabs>

			</v-col>
			<v-col cols="auto" class="ml-auto">

				<v-btn
					color="secondary"
					@click="
						$store.commit('orders/RESET_DELIVERY_ORDERS_FILTER')
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
		
		<div class="d-none">
			<v-card
				ref="mapPopup"
				:to="{name: 'DeliveryOrderDetails', params: {id: selectedOrder.id}}"
			>

				<v-card-title>
					{{ $tc('order') + ' №' + selectedOrder.id }}
				</v-card-title>
				
				<v-card-subtitle>{{ $tc('orderDeliveryDateAt') + ' ' + selectedOrder.deliveryDate }}</v-card-subtitle>

				<v-card-text>
					<div class="mb-1">
						<span class="font-weight-black">{{ $tc('address') }}:</span>
						<span>
							<span>{{ selectedOrder.addressStreet }},</span>
							<span v-if="selectedOrder.addressHouse">{{ $tc('house') + ' ' + selectedOrder.addressHouse }},</span>
							<span v-if="selectedOrder.addressBuilding">{{ $tc('building') + ' ' + selectedOrder.addressBuilding }},</span>
							<span v-if="selectedOrder.addressEntrance">{{ $tc('entrance') + ' ' + selectedOrder.addressEntrance }},</span>
							<span v-if="selectedOrder.addressFloor">{{ $tc('floor') + ' ' + selectedOrder.addressFloor }},</span>
							<span v-if="selectedOrder.addressDoorphone">{{ $tc('doorPhone') + ' ' + selectedOrder.addressDoorphone }},</span>
							<span v-if="selectedOrder.addressAppt">{{ $tc('apartment') + ' ' + selectedOrder.addressAppt }}</span>
						</span>
					</div>

					<div class="mb-1">
						<span class="font-weight-black">{{ $tc('client') }}:</span>
						{{ selectedOrder.clientName }} <a :href="'tel:' + String(selectedOrder.clientPhone).replace(/\D/g, '')">{{ selectedOrder.clientPhone }}</a>
					</div>

					<div class="mb-1">
						<span class="font-weight-black"> {{ $tc('clientComment') }}:</span>
						{{ selectedOrder.clientsComment }}
					</div>
				</v-card-text>

			</v-card>
		</div>

		<v-tabs-items v-model="displayType">
			<v-tab-item>

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
					<template #item.id="{ item }">
						
						<router-link :to="{name: 'DeliveryOrderDetails', params: {id: item.id}}">{{ item.id }}</router-link>
						
					</template>
					<template #item.clientName="{ item }">
				
						{{ item.clientName }}
						<br v-if="item.clientName">
						{{ item.clientPhone }}
						
					</template>
					<template #item.address="{ item }">
						
						<div>
							<span>{{ item.addressStreet }},</span>
							<span v-if="item.addressHouse">{{ $tc('house') + ' ' + item.addressHouse }},</span>
							<span v-if="item.addressBuilding">{{ $tc('building') + ' ' + item.addressBuilding }},</span>
							<span v-if="item.addressEntrance">{{ $tc('entrance') + ' ' + item.addressEntrance }},</span>
							<span v-if="item.addressFloor">{{ $tc('floor') + ' ' + item.addressFloor }},</span>
							<span v-if="item.addressDoorphone">{{ $tc('doorPhone') + ' ' + item.addressDoorphone }},</span>
							<span v-if="item.addressAppt">{{ $tc('apartment') + ' ' + item.addressAppt }}</span>
						</div>

						<div v-if="item.addressComment" class="font-weight-light">
							{{ item.addressComment }}
						</div>
						
					</template>
				</v-data-table>

			</v-tab-item>
			<v-tab-item>

				<GoogleMap
					:settings="mapSettings"
					:options="mapOptions"
					:markers="markers"
					:popupContent="mapPopup"
					:markerClick="markerClick"
					class="flex-grow-1"
					:style="{'height': '400px'}"
				></GoogleMap>
		
			</v-tab-item>
		</v-tabs-items>
		
	</v-container>
</template>

<script>
import DateInput from '@bo/components/DateInput'
import GoogleMap from '@bo/components/GoogleMap'
import {orderStatuses, deliveryIntervals, autoRefresh, } from '@bo/mixins.js'

export default {
	components: {
		DateInput,
		GoogleMap,
	},

	mixins: [orderStatuses, deliveryIntervals, autoRefresh, ],

	data() { return {
		loading: false,
		mapPopup: null,
		markers: [],
		orders: [],
		selectedOrder: {},
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
				text: this.$tc('deliveryInterval'),
				value: 'interval',
				align: 'end',
			},
			{
				text: this.$tc('address'),
				value: 'address',
			},
		],
		
	}},
	
	methods: {
		getOrders(force = true) {
			setTimeout(() => {
				this.$store.dispatch('orders/getDeliveryOrders', {
					force,
					params: this.filter,
					start: ()=> this.loading = true,
					finish: ()=> this.loading = false,
				})
			})
		},
		
		markerClick(id, popup, map, marker) {
			// в новой вкладке
			// window.open(
			// 	this.$router.resolve({ name: 'DeliveryOrderDetails', params: { id, }, }).href,
			// '_blank')

			// this.$router.push({ name: 'DeliveryOrderDetails', params: { id, }, })

			this.selectedOrder = this.orders.find(e => e.id === id)
			popup.open(map, marker)
		},
		
	},
	
	computed: {
		filter() {
			return this.$store.state.orders.deliveryOrdersFilter
		},

		displayType: {
			get() {
				return this.$store.state.orders.deliveryOrdersDisplayType
			},
			set(v) {
				this.$store.state.orders.deliveryOrdersDisplayType = v
			}
		},

		mapSettings() {
			let settings = this.$store.state.general.settings

			return {
				apiKey: settings.mapsToken,
				language: settings.lang,
			}
		},

		mapOptions() {
			let settings = this.$store.state.general.settings

			return {
				zoom: parseFloat(settings.mapsZoom),
				center: {
					lat: parseFloat(settings.mapsCenterLat),
					lng: parseFloat(settings.mapsCenterLon),
				},
			}
		},
		
	},

	watch: {
		'$store.state.orders.deliveryOrders': {
			immediate: true,
			handler(v) {
				let orders = JSON.parse(JSON.stringify(v))
				this.markers = []
				
				orders.forEach(item => {
					item.interval = this.deliveryIntervalPropertiesToString(item)

					this.markers.push({
						id: item.id,
						lat: parseFloat(item.addressLat),
						lng: parseFloat(item.addressLon),
					})
				})

				this.orders = orders
			},
		},

	},
	
	created() {
		this.$store.dispatch('general/getSettings')
		this.getOrders(false)
		this.autoRefresh(this.getOrders)

	},

	mounted() {
		this.mapPopup = this.$refs.mapPopup.$el

	},
	
}
</script>
