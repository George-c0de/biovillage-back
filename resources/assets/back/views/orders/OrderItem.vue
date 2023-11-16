<template>
	<v-container fluid v-if="orderItem.id">
		
		<h1 class="mb-3">
			<router-link
				:to="{name: 'OrderDetails', params: {id: orderItem.orderId}}"
				:title="$tc('toOrder')"
				class="navigation-icon navigation-icon--back"
			>
				<v-icon>arrow_back_ios</v-icon>
			</router-link>
			
			{{ $tc(isProduct ? 'orderItemProduct' : 'orderItemGift') + ' №' + id }}
		</h1>

		<v-row align="end" class="mb-3">
			<v-col cols="12" sm="6" lg="auto">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.createdAt || ' '"
					:label="$tc('orderItemCreated')"
					placeholder=" "
					prepend-icon="double_arrow"
					hide-details
				></v-text-field>
			
			</v-col>
			<v-col cols="12" sm="6" lg="auto">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.deletedAt || ' '"
					:label="$tc('orderItemDeleted')"
					placeholder=" "
					prepend-icon="cancel"
					hide-details
				></v-text-field>

			</v-col>
		</v-row>

		<v-row v-if="isProduct" justify="space-between" align="end">
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.productName || ' '"
					:label="$tc('product')"
					placeholder=" "
					hide-details
				>
					<template #append>
						<v-btn
							color="info"
							icon
							small
							:to="{name: 'CatalogProductEdit', params: {id: orderItem.unitId}}"
						>
							<v-icon>link</v-icon>
						</v-btn>
					</template>
				</v-text-field>
			
			</v-col>
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="numberByThousands(orderItem.price) + ' ' + $store.state.general.settings.paymentCurrencySign"
					:label="$tc('price')"
					placeholder=" "
					hide-details
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.qty + ' x ' + orderItem.unitStep + ' ' + orderItem.unitShortName"
					:label="$tc('quantity')"
					placeholder=" "
					hide-details
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="numberByThousands(orderItem.total) + ' ' + $store.state.general.settings.paymentCurrencySign"
					:label="$tc('sum')"
					placeholder=" "
					hide-details
				></v-text-field>

			</v-col>
		</v-row>

		<v-row v-else justify="space-between" align="end">
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.giftName || ' '"
					:label="$tc('gift')"
					placeholder=" "
					hide-details
				>
					<template #append>

						<v-btn
							color="info"
							icon
							small
							:to="{name: 'Gifts'}"
						>
							<v-icon>link</v-icon>
						</v-btn>

					</template>
				</v-text-field>
			
			</v-col>
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="numberByThousands(orderItem.bonuses)"
					:label="$tc('price', 2)"
					placeholder=" "
					hide-details
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.qty"
					:label="$tc('quantity')"
					placeholder=" "
					hide-details
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="3">

				<v-text-field
					readonly
					dense outlined
					:value="numberByThousands(orderItem.totalBonuses)"
					:label="$tc('sum')"
					placeholder=" "
					hide-details
				></v-text-field>

			</v-col>
		</v-row>
		
		<v-divider class="mt-9 mb-6"></v-divider>

		<h2 class="mb-3">{{ $tc('realValue') + ' ' + $tc('purchased').toLowerCase() }}</h2>

		<v-row v-if="isProduct" justify="space-between" class="mb-3">
			<v-col cols="12" sm="6" lg="4">
				<v-text-field
					dense outlined filled
					v-model="realData.realPrice"
					:label="$tc('realValue') + ' ' + $tc('price').toLowerCase()"
					:placeholder="orderItem.realPrice.toString()"
					:suffix="$store.state.general.settings.paymentCurrencySign"
					hide-details
					@input="updateRealTotal()"
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="4">
				<div class="d-flex">
					<v-text-field
						dense outlined filled
						type="number"
						class="no-input-arrows"
						v-model="realData.realUnits"
						:label="$tc('realValue') + ' ' + $tc('quantity').toLowerCase()"
						:placeholder="realData.unitType == 'der' ? `${orderItem.realUnits}` : `${orderItem.realUnits * orderItem.unitStep}`"
						:hint="realUnitsHint"
						persistent-hint
						:error-messages="realUnitsErrorText"
						@input="updateRealTotal()"
					></v-text-field>
					<v-select
						v-model="realData.unitType"
						hide-details="auto"
						dense outlined filled
						:items="[{'value': 'base', 'text': orderItem.unitShortName}, {'value': 'der', 'text': orderItem.unitShortDerName}]"
						class="pt-0 ml-2 text-right hint-text-right"
						style="width: 90px"
						@change="changeUnitType($event)"
					></v-select>
				</div>
			</v-col>
			<v-col cols="12" sm="6" lg="4">

				<v-text-field
					dense outlined filled
					v-model="realData.realTotal"
					:label="$tc('realValue') + ' ' + $tc('sum').toLowerCase()"
					:placeholder="orderItem.realTotal.toString()"
					:suffix="$store.state.general.settings.paymentCurrencySign"
					hide-details
				></v-text-field>

			</v-col>
		</v-row>

		<v-row v-else justify="space-between" align="end" class="mb-3">
			<v-col cols="12" sm="6" lg="4">

				<v-text-field
					readonly
					dense outlined
					:value="orderItem.bonuses || ' '"
					:label="$tc('realValue') + ' ' + $tc('price').toLowerCase()"
					hide-details
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="4">

				<v-text-field
					dense outlined filled
					v-model="realData.realQty"
					:label="$tc('realValue') + ' ' + $tc('quantity').toLowerCase()"
					:placeholder="orderItem.realQty.toString()"
					hide-details
					@input="realData.realTotalBonuses = orderItem.bonuses * realData.realQty"
				></v-text-field>

			</v-col>
			<v-col cols="12" sm="6" lg="4">

				<v-text-field
					dense outlined filled
					v-model="realData.realTotalBonuses"
					:label="$tc('realValue') + ' ' + $tc('sum').toLowerCase()"
					:placeholder="orderItem.realTotalBonuses.toString()"
					hide-details
				></v-text-field>

			</v-col>
		</v-row>


		<v-row justify="space-between">
			<v-col cols="auto">

				<v-btn
					color="success"
					class="align-self-start"
					@click="updateOrderItem"
				>
					<v-icon left>
						save
					</v-icon>
					{{ $tc('save') }}
				</v-btn>

			</v-col>
			<v-col cols="auto">

				<v-btn
					color="error"
					class="align-self-start"
					@click="cancelDialog = true"
				>
					<v-icon left>
						cancel
					</v-icon>
					{{ $tc('cancel') + ' ' + $tc('orderItemEdit') }}
				</v-btn>

			</v-col>
		</v-row>

		<ConfirmDialog
			:show="cancelDialog"
			:title="$tc('cancellation')"
			agreeIcon="delete"
			@agree="confirmCancelDialog"
			@disagree="cancelDialog = false"
		>
			{{ $tc('areYouSure') + ' ' + $tc('cancel').toLowerCase() }} <br /> <b>{{ $tc(isProduct ? 'orderItemProductEdit' : 'orderItemGiftEdit') + ' №' + id }}</b>?
		</ConfirmDialog>

	</v-container>
</template>

<script>
import {numberByThousands, formatUnits} from '@bo/mixins'
import ConfirmDialog from '@bo/components/ConfirmDialog'

export default {
	props: ['id', 'isProduct', 'backLink'],

	mixins: [numberByThousands, formatUnits],

	components: {
		ConfirmDialog,
	},

	data() { return {
		orderItem: {},
		realData: {},
		cancelDialog: false,

	}},

	computed: {
		realUnitsHint() {
			let realUnits = this.realData.unitType == 'der' ? 
				this.realData.realUnits * this.orderItem.unitFactor / this.orderItem.unitStep : 
				this.realData.realUnits / this.orderItem.unitStep
			realUnits = Math.ceil(realUnits * 100) / 100
			return `${realUnits} x ${this.orderItem.unitStep} ${this.orderItem.unitShortName}`
		},

		realUnitsErrorText() {
			let units;
			if (this.realData.unitType == 'der') {
				units = this.realData.realUnits * this.orderItem.unitFactor / this.orderItem.unitStep
			} else {
				units = this.realData.realUnits / this.orderItem.unitStep
			}
			if (!Number.isInteger(units)) return this.$tc('unitsNotMatchStepErr')
			else return null
		}
	},

	methods: {
		updateOrderItem() {
			let data = JSON.parse(JSON.stringify(this.realData))
			data.realUnits = data.unitType == 'der' ? 
				data.realUnits * this.orderItem.unitFactor / this.orderItem.unitStep : 
				data.realUnits / this.orderItem.unitStep
			this.$store.dispatch('orders/updateOrderItem', {
				id: Number(this.id),
				isProduct: this.isProduct,
				data: data,
				then: ()=> {
					if (this.backLink) this.$router.push({name: this.backLink, params: {id: this.orderItem.orderId}})
				},
			})
		},

		confirmCancelDialog() {
			this.$store.dispatch('orders/cancelOrderItem', {
				id: Number(this.id),
				isProduct: this.isProduct,
				then: () => {
					this.cancelDialog = false
					if (this.backLink) this.$router.push({name: this.backLink, params: {id: this.orderItem.orderId}})
				},
			})
		},

		changeUnitType(val) {
			if (val == 'der') {
				this.realData.realUnits = this.unitsToDer(this.realData.realUnits, this.orderItem.unitFactor)
			} else if (val == 'base') {
				this.realData.realUnits = this.unitsToBase(this.realData.realUnits, this.orderItem.unitFactor)
			}
		},

		updateRealTotal() {
			let units = this.realData.unitType == 'base' ?  this.realData.realUnits / this.orderItem.unitStep : this.realData.realUnits
			this.realData.realTotal = this.realData.realPrice * units
		},

	},

	watch: {
		id: {
			immediate: true,
			handler(v) {
				this.$store.dispatch('orders/getOrderItem', {
					id: Number(v),
					isProduct: this.isProduct,
				})
			}
		},

		'$store.state.orders.orderItem': {
			immediate: true,
			deep: true,
			handler(v) {
				this.orderItem = JSON.parse(JSON.stringify(v))
				let unitType = this.orderItem.realUnits * this.orderItem.unitStep > this.orderItem.unitFactor ? 'der' : 'base'
				this.realData = this.isProduct ? {
					realUnits: unitType == 'der' ? 
						this.orderItem.realUnits * this.orderItem.unitStep / this.orderItem.unitFactor : 
						this.orderItem.realUnits * this.orderItem.unitStep,
					realPrice: this.orderItem.realPrice,
					realTotal: this.orderItem.realTotal,
					unitType: unitType
				} : {
					realQty: this.orderItem.realQty,
					realTotalBonuses: this.orderItem.realTotalBonuses,
				}
			}
		},

	},

}
</script>
