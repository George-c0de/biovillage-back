<template>
	<v-container
		v-if="order.info.id"
		fluid
	>
		<h1>
			<router-link
				:to="{name: 'DeliveryOrders'}"
				:title="$tc('toList')"
				class="navigation-icon navigation-icon--back"
			>
				<v-icon>arrow_back_ios</v-icon>
			</router-link>
			
			{{ $tc('titles.deliveryOrderDetails') + ' №' + id + ' ' + $tc('orderDeliveryDateAt') + ' ' + order.info.deliveryDate }}
		</h1>

		<v-row class="mt-0 mb-6">
			<v-col cols="12" md="6" class="d-flex flex-column">

				<v-card outlined class="pa-4">
					<div class="mb-3">

						<span class="font-weight-black">{{ $tc('address') }}:</span>
						<span>
							<span>{{ order.info.addressStreet }},</span>
							<span v-if="order.info.addressHouse">{{ $tc('house') + ' ' + order.info.addressHouse }},</span>
							<span v-if="order.info.addressBuilding">{{ $tc('building') + ' ' + order.info.addressBuilding }},</span>
							<span v-if="order.info.addressEntrance">{{ $tc('entrance') + ' ' + order.info.addressEntrance }},</span>
							<span v-if="order.info.addressFloor">{{ $tc('floor') + ' ' + order.info.addressFloor }},</span>
							<span v-if="order.info.addressDoorphone">{{ $tc('doorPhone') + ' ' + order.info.addressDoorphone }},</span>
							<span v-if="order.info.addressAppt">{{ $tc('apartment') + ' ' + order.info.addressAppt }}</span>
						</span>

					</div>
					<div class="mb-3">

						<span class="font-weight-black">{{ $tc('client') }}:</span>
						{{ order.info.clientName }} <a :href="'tel:' + String(order.info.clientPhone).replace(/\D/g, '')">{{ order.info.clientPhone }}</a>

					</div>
					<div class="mb-3">

						<span class="font-weight-black">{{ $tc('actionIfNotDelivery') }}:</span>
						{{ $tc(order.info.actionIfNotDelivery) }}

					</div>
					<div>

						<span class="font-weight-black">{{ $tc('clientComment') }}:</span>
						{{ order.info.clientsComment }}
						
					</div>
				</v-card>

			</v-col>
			<v-col cols="12" md="6">

				<v-form
					:disabled="btnDisabled.update"
					@submit.prevent="updateOrderInfo($event.target)"
				>
					<v-autocomplete
						dense outlined filled
						v-model="order.info.deliveryIntervalId"
						:label="$tc('deliveryInterval')"
						:items="deliveryIntervals"
						:hint="$tc('deliveryUpdateHint')"
						persistent-hint
						prepend-inner-icon="today"
						size="10"
						class="mb-3"
					></v-autocomplete>

					<input type="hidden" name="deliveryIntervalId" :value="order.info.deliveryIntervalId">

					<v-textarea
						outlined filled
						v-model="order.info.adminsComment"
						name="adminsComment"
						:label="$tc('adminComment')"
						prepend-inner-icon="comment_bank"
						rows="2"
						hide-details="auto"
						class="mb-3"
					></v-textarea>

					<v-btn
						type="submit"
						color="primary"
						class="align-self-start"
						:disabled="btnDisabled.update"
						:loading="btnLoading.update"
					>
						<v-icon left>
							refresh
						</v-icon>
						{{ $tc('updateOrderInfo') }}
					</v-btn>
				</v-form>

			</v-col>
		</v-row>
				
		<h2>{{ $tc('gift', 2) }}</h2>
		<div class="d-flex flex-column" style="max-height: 40vh; overflow: hidden">
			<v-data-table
				:headers="giftsHeaders"
				:items="order.gifts"
				item-key="id"
				must-sort
				sort-by="id"
				disable-pagination
				hide-default-footer
				fixed-header
				class="mb-9"
				style="overflow-y: auto"
			>
				<template #item.bonuses="{ item }">
					{{ numberByThousands(item.bonuses) }}
				</template>

				<template #item.totalBonuses="{ item }">
					{{ numberByThousands(item.totalBonuses) }}
				</template>
				<template v-if="order.info.bonuses" #body.append="{ isMobile }">

					<tr :class="{'d-block': isMobile}">
						<template v-for="n in giftsHeaders">
							<td
								:key="n.value"
								v-if="n.value === 'totalBonuses'"
								align="right"
								:class="{'d-flex align-center justify-end': isMobile}"
							>
								<div>
									<span class="font-weight-black">{{ $tc('total') }}: </span> {{ numberByThousands(order.info.bonuses) }}
								</div>
							</td>
							<td :key="n.value" v-else :class="{'d-none': isMobile}"></td>
						</template>
					</tr>

				</template>
			</v-data-table>
		</div>
				
		<h2>{{ $tc('product', 2) }}</h2>
		<div class="d-flex flex-column" style="max-height: 80vh; overflow: hidden">

			<v-data-table
				:headers="productsHeaders"
				:items="order.items"
				item-key="id"
				must-sort
				sort-by="id"
				disable-pagination
				hide-default-footer
				fixed-header
				class="mb-9"
				style="overflow-y: auto"
			>
				<template #item.productName="{ item }">
					<router-link :to="{name: 'CatalogProductEdit', params: {id: item.productId}}">
						{{ item.productName }}
					</router-link>
				</template>

				<template #item.qty="{ item }">
					{{ autoFormatUnits({ item, q: item.qty * item.unitStep }) }}
				</template>

				<template #item.price="{ item }">
					{{ numberByThousands(item.price) }}
				</template>

				<template #item.total="{ item }">
					{{ numberByThousands(item.total) }}
				</template>

				<template #item.realUnits="{ item }">
					{{ autoFormatUnits({ item }) }}
				</template>

				<template #item.realPrice="{ item }">
					{{ numberByThousands(item.realPrice) }}
				</template>

				<template #item.realTotal="{ item }">
					{{ numberByThousands(item.realTotal) }}
				</template>

				<template #body.append="{ isMobile }">
					<tr :class="{'d-block': isMobile}">
						<template v-for="n in productsHeaders">
							<td
								:key="n.value"
								v-if="n.value === 'total'"
								align="right"
								:class="{'d-flex align-center justify-end': isMobile}"
							>
								<div>
									<span class="font-weight-black">{{ $tc('total') }}: </span> {{ numberByThousands(order.info.productsSum) }}
								</div>
							</td>
							<td
								:key="n.value"
								v-else-if="n.value === 'realTotal'"
								align="right"
								:class="{'d-flex align-center justify-end': isMobile}"
							>
								<div>
									<span class="font-weight-black">
										{{ $tc('total') }}<template v-if="isMobile"> {{ $tc('realValue') }}</template>:
									</span>
									{{ totalProductsRealSum }}
								</div>
							</td>
							<td :key="n.value" v-else :class="{'d-none': isMobile}"></td>
						</template>
					</tr>

				</template>
			</v-data-table>
		</div>

		<v-row class="mb-6">
			<!-- <v-col cols="12" sm="6" lg="3"></v-col> -->
			<v-col cols="12" md="6" lg="3" order-lg="last">

				<v-text-field
					readonly
					dense outlined
					:value="totalProductsRealSumText"
					:label="$tc('realValue') + ' ' + this.$tc('price').toLowerCase()"
					placeholder=" "
					hide-details="auto"
					size="10"
				></v-text-field>
				
			</v-col>

			<v-col cols="12" md="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="deliverySumText"
					:label="$tc('delivery')"
					placeholder=" "
					hide-details="auto"
					size="10"
				></v-text-field>
				
			</v-col>
			<v-col cols="12" md="6" lg="3" order-sm="first">

				<v-text-field
					readonly
					dense outlined
					:value="totalText"
					:label="$tc('total')"
					placeholder=" "
					hide-details="auto"
					size="10"
				></v-text-field>
				
			</v-col>
		</v-row>

		<v-row class="my-0 align-center">
			<v-col cols="auto">

				<v-form @submit.prevent="completeOrder">
					<v-btn
						type="submit"
						color="success"
						:disabled="btnDisabled.complete"
						:loading="btnLoading.complete"
					>
						<v-icon left>
							check
						</v-icon>
						{{ $tc('delivered') }}
					</v-btn>
				</v-form>

			</v-col>
			<v-col cols="auto" class="mr-auto">

				<v-form @submit.prevent="updateOrderInfo($event.target, true)">
					<input type="hidden" name="status" value="delivery">
					<v-btn
						type="submit"
						color="primary"
						:disabled="btnDisabled.delivery"
						:loading="btnLoading.delivery"
					>
						<v-icon left>
							delivery_dining
						</v-icon>
						{{ $tc('delivery') }}
					</v-btn>
				</v-form>

			</v-col>
			<v-col cols="auto">

				<v-form @submit.prevent="cancelOrder">
					<v-btn
						type="submit"
						color="error"
						:disabled="btnDisabled.cancel"
						:loading="btnLoading.cancel"
					>
						<v-icon left>
							cancel
						</v-icon>
						{{ $tc('cancel') + ' ' + $tc('order').toLowerCase() }}
					</v-btn>
				</v-form>

			</v-col>
		</v-row>

	</v-container>
</template>

<script>
import {orderStatuses, deliveryIntervals, numberByThousands, totalSum, formatUnits} from '@bo/mixins.js'

export default {
	props: ['id'],

	mixins: [orderStatuses, deliveryIntervals, numberByThousands, totalSum, formatUnits],

	data() { return {
		order: {},
		orderPending: false,

		productsHeaders: [
			{
				text: this.$tc('product'),
				value: 'productName',
			},
			{
				text: this.$tc('quantity'),
				value: 'qty',
				align: 'end',
			},
			{
				text: this.$tc('price') + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'price',
				align: 'end',
			},
			{
				text: this.$tc('sum') + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'total',
				align: 'end',
			},
			{
				text: this.$tc('realValue') + ' ' + this.$tc('quantity').toLowerCase(),
				value: 'realUnits',
				align: 'end',
			},
			{
				text: this.$tc('realValue') + ' ' + this.$tc('price').toLowerCase() + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'realPrice',
				align: 'end',
			},
			{
				text: this.$tc('realValue') + ' ' + this.$tc('sum').toLowerCase() + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'realTotal',
				align: 'end',
			},
		],

		giftsHeaders: [
			{
				text: this.$tc('gift'),
				value: 'giftName',
			},
			{
				text: this.$tc('quantity'),
				value: 'qty',
				align: 'end',
			},
			{
				text: this.$tc('price') + ', ' + this.$tc('bonus', 2),
				value: 'bonuses',
				align: 'end',
			},
			{
				text: this.$tc('sum') + ', ' + this.$tc('bonus', 2),
				value: 'totalBonuses',
				align: 'end',
			},
		],

	}},

	methods: {
		updateOrderInfo(form) {
			this.orderPending = true
			this.btnLoading.update = true
			
			this.$store.dispatch('orders/updateOrderInfo', {
				id: Number(this.id),
				data: new FormData(form),
				finish: ()=> {
					this.orderPending = false
					this.btnLoading.update = false
				},
			})
		},

		completeOrder() {
			this.orderPending = true
			this.btnLoading.complete = true

			this.$store.dispatch('orders/completeOrder', {
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> {
					this.orderPending = false
					this.btnLoading.complete = false
				},
			})
		},

		cancelOrder() {
			this.orderPending = true
			this.btnLoading.cancel = true

			this.$store.dispatch('orders/cancelOrder', {
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> {
					this.orderPending = false
					this.btnLoading.cancel = false
				},
			})
		},

		successFinish() {
			this.$router.push({name: 'DeliveryOrders'})
		},

	},

	computed: {
		btnDisabled() {
			const status = this.order?.info.status
			return {
				update: this.orderPending,
				delivery: this.orderPending || ['placed', 'packed'].indexOf(status) === -1,
				complete: this.orderPending || ['placed', 'packed', 'delivery'].indexOf(status) === -1,
				cancel: this.orderPending || ['finished', 'canceled'].indexOf(status) !== -1,
			}
		},

		btnLoading() {
			return {
				update: false,
				complete: false,
				cancel: false,
			}
		},

		paymentCurrencySign() {
			let sign = this.$store.state.general.settings.paymentCurrencySign
			return sign ? (' ' + sign) : ''
		},

		deliverySumText() {
			return this.order.info.deliverySum ? (this.numberByThousands(this.order.info.deliverySum) + this.paymentCurrencySign) : ' '
		},

		totalProductsRealSumText() {
			return this.totalProductsRealSum ? this.totalProductsRealSum + this.paymentCurrencySign : ' '
		},

		totalText() {
			return this.numberByThousands(
				Number(this.totalSum(this.order.items, 'realTotal'))
				+ Number(this.order.info.deliverySum)
			) + this.paymentCurrencySign
		},

		totalProductsRealSum() {
			return this.totalSumText(this.order.items, 'realTotal')
		},

	},

	watch: {
		id: {
			immediate: true,
			handler(v) {
				this.$store.dispatch('orders/getOrderDetails', {id: Number(v)})
			}
		},

		'$store.state.orders.orderDetails': {
			immediate: true,
			deep: true,
			handler(v) {
				this.order = JSON.parse(JSON.stringify(v))

				this.order.info.interval = this.deliveryIntervalPropertiesToString(this.order.info)
			}
		},

	},

}
</script>
