<template>
	<v-app>
		<v-navigation-drawer v-model="drawer" app v-if="isAuth">
			<v-list dense class="py-0">
				<v-list-item
					v-for="(item, i) in menu"
					:key="i"
					v-show="checkRole($router.resolve(item.to).route)"
					:to="$router.resolve(item.to).route.path"
					:ripple="$route.name !== item.to.name"
					color="primary"
				>
					<v-list-item-icon>
						<v-icon>{{ item.icon }}</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>{{ item.name }}</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
			</v-list>
		</v-navigation-drawer>

		<v-app-bar app color="primary" dark v-if="isAuth">
			<v-app-bar-nav-icon @click.stop="drawer = !drawer" />
			<v-toolbar-title>BioVillage | {{ $tc('administration') }}</v-toolbar-title>
			<v-spacer></v-spacer>

			<v-menu offset-y nudge-bottom="5">
				<template #activator="{ on }">
					<v-btn v-on="on" outlined>
						<v-icon class="mr-2">person</v-icon>
							{{ userName }}
					</v-btn>
				</template>

				<v-list class="py-0">
					<v-list-item @click="logout">
						<v-list-item-title>{{ $tc('logOut') }}</v-list-item-title>
					</v-list-item>
				</v-list>
			</v-menu>
		</v-app-bar>

		<v-main>
			<router-view class="flex-grow-1 d-flex flex-column flex-children-grow-0"></router-view>
		</v-main>

		<Alerts />

	</v-app>
</template>

<script>
import Alerts from '@bo/components/Alerts'
import {adminRoles} from '@bo/mixins'

export default {
	components: {
		Alerts,
	},

	mixins: [adminRoles],

	data() {
		return {
			drawer: null,

		}
	},

	computed: {
		isAuth() {
			return this.$store.getters['auth/isAuth']
		},

		userName() {
			let user = this.$store.state.auth.user
			return user && user.name ? user.name : this.$tc('defaultUsername')
		},
		
		menu() { return [
			{
				name: this.$tc('catalog'),
				icon: 'store',
				to: {name: 'Catalog'},
			},
			{
				name: this.$tc('gift', 2),
				icon: 'card_giftcard',
				to: {name: 'Gifts'},
			},
			{
				name: this.$tc('slider'),
				icon: 'slideshow',
				to: {name: 'Slider'},
			},
			{
				name: this.$tc('tag', 2),
				icon: 'tag',
				to: {name: 'Tags'},
			},
			{
				name: this.$tc('unit', 2),
				icon: 'bar_chart',
				to: {name: 'Units'},
			},
			{
				name: this.$tc('admin', 2),
				icon: 'supervised_user_circle',
				to: {name: 'AdminList'},
			},
			{
				name: this.$tc('client', 2),
				icon: 'people',
				to: {name: 'Clients'},
			},
			{
				name: this.$tc('deliveryInterval', 2),
				icon: 'format_line_spacing',
				to: {name: 'DeliveryIntervals'},
			},
			{
				name: this.$tc('deliveryArea', 2),
				icon: 'map',
				to: {name: 'DeliveryAreas'},
			},
			{
				name: this.$tc('order', 2),
				icon: 'local_grocery_store',
				to: {name: 'Orders'},
			},
			{
				name: this.$tc('purchase'),
				icon: 'account_balance_wallet',
				to: {name: 'PurchaseOrders'},
			},
			{
				name: this.$tc('packing'),
				icon: 'backpack',
				to: {name: 'PackOrders'},
			},
			{
				name: this.$tc('delivery'),
				icon: 'delivery_dining',
				to: {name: 'DeliveryOrders'},
			},
			{
				name: this.$tc('setting', 2),
				icon: 'settings',
				to: {name: 'Settings'},
			},
			{
				name: this.$tc('store', 2),
				icon: 'corporate_fare',
				to: {name: 'Stores'},
			},
		]},
		
	},

	methods: {
		logout() {
			this.$store.dispatch('auth/logout')
		},

	},

	watch: {
		'$store.state.general.settings.lang': {
			handler(v) {
				this.$i18n.locale = v
				this.$vuetify.lang.current = v
			}
		},
		
	},

	created() {
		// Обновим юзера, чтобы обновить токен в axios
		if (this.isAuth) this.$store.commit('auth/SET_USER', this.$store.state.auth.user)
		
	},

}
</script>

<style lang="scss">
html {
	overflow-y: auto;
}

.navigation-icon {
	all: unset;
	// display: inline-block;
	// padding: 0 6px;

	.v-icon {
		pointer-events: none;
	}

	&:hover,
	&:focus {
		.v-icon {
			color: rgba(0, 0, 0, 0.87);
			transform: none;
		}
	}

	&--back .v-icon {
		transform: translateX(4px);
	}
}

.row + {
	.row {
		margin-top: -12px;
	}

	.row--dense {
		margin-top: -4px;
	}
}

.v-text-field--filled.v-text-field--outlined {
	input {
		margin-top: 0;
	}
	.v-text-field__suffix {
		margin-top: 0;
	}
}

.text-right input {
	text-align: end;
}

.hint-text-right .v-messages__message {
	text-align: right;
}

.v-main__wrap {
	display: flex;
	flex-direction: column;
}

.flex-children-grow-0 > * {
	flex-grow: 0;
}

.v-data-table--hide-sort-badge .v-data-table-header__sort-badge {
	display: none;
}

.v-file-input .v-text-field__slot {
	cursor: pointer;
}

.v-application--is-ltr .v-input--selection-controls {
	padding-top: 0;
	margin-top: 0;

	&__input {
		margin-right: 0;

		+ .v-label {
			margin-left: 8px;
		}
	}

	&--label-left {
		.v-input__slot {
			flex-direction: row-reverse;
		}
		.v-label {
			margin-left: 0px;
			margin-right: 8px;
		}
	}
}

.v-application .v-input--unchanged {
	.v-input__append-inner,
	.v-input__append-outer {
		.v-icon {
			visibility: hidden;
		}
	}

	.v-input--selection-controls__input .v-icon {
		color: rgba(0, 0, 0, 0.42) !important;
	}
}

.v-application .v-input--changed {
	> .v-input__control > .v-input__slot::before {
		border-color: currentColor !important;
	}

	.v-input--selection-controls__input .v-icon {
		color: currentColor !important;
	}

	.v-input__append-inner,
	.v-input__append-outer {
		.v-icon {
			color: currentColor !important;
		}
	}
}

.v-application .v-input--hide-append {
	.v-input__append-inner,
	.v-input__append-outer {
		.v-icon {
			visibility: hidden;
		}
	}
}

// .v-data-table.v-data-table--editable {
// 	.v-data-table__wrapper {
// 		overflow: hidden;
// 		padding-bottom: 1px;
// 	}

// 	// > .v-data-table__wrapper > table > tbody > tr:not(:last-child) > td:not(.v-data-table__mobile-row) {
// 	// 	border: none;
// 	// }
	
// 	// .v-data-table__mobile-row {
// 	// 	align-items: end;
// 	// }

// 	&.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr > td:not(.v-data-table__mobile-row),
// 	&.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr > th:not(.v-data-table__mobile-row) {
//    	border-bottom: thin solid rgba(0, 0, 0, 0.12);
// 	}
// }

.v-data-table__wrapper {
	padding-bottom: 1px;
}

.cursor-pointer {
	cursor: pointer !important;
}

.v-icon,
.v-btn {
	&--disabled {
		pointer-events: auto;
		cursor: default !important;
	}
}

.v-select--size .v-select__selection {
	position: absolute;
}

.table-filters {
	// padding-left: 16px;
	// padding-right: 16px;
	align-items: end;
	padding-bottom: 12px;
	border-bottom: 2px solid rgba(0, 0, 0, 0.12);
	
	&#{&} {
		margin-top: 0;
		margin-bottom: 12px;
	}

	> * {
		flex: 0 0 auto;
		width: auto;
		max-width: 100%;
	}

	& > *:not(&__actions) {
		// padding: 6px;
		// background-color: #f0f0f0;
		// border-radius: 4px 4px 0 0;
		// margin: 6px;

		// flex-grow: 1;

		padding-top: 0;
	}

	&__actions {
		margin-left: auto;
	}

}

.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr > td:not(.v-data-table__mobile-row):not(:last-child),
.theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr > th:not(.v-data-table__mobile-row):not(:last-child),
.theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr > th:not(:last-child) {
	border-right: thin solid rgba(0, 0, 0, 0.12);
}

.theme--light.v-text-field--filled > .v-input__control > .v-input__slot {
	background-color: #F8F8F8;
}

.v-text-field--outlined fieldset {
	border: 1px solid #E8E8E8;
}

.theme--light.v-tabs .v-tab--active::before {
	opacity: 0.12;
}

.v-tabs-bar__content {
	&::before {
		content: '';
		position: absolute;
		bottom: 0;
		width: 100%;
		height: 2px;
		background: #D8D8D8;
	}
}

.row-table {
	// padding: 4px;
	// border-radius: 4px;
	// border: thin solid #F2F2F2;

	> .row {
		&.row {
			margin: 0;
		}

		> * {
			padding-left: 8px;
			padding-right: 8px;
		}

		&:not(:last-child) {
			border-bottom: thin solid #F2F2F2;
		}

		> .font-weight-bold {
			border-right: thin solid #F2F2F2;

			+ * {
				padding-right: 0;
			}
		}
	}
}

.theme--light.v-input {
	&--is-readonly:not(&--not-readonly)
	input {
		color: #828282;
	}

	&.v-text-field--outlined fieldset {
		border-color: #F2F2F2;
	}
}

.v-expansion-panel-content__wrap {
	&::before {
		content: '';
		display: block;
		padding-top: 24px;
		border-top: thin solid rgba(0, 0, 0, 0.12);
	}
}

.v-input.no-input-arrows{
	input[type=number] {
		-moz-appearance: textfield;
		&::-webkit-outer-spin-button,
		&::-webkit-inner-spin-button{
			-webkit-appearance: none;
				margin: 0;
		}
	}
}

.th-nowrap th {
  white-space: nowrap;
}

.input-sm-padding input{
	padding: 4px 0 2px !important;
}

</style>
