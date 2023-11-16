<template>
	<v-container fluid>

		<h1>{{ $tc('titles.purchaseOrders') }}</h1>

		<v-row class="table-filters mt-4 align-end">
			<v-col cols="auto">
						
				<div class="v-label theme--light">
					<v-icon class="pb-1">delivery_dining</v-icon> {{ $tc('delivery') }}
				</div>
				<v-row align="end" class="mt-n1">
					<v-col cols="auto" class="mr-n3">

						<DateInput
							v-model="filter.date"
							dense outlined filled
							:label="$tc('date')"
							hide-details
							@input="getOrders()"
						/>
						
					</v-col>
					<v-col cols="auto" class="mr-n3">

						<v-text-field
							v-model="filter.startHour"
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
							v-model="filter.endHour"
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
					<v-col cols="auto">
						<v-select
							v-model="shortageProductsOnly"
							:items="productsTypes"
							dense outlined filled
							:label="$tc('getPurchaseOrdersSelect.label')"
							style="max-width: 260px"
							hide-details
							@change="getOrders()"
						></v-select>
					</v-col>
				</v-row>
				
			</v-col>
			<v-col cols="auto" class="ml-auto">

				<v-btn
					color="secondary"
					@click="
						$store.commit('orders/RESET_PURCHASE_ORDERS_FILTER')
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
		
		<h2>{{ $tc('product', 2) }}</h2>
		<v-data-table
			:headers="productsHeaders"
			:items="products"
			item-key="id"
			disable-pagination
			must-sort
			sort-by="id"
			:loading="loading"
			hide-default-footer
			fixed-header
		>
			<template #item.productName="{ item }">

				<router-link :to="{name: 'CatalogProductEdit', params: {id: item.id}}">
					{{ item.productName }}
				</router-link>
				({{ item.totalQty }} {{ $tc('pc') }})

			</template>

			<template #item.price="{ item }">
				{{ numberByThousands(item.netCostPerStep / 100) }}
				{{ $store.state.general.settings.paymentCurrencySign || '' }}
			</template>

			<template #item.totalQty="{ item }">
				{{ autoFormatUnits({ item, qField: 'totalUnits' }) }}
			</template>

			<template #item.total="{ item }">
				{{ numberByThousands(item.total / 100) }} 
				{{ $store.state.general.settings.paymentCurrencySign || '' }}
			</template>

			<template v-if="totalProducutsSum" #body.append="{ isMobile }">

				<tr :class="{'d-block': isMobile}">
					<template v-for="n in productsHeaders">
						<td
							:key="n.value"
							v-if="n.value === 'total'"
							align="right"
							:class="{'d-flex align-center justify-end': isMobile}"
						>
							<div>
								<span class="font-weight-black">{{ $tc('total') }}: </span> {{ totalProducutsSum }} {{ $store.state.general.settings.paymentCurrencySign || '' }}
							</div>
						</td>
						<td :key="n.value" v-else :class="{'d-none': isMobile}"></td>
					</template>
				</tr>

      	</template>
		</v-data-table>

		<h2>{{ $tc('gift', 2) }}</h2>
		<v-data-table
			:headers="giftsHeaders"
			:items="gifts"
			item-key="id"
			disable-pagination
			must-sort
			sort-by="id"
			:loading="loading"
			hide-default-footer
			fixed-header
		>
			<template #item.total="{ item }">
				{{ shortageProductsOnly ? item.total : item.totalQty }}
			</template>
			
			<template #item.totalBonuses="{ item }">
				{{ numberByThousands(item.totalBonuses) }}
			</template>

			<template v-if="totalGiftsSum" #body.append="{ isMobile }">

				<tr :class="{'d-block': isMobile}">
					<template v-for="n in giftsHeaders">
						<td
							:key="n.value"
							v-if="n.value === 'totalBonuses'"
							align="right"
							:class="{'d-flex align-center justify-end': isMobile}"
						>
							<div>
								<span class="font-weight-black">{{ $tc('total') }}: </span> {{ totalGiftsSum }}
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
import {orderStatuses, numberByThousands, totalSum, autoRefresh, formatUnits} from '@bo/mixins.js'

export default {
	components: {
		DateInput,
	},

	mixins: [orderStatuses, numberByThousands, totalSum, autoRefresh, formatUnits],

	data() { return {
		loading: false,
		
		productsHeaders: [
			{
				text: this.$tc('section'),
				value: 'groupName',
			},
			{
				text: this.$tc('product'),
				value: 'productName',
			},
			{
				text: this.$tc('price'),
				value: 'price',
				align: 'end',
			},
			{
				text: this.$tc('quantity'),
				value: 'totalQty',
				align: 'end',
			},
			{
				text: this.$tc('sum'),
				value: 'total',
				align: 'end',
			},
		],
		giftsHeaders: [
			{
				text: this.$tc('gift'),
				value: 'giftName',
			},
			{
				text: this.$tc('price') + ', ' + this.$tc('bonus', 2),
				value: 'bonuses',
				align: 'end',
			},
			{
				text: this.$tc('quantity'),
				value: 'total',
				align: 'end',
			},
			{
				text: this.$tc('sum') + ', ' + this.$tc('bonus', 2),
				value: 'totalBonuses',
				align: 'end',
			},
		],

		productsTypes: [
			{
				text: this.$tc('getPurchaseOrdersSelect.true'),
				value: true
			},
			{
				text: this.$tc('getPurchaseOrdersSelect.false'),
				value: false
			},
		],

		shortageProductsOnly: true,
		
	}},
	
	methods: {
		getOrders(force = true) {
			let methodName = 'orders/getPurchaseOrders'
			if (this.shortageProductsOnly) methodName = 'orders/getPurchaseShortageProducts'
			setTimeout(() => {
				this.$store.dispatch(methodName, {
					force,
					params: this.filter,
					start: () => this.loading = true,
					finish: () => this.loading = false
				})
			})
		}
	},
	
	computed: {
		filter() {
			return this.$store.state.orders.purchaseOrdersFilter
		},

		products() {
			return this.$store.state.orders.purchaseOrders.products
		},

		gifts() {
			return this.$store.state.orders.purchaseOrders.gifts
		},

		totalProducutsSum() {
			return this.totalSumText(this.products, 'total', 0.01)
		},

		totalGiftsSum() {
			return this.totalSumText(this.gifts, 'totalBonuses')
		},
		
	},
	
	created() {
		this.getOrders(false)
		this.autoRefresh(this.getOrders)

	},
	
}
</script>
