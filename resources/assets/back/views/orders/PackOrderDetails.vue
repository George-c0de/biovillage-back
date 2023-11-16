<template>
	<v-container
		v-if="order.info.id"
		fluid
	>

		<h1>
			<router-link
				:to="{name: 'PackOrders'}"
				:title="$tc('toList')"
				class="navigation-icon navigation-icon--back"
			>
				<v-icon>arrow_back_ios</v-icon>
			</router-link>
			
			{{ $tc('titles.PackOrderDetails') + ' №' + id + ' ' + $tc('orderDeliveryDateAt') + ' ' + order.info.deliveryDate }}
		</h1>

		<v-row class="mt-0 mb-6">
			<v-col cols="12" md="6" class="d-flex flex-column">

				<v-card outlined class="pa-4">
					<div class="mb-3">

						<span class="font-weight-black">{{ $tc('status') }}:</span>
						{{ order.info.statusText }}

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
				class="mb-6 th-nowrap"
				style="overflow-y: auto"
			>
				<template #item.id="{ item }">

					<router-link :to="{name: 'OrderItemProduct', params: {id: item.id, backLink: 'PackOrderDetails'}}">
						{{ item.id }}
					</router-link>
					
				</template>
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
					<div class="d-flex mb-1 mt-2">
						<v-text-field
							v-model="item.realUnits"
							type="number"
							hide-details="auto"
							:hint="getRealUnitsHint(item)"
							persistent-hint
							size="6"
							class="pt-0 text-right hint-text-right no-input-arrows"
							style="width: 124px"
							:error-messages="realUnitsErrorsIds.indexOf(item.id) > -1 ? $tc('unitsNotMatchStepErr') : null"
							@input="updateTotalProduct(item)"
						></v-text-field>
						<v-select
							v-model="item.unitType"
							hide-details="auto"
							single-line
							:items="[{'value': 'base', 'text': item.unitShortName}, {'value': 'der', 'text': item.unitShortDerName}]"
							class="pt-0 ml-2 text-right hint-text-right"
							style="max-width: 64px"
							@change="changeUnitType(item, $event)"
						></v-select>
					</div>
				</template>

				<template #item.realPrice="{ item }">

					<v-text-field
						v-model="item.realPrice"
						type="number"
						single-line
						persistent-hint
						size="8"
						class="pt-0 mb-1 mt-2 text-right no-input-arrows"
						@input="updateTotalProduct(item)"
					></v-text-field>

				</template>
				<template #item.realTotal="{ item }">

					<v-text-field
						v-model="item.realTotal"
						single-line
						persistent-hint
						size="10"
						class="pt-0 mb-1 mt-2 text-right"
					></v-text-field>

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

		<h2>{{ $tc('gift', 2) }}</h2>
		<div class="d-flex flex-column" style="max-height: 80vh; overflow: hidden">

			<v-data-table
				:headers="giftsHeaders"
				:items="order.gifts"
				item-key="id"
				must-sort
				sort-by="id"
				disable-pagination
				hide-default-footer
				fixed-header
				class="mb-6 th-nowrap"
				style="overflow-y: auto"
			>
				<template #item.id="{ item }">

					<router-link :to="{name: 'OrderItemGift', params: {id: item.id, backLink: 'PackOrderDetails'}}">
						{{ item.id }}
					</router-link>
					
				</template>
				<template #item.bonuses="{ item }">
					{{ numberByThousands(item.bonuses) }}
				</template>

				<template #item.totalBonuses="{ item }">
					{{ numberByThousands(item.totalBonuses) }}
				</template>
				<template #item.realQty="{ item }">

					<v-text-field
						v-model="item.realQty"
						single-line
						hide-details="auto"
						size="6"
						:suffix="item.unitShortName"
						class="pt-0 text-right"
						@input="updateTotalGift(item)"
					></v-text-field>

				</template>
				<template #item.realTotalBonuses="{ item }">

					<v-text-field
						v-model="item.realTotalBonuses"
						single-line
						hide-details="auto"
						size="10"
						class="pt-0 text-right"
					></v-text-field>

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
							<td
								:key="n.value"
								v-else-if="n.value === 'realTotalBonuses'"
								align="right"
								:class="{'d-flex align-center justify-end': isMobile}"
							>
								<div>
									<span class="font-weight-black">
										{{ $tc('total') }}<template v-if="isMobile"> {{ $tc('realValue') }}</template>:
									</span>
									{{ totalGiftsRealSum }}
								</div>
							</td>
							<td :key="n.value" v-else :class="{'d-none': isMobile}"></td>
						</template>
					</tr>

				</template>
			</v-data-table>
		</div>

		<v-row class="my-0">
			<v-col cols="auto">

				<v-form @submit.prevent="packOrder()">
					<v-btn
						type="submit"
						color="success"
						:disabled="btnDisabled.pack || realUnitsErrorsIds.length > 0"
						:loading="btnLoading.pack"
					>
						<v-icon left>
							check
						</v-icon>
						{{ $tc('packOrder') }}
					</v-btn>
				</v-form>

			</v-col>
			<v-col cols="auto" class="mr-auto">

				<v-btn
					color="primary"
					@click="atPar()"
				>
					<v-icon left>
						content_copy
					</v-icon>
					{{ $tc('atPar') }}
				</v-btn>

			</v-col>
			<v-col cols="auto">

				<v-form @submit.prevent="unpackOrder()">
					<v-btn
						type="submit"
						color="secondary"
						:disabled="btnDisabled.unpack"
						:loading="btnLoading.unpack"
					>
						<v-icon left>
							undo
						</v-icon>
						{{ $tc('unpackOrder') }}
					</v-btn>
				</v-form>

			</v-col>
			<v-col cols="auto">

				<v-form @submit.prevent="cancelOrder()">
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

		<ConfirmDialog
			:show="dialogShortageForce"
			:title="$tc('confirmation')"
			agreeIcon="check"
			@agree="packOrder(true)"
			@disagree="dialogShortageForce = false"
			maxWidth="480px"
		>
			<div class="black--text mb-3">{{ $tc('shortageProductPositions') }}:</div>
			<div 
				v-for="(product, i) in shortageForceProducts" 
				:key="'sp_'+i" 
				class="my-1" 
				v-html="buildShortageText(product)"
			></div>
		</ConfirmDialog>

	</v-container>
</template>

<script>
import ConfirmDialog from '@bo/components/ConfirmDialog'
import {orderStatuses, deliveryIntervals, numberByThousands, totalSum, formatUnits} from '@bo/mixins.js'

export default {
	props: ['id'],

	mixins: [orderStatuses, deliveryIntervals, numberByThousands, totalSum, formatUnits],

	components: {
		ConfirmDialog
	},

	data() { return {
		order: {},
		orderPending: false,
		
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
			{
				text: this.$tc('realValue') + ' ' + this.$tc('quantity').toLowerCase(),
				value: 'realQty',
				align: 'end',
			},
			{
				text: this.$tc('realValue') + ' ' + this.$tc('sum').toLowerCase() + ', ' + this.$tc('bonus', 2),
				value: 'realTotalBonuses',
				align: 'end',
			},
		],

		realUnitsErrorsIds: [],
		dialogShortageForce: false,
		shortageForceProducts: []

	}},

	methods: {
		updateTotalProduct(item) {
			let units;
			if (item.unitType == 'der') {
				units = item.realUnits * item.unitFactor / item.unitStep
			} else {
				units = item.realUnits / item.unitStep
			}
			item.realTotal = Math.round(parseFloat(units * item.realPrice) * 100) / 100

			// Валидация:
			let i = this.realUnitsErrorsIds.indexOf(item.id)
			if (Number.isInteger(units)) {
				if (i > -1) this.realUnitsErrorsIds.splice(i, 1)
			} else {
				if (i < 0) this.realUnitsErrorsIds.push(item.id)
			}
		},

		updateTotalGift(item) {
			item.realTotalBonuses = item.realQty * item.bonuses
		},

		atPar() {
			this.order.items.forEach((item) => {
				if (item.unitType == 'der') {
					if ((item.qty * item.unitStep) < item.unitFactor) {
						item.unitType = 'base'
						item.realUnits = item.qty * item.unitStep
					} else {
						item.realUnits = item.qty * item.unitStep / item.unitFactor
					}
				} else {
					if ((item.qty * item.unitStep) >= item.unitFactor) {
						item.unitType = 'der'
						item.realUnits = item.qty * item.unitStep / item.unitFactor
					} else {
						item.realUnits = item.qty * item.unitStep
					}
				}
				item.realPrice = item.price
				item.realTotal = item.total
				this.updateTotalProduct(item)
			})
			this.order.gifts.forEach((item) => {
				item.realQty = item.qty
				item.realTotalBonuses = item.totalBonuses
			})
		},

		updateOrderInfo(form, then = false) {
			this.orderPending = true
			this.btnLoading.update = true

			this.$store.dispatch('orders/updateOrderInfo', {
				id: Number(this.id),
				data: new FormData(form),
				then: then ? this.successFinish : undefined,
				finish: ()=> {
					this.orderPending = false
					this.btnLoading.update = false
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

		packOrder(force = false) {
			this.orderPending = true
			this.btnLoading.pack = true
			if (!force) this.shortageForceProducts = []

			let data = new FormData()

			this.order.items.forEach((item, i) => {
				data.append('items[' + i.toString() + '][id]', item.id)
				data.append('items[' + i.toString() + '][realTotal]', item.realTotal)
				let realUnits = item.unitType == 'der' ? item.realUnits * item.unitFactor : item.realUnits
				data.append('items[' + i.toString() + '][realUnits]', realUnits)
				data.append('items[' + i.toString() + '][realPrice]', item.realPrice)
				data.append('items[' + i.toString() + '][productId]', item.productId)
			})
			this.order.gifts.forEach((item, i) => {
                data.append('gifts[' + i.toString() + '][id]', item.id)
                data.append('gifts[' + i.toString() + '][giftId]', item.giftId)
                data.append('gifts[' + i.toString() + '][realQty]', item.realQty)
                data.append('gifts[' + i.toString() + '][realTotalBonuses]', item.realTotalBonuses)
			})

			data.append('force', force)
			
			this.$store.dispatch('orders/packOrder', {
				data,
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> {
					this.orderPending = false
					this.btnLoading.pack = false
				},
				error: (errors) => {
					if (errors && (errors.products || errors.gifts)) {
						let showDialog = false
						if (errors.products) errors.products.forEach(err => {
							let i = this.order.items.findIndex(v => v.productId === err.productId)
							if (i > -1) {
								let item = JSON.parse(JSON.stringify(this.order.items[i]))
								item.shortage = Math.abs(err.shortage)
								this.shortageForceProducts.push(item)
							}
							showDialog = true
						})
						if (errors.gifts) errors.gifts.forEach(err => {
							let i = this.order.gifts.findIndex(v => v.giftId === err.giftId)
							if (i > -1) {
								let gift = JSON.parse(JSON.stringify(this.order.gifts[i]))
								gift.isGift = true
								gift.shortage = Math.abs(err.shortage)
								this.shortageForceProducts.push(gift)
							}
							showDialog = true
						})
						if (showDialog) this.dialogShortageForce = true
					}
				}
			})
		},

		buildShortageText(product) {
			let units = product.shortage + ' ' + (product.unitShortName || this.$tc('pc'))
			if (!product.isGift && product.shortage >= product.unitFactor) {
				units = this.unitsToDer(product.shortage, product.unitFactor) + ' ' + product.unitShortDerName
			}
			return `<b>${product.productName || product.giftName || product.name}</b>: ${this.$tc('shortage').toLowerCase()} ${units}`
		},

		unpackOrder() {
			this.orderPending = true
			this.btnLoading.unpack = true

			this.$store.dispatch('orders/unpackOrder', {
				id: Number(this.id),
				then: this.successFinish,
				finish: ()=> {
					this.orderPending = false
					this.btnLoading.unpack = false
				},
			})
		},

		successFinish() {
			this.$router.push({name: 'PackOrders'})
		},

		changeUnitType(item, val) {
			let i = this.order.items.indexOf(item)
			if (i < 0 ) return;
			if (val == 'der') {
				item.realUnits = this.unitsToDer(item.realUnits, item.unitFactor)
			} else if (val == 'base') {
				item.realUnits = this.unitsToBase(item.realUnits, item.unitFactor)
			}
			Vue.set(this.order.items, i, item)
		},

		getRealUnitsHint(item) {
			let realUnits = item.unitType == 'der' ? item.realUnits * item.unitFactor / item.unitStep : item.realUnits / item.unitStep
			realUnits = Math.ceil(realUnits * 100) / 100
			return `${realUnits} x ${item.unitStep} ${item.unitShortName}`
		},

	},

	computed: {
		btnDisabled() {
			const status = this.order?.info.status
			return {
				update: this.orderPending,
				pack: this.orderPending || status !== 'placed',
				unpack: this.orderPending || status !== 'packed',
				cancel: this.orderPending || ['finished', 'canceled'].indexOf(status) !== -1
			}
		},

		btnLoading() {
			return {
				update: false,
				pack: false,
				unpack: false,
				cancel: false,
			}
		},

		totalProductsRealSum() {
			return this.totalSumText(this.order.items, 'realTotal')
		},

		totalGiftsRealSum() {
			return this.totalSumText(this.order.gifts, 'realTotalBonuses')
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

				let statusText = this.orderStatuses.find(s => s.value === this.order.info.status)
				this.order.info.statusText = statusText ? statusText.text : this.order.info.status

				this.order.items.forEach((item) => {
					if (item.realUnits * item.unitStep >= item.unitFactor) {
						item.unitType = 'der'
						item.realUnits = item.realUnits * item.unitStep / item.unitFactor
					} else {
						item.unitType = 'base'
						item.realUnits = item.realUnits * item.unitStep
					}
				})

				this.order.info.interval = this.deliveryIntervalPropertiesToString(this.order.info)
			}
		},

	},

}
</script>
