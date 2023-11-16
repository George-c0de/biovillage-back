<template>
	<v-container fluid>

		<h1 class="mb-3">{{ $tc('orderDetails') + ' №' + id }}</h1>

		<v-tabs
			v-model="tab"
			class="mb-3"
		>
			<v-tabs-slider color="primary"></v-tabs-slider>

			<v-tab>{{ $tc('general') }}</v-tab>
			<v-tab>{{ $tc('product', 2) }}</v-tab>
			<v-tab>{{ $tc('gift', 2) }}</v-tab>
			<v-tab>{{ $tc('payment', 2) }}</v-tab>
		</v-tabs>

		<v-tabs-items v-model="tab">
			<v-tab-item>

				<v-form
					:disabled="!loaded || btnDisabled.update"
					@submit.prevent="updateOrderInfo"
				>
					<v-card
						tile
						elevation="0"
						:disabled="!loaded || btnDisabled.update"
						class="mb-3"
					>
						<v-card-text>

							<v-row>
								<v-col cols="12" sm="6" lg="4">

									<v-select
										dense outlined filled
										v-model="orderInfo.status"
										:label="$tc('status')"
										:items="orderStatuses"
										prepend-inner-icon="double_arrow"
										hide-details="auto"
										size="10"
										class="v-select--size"
									></v-select>

								</v-col>
								<v-col cols="12" sm="6" lg="5">

									<v-autocomplete
										dense outlined filled
										v-model="orderInfo.deliveryIntervalId"
										:label="$tc('deliveryInterval')"
										prepend-inner-icon="access_time"
										:items="deliveryIntervals"
										:hint="$tc('deliveryDateUpdateHint')"
										persistent-hint
										hide-details="auto"
										size="10"
									></v-autocomplete>

								</v-col>
								<v-col cols="12" lg="3">

									<v-text-field
										dense outlined
										disabled
										:value="orderDetails.info ? orderDetails.info.deliveryDate : ''"
										:label="$tc('deliveryDate')"
										prepend-inner-icon="today"
										:hint="$tc('deliveryIntervalUpdateHint')"
										persistent-hint
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" lg="6">

									<v-textarea
										outlined filled
										v-model="orderInfo.adminsComment"
										:label="$tc('adminComment')"
										prepend-inner-icon="comment_bank"
										hide-details="auto"
										rows="3"
									></v-textarea>

								</v-col>
								<v-col cols="12" lg="6">

									<v-textarea
										outlined filled
										v-model="orderInfo.commentForClient"
										:label="$tc('commentForClient')"
										prepend-inner-icon="comment"
										hide-details="auto"
										rows="3"
									></v-textarea>

								</v-col>
							</v-row>

						</v-card-text>
						<v-card-actions class="py-4">

							<v-row justify="space-between">
								<v-col cols="auto">

									<v-btn
										type="submit"
										color="success"
										:disabled="btnDisabled.update"
										:loading="btnLoading.update"
									>
										<v-icon left>
											save
										</v-icon>
										{{ $tc('save') }}
									</v-btn>

								</v-col>
								<v-col v-if="orderDetails && orderDetails.info" cols="auto">

									<v-row>
										<v-col cols="auto">
											
											<v-btn
												color="primary"
												:disabled="btnDisabled.complete"
												:loading="btnLoading.complete"
												@click="completeOrder"
											>
												<v-icon left>
													check
												</v-icon>
												{{ $tc('completed') }}
											</v-btn>
											
										</v-col>
										<v-col cols="auto">
											
											<v-btn
												color="error"
												:disabled="btnDisabled.cancel"
												:loading="btnLoading.cancel"
												@click="cancelOrder"
											>
												<v-icon left>
													cancel
												</v-icon>
												{{ $tc('cancel') }}
											</v-btn>

										</v-col>
										<v-col cols="auto">
											
											<v-btn
												color="error"
												:disabled="btnDisabled.refund"
												:loading="btnLoading.refund"
												@click="refundOrder"
											>
												<v-icon left>
													backspace
												</v-icon>
												{{ $tc('refund') }}
											</v-btn>
											
										</v-col>
									</v-row>

								</v-col>
							</v-row>

						</v-card-actions>
					</v-card>
				</v-form>

				<v-divider></v-divider>

				<template v-if="loaded">
					<v-card
						tile
						elevation="0"
						:disabled="!loaded"
						class="mb-3"
					>
						<v-card-title>
							{{ $tc('history') }}
						</v-card-title>
						<v-card-text>

							<v-row>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="orderDetails.info.createdAt || ' '"
										dense outlined
										:label="$tc('created')"
										placeholder=" "
										prepend-icon="double_arrow"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="orderDetails.info.packedAt || ' '"
										dense outlined
										:label="$tc('placed')"
										placeholder=" "
										prepend-icon="backpack"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="orderDetails.info.finishedAt || ' '"
										dense outlined
										:label="$tc('completed')"
										placeholder=" "
										prepend-icon="playlist_add_check"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="orderDetails.info.updatedAt || ' '"
										dense outlined
										:label="$tc('updated')"
										placeholder=" "
										prepend-icon="update"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
							</v-row>

						</v-card-text>
					</v-card>
					<v-divider></v-divider>

					<v-card
						tile
						elevation="0"
						:disabled="!loaded"
						class="mb-3"
					>
						<v-card-title>
							{{ $tc('address') }} ({{ orderDetails.info.addressName }})
						</v-card-title>

						<v-card-subtitle v-if="orderDetails.info.daName">
							<span class="text-subtitle-2">{{ $tc('deliveryArea') }}: </span>
							<span class="text-body-2">{{ orderDetails.info.daName }}</span>
						</v-card-subtitle>
						
						<v-card-text>
							<span>{{ orderDetails.info.addressStreet }},</span>
							<span v-if="orderDetails.info.addressHouse">{{ $tc('house') + ' ' + orderDetails.info.addressHouse }},</span>
							<span v-if="orderDetails.info.addressBuilding">{{ $tc('building') + ' ' + orderDetails.info.addressBuilding }},</span>
							<span v-if="orderDetails.info.addressEntrance">{{ $tc('entrance') + ' ' + orderDetails.info.addressEntrance }},</span>
							<span v-if="orderDetails.info.addressFloor">{{ $tc('floor') + ' ' + orderDetails.info.addressFloor }},</span>
							<span v-if="orderDetails.info.addressDoorphone">{{ $tc('doorPhone') + ' ' + orderDetails.info.addressDoorphone }},</span>
							<span v-if="orderDetails.info.addressAppt">{{ $tc('apartment') + ' ' + orderDetails.info.addressAppt }}</span>
						</v-card-text>

						<v-card-title v-if="orderDetails.info.addressComment">
							{{ $tc('comment') }}
						</v-card-title>
						
						<v-card-text>
							{{ orderDetails.info.addressComment }}
						</v-card-text>

					</v-card>
					<v-divider></v-divider>

					<v-card
						tile
						elevation="0"
						:disabled="!loaded"
						class="mb-3"
					>
						<v-card-title>
							{{ $tc('price') }}
						</v-card-title>
						
						<v-card-text>
							<v-row>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="numberByThousands(orderDetails.info.productsSum) + ($store.state.general.settings.paymentCurrencySign ? (' ' + $store.state.general.settings.paymentCurrencySign) : '')"
										dense outlined
										:label="$tc('product', 2)"
										placeholder=" "
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="numberByThousands(orderDetails.info.deliverySum) + ($store.state.general.settings.paymentCurrencySign ? (' ' + $store.state.general.settings.paymentCurrencySign) : '')"
										dense outlined
										:label="$tc('delivery')"
										placeholder=" "
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="numberByThousands(orderDetails.info.total) + ($store.state.general.settings.paymentCurrencySign ? (' ' + $store.state.general.settings.paymentCurrencySign) : '')"
										dense outlined
										:label="$tc('total')"
										placeholder=" "
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="3">

									<v-text-field
										readonly
										:value="numberByThousands(orderDetails.info.bonuses)"
										dense outlined
										:label="$tc('bonus', 2)"
										placeholder=" "
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
							</v-row>
						</v-card-text>

						<v-card-subtitle v-if="orderDetails.info.promoCode">
							<span class="text-subtitle-2">{{ $tc('promocode'), }}: </span>
							<span class="text-body-2">{{ orderDetails.info.promoCode }}</span>
						</v-card-subtitle>

					</v-card>
					<v-divider></v-divider>

					<v-card
						tile
						elevation="0"
						:disabled="!loaded"
						class="mb-3"
					>
						<v-card-title>
							{{ $tc('client') }} 
							<router-link class="ml-3" :to="{name: 'ClientAccount', params: {id: orderDetails.info.clientId}}">[{{ orderDetails.info.clientId }}]</router-link>
						</v-card-title>
						
						<v-card-text>
							<v-row>
								<v-col cols="12" lg="4">

									<v-text-field
										readonly
										:value="orderDetails.info.clientName || ' '"
										dense outlined
										:label="$tc('name')"
										placeholder=" "
										prepend-icon="person"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="4">

									<v-text-field
										readonly
										:value="orderDetails.info.clientPhone || ' '"
										dense outlined
										:label="$tc('phone')"
										placeholder=" "
										prepend-icon="phone"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
								<v-col cols="12" sm="6" lg="4">

									<v-text-field
										readonly
										:value="orderDetails.info.platform || ' '"
										dense outlined
										:label="$tc('platform')"
										placeholder=" "
										prepend-icon="devices_other"
										hide-details="auto"
										size="10"
									></v-text-field>

								</v-col>
							</v-row>
						</v-card-text>

					</v-card>
					<v-divider></v-divider>

					<v-card
						tile
						elevation="0"
						:disabled="!loaded"
						class="mb-3"
					>
						<v-card-title>
							{{ $tc('clientComment') }}
						</v-card-title>
						
						<v-card-text>
							{{ orderDetails.info.clientsComment }}
						</v-card-text>

						<v-card-title>
							{{ $tc('actionIfNotDelivery') }}
						</v-card-title>
						
						<v-card-text>
							{{ $tc(orderDetails.info.actionIfNotDelivery) }}
						</v-card-text>

					</v-card>
					<v-divider></v-divider>

					<v-card
						tile
						elevation="0"
						:disabled="!loaded"
						class="mb-3"
					>
						<v-card-title>
							{{ $tc('error', 2) }}
						</v-card-title>
						
						<v-card-text>
							{{ orderDetails.info.error }}
						</v-card-text>

					</v-card>

				</template>
			</v-tab-item>

			<v-tab-item>

				<v-data-table
					:headers="productsHeaders"
					:items="orderDetails.items"
					item-key="id"
					must-sort
					sort-by="id"
					disable-pagination
					hide-default-footer
					fixed-header
				>
					<template #item.id="{ item }">

						<router-link :to="{name: 'OrderItemProduct', params: {id: item.id, backLink: 'OrderDetails'}}">
							{{ item.id }}
						</router-link>
						
					</template>
					<template #item.productName="{ item }">

						<router-link :to="{name: 'CatalogProductEdit', params: {id: item.productId}}">
							{{ item.productName }}
						</router-link>

						</template>

						<template #item.price="{ item }">
							{{ numberByThousands(item.price) }}
						</template>

						<template #item.qty="{ item }">
							{{ autoFormatUnits({ item, q: item.qty * item.unitStep }) }}
							<br>
							({{ String(item.qty) + ' x ' + String(item.unitStep) + ' ' + item.unitShortName }})
						</template>

						<template #item.total="{ item }">
							{{ numberByThousands(item.total) }}
						</template>

						<template #item.realUnits="{ item }">
							{{ autoFormatUnits({ item }) }}
							<br>
							({{ (Math.round(item.realUnits / item.unitStep * 100) / 100) + ' x ' + String(item.unitStep) + ' ' + item.unitShortName }})
						</template>

						<template v-if="orderDetails.info.productsSum" #body.append="{ isMobile }">

							<tr :class="{'d-block': isMobile}">
								<template v-for="n in productsHeaders">
									<td
										:key="n.value"
										v-if="n.value === 'total'"
										align="right"
										:class="{'d-flex align-center justify-end': isMobile}"
									>
										<div>
											<span class="font-weight-black">{{ $tc('total') }}: </span> {{ numberByThousands(orderDetails.info.productsSum) }}
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

			</v-tab-item>
			<v-tab-item>

				<v-data-table
					:headers="giftsHeaders"
					:items="orderDetails.gifts"
					item-key="id"
					must-sort
					sort-by="id"
					disable-pagination
					hide-default-footer
					fixed-header
				>
					<template #item.id="{ item }">

						<router-link :to="{name: 'OrderItemGift', params: {id: item.id, backLink: 'OrderDetails'}}">
							{{ item.id }}
						</router-link>
						
					</template>
					<template #item.totalBonuses="{ item }">
						{{ numberByThousands(item.totalBonuses) }}
					</template>
					<template v-if="orderDetails.info.bonuses" #body.append="{ isMobile }">

							<tr :class="{'d-block': isMobile}">
								<template v-for="n in giftsHeaders">
									<td
										:key="n.value"
										v-if="n.value === 'totalBonuses'"
										align="right"
										:class="{'d-flex align-center justify-end': isMobile}"
									>
										<div>
											<span class="font-weight-black">{{ $tc('total') }}: </span> {{ numberByThousands(orderDetails.info.bonuses) }}
										</div>
									</td>
									<td :key="n.value" v-else :class="{'d-none': isMobile}"></td>
								</template>
							</tr>

						</template>
				</v-data-table>

			</v-tab-item>
			<v-tab-item>

				<v-data-table
					:headers="paymentHeaders"
					:items="orderDetails.payments"
					item-key="id"
					sort-by="id"
					must-sort
					disable-pagination
					hide-default-footer
					fixed-header
					height="100%"
				>
					<template #item.data="{ item }">
						<v-menu
							v-model="menus[item.id]"
							v-if="item.data"
							:close-on-content-click="false"
							max-height="100%"
						>
							<template v-slot:activator="{ on, attrs }">

								<v-btn
									text
									v-bind="attrs"
									v-on="on"
								>
									{{ $tc('show') }}
								</v-btn>

							</template>
							<v-card>
								<v-card-title :style="{'position': 'sticky', 'top': '0','background': '#fff'}">

									<v-row align="center">
										<v-col>{{ $tc('paymentData') }}</v-col>
										<v-col cols="auto">

											<v-btn
												icon
												@click.stop="menus[item.id] = false"
											>
												<v-icon>
													close
												</v-icon>
											</v-btn>

										</v-col>
									</v-row>

								</v-card-title>
								<v-card-text>
									<div style="white-space: pre-wrap">{{ item.data }}</div>
								</v-card-text>
							</v-card>

						</v-menu>
					</template>
					
					<template #item.total="{ item }">
						{{ numberByThousands(item.total) }}
					</template>
				</v-data-table>

			</v-tab-item>
		</v-tabs-items>

	</v-container>
</template>

<script>
import {orderStatuses, deliveryIntervals, numberByThousands, paymentValues, totalSum, formatUnits} from '@bo/mixins'

export default {
	props: ['id'],
	
	mixins: [orderStatuses, deliveryIntervals, numberByThousands, paymentValues, totalSum, formatUnits],
	
	data() { return {
		orderDetails: {},
		orderInfo: {
			status: null,
			deliveryIntervalId: null,
			adminsComment: null,
			commentForClient: null,
		},
		orderPending: false,
		menus: {},

		productsHeaders: [
			{
				text: this.$tc('orderItem'),
				value: 'id',
			},
			{
				text: this.$tc('product'),
				value: 'productName',
			},
			{
				text: this.$tc('price') + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'price',
				align: 'end',
			},
			{
				text: this.$tc('quantity'),
				value: 'qty',
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
				text: this.$tc('orderItem'),
				value: 'id',
			},
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
				value: 'qty',
				align: 'end',
			},
			{
				text: this.$tc('sum') + ', ' + this.$tc('bonus', 2),
				value: 'totalBonuses',
				align: 'end',
			},
		],

		paymentHeaders: [
			{
				text: this.$tc('id'),
				value: 'id',
			},
			{
				text: this.$tc('method'),
				value: 'method',
			},
			{
				text: this.$tc('transaction'),
				value: 'transaction',
			},
			{
				text: this.$tc('status'),
				value: 'status',
			},
			{
				text: this.$tc('data'),
				value: 'data',
			},
			{
				text: this.$tc('paymentCreated'),
				value: 'createdAt',
			},
			{
				text: this.$tc('paymentStatuses.cancel'),
				value: 'canceledAt',
			},
			{
				text: this.$tc('sum') + (this.$store.state.general.settings.paymentCurrencySign ? (', ' + this.$store.state.general.settings.paymentCurrencySign) : ''),
				value: 'total',
				align: 'end',
			},
			
		],

	}},
	
	computed: {
		tab: {
			get() {
				return this.$store.state.orders.orderDetailsTab
			},
			set(v) {
				this.$store.state.orders.orderDetailsTab = v
			}
		},

		btnDisabled() {
			const status = this.orderDetails?.info.status
			return {
				update: this.orderPending,
				complete: this.orderPending || ['placed', 'packed', 'delivery'].indexOf(status) === -1,
				cancel: this.orderPending || ['finished', 'canceled'].indexOf(status) !== -1,
				refund: this.orderPending || status !== 'finished',
			}
		},

		btnLoading() {
			return {
				update: false,
				complete: false,
				cancel: false,
				refund: false,
			}
		},

		loaded() {
			return this.$store.state.orders.orderDetailsLoaded
		},

		totalProductsRealSum() {
			return this.totalSumText(this.orderDetails.items, 'realTotal')
		},

	},
	
	methods: {
		updateOrderInfo() {
			this.orderPending = true
			this.$store.dispatch('orders/updateOrderInfo', {
				data: this.orderInfo,
				id: Number(this.id),
				finish: ()=> this.orderPending = false,
			})
		},

		completeOrder() {
			this.orderPending = true
			this.$store.dispatch('orders/completeOrder', {
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> this.orderPending = false,
			})
		},

		cancelOrder() {
			this.orderPending = true
			this.$store.dispatch('orders/cancelOrder', {
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> this.orderPending = false,
			})
		},

		refundOrder() {
			this.orderPending = true
			this.$store.dispatch('orders/refundOrder', {
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> this.orderPending = false,
			})
		},

		successFinish() {
			this.$router.push({name: 'Orders'})
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
				this.orderDetails = JSON.parse(JSON.stringify(v))
				let menus = {},
					info = this.orderDetails.info,
					products = this.orderDetails.items,
					payments = this.orderDetails.payments
				
				if (info !== undefined) for (const key in this.orderInfo) {
					this.orderInfo[key] = info[key]
				}

				if (payments !== undefined) payments.forEach(e => {
					e.transaction = this.transactionTypes.find(p => p.value === e.transaction).text
					e.method = this.paymentMethods.find(p => p.value === e.method).text
					e.status = this.paymentStatuses.find(p => p.value === e.status).text
					menus[e.id] = null
				})
				this.menus = menus
			}
			
		},
		
	},
	
}
</script>
