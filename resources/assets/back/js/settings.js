export default {
	general: {
		options: {
			lang: {
				name: 'lang',
				type: 'select',
				items: [
					'ru',
					'en',
				],
				value: null,
			},
			mobilePhoneFormat: {
				name: 'mobilePhoneFormat',
				value: null,
			},
		},
	},

	referral: {
		options: {
			bonusesForReferral: {
				name: 'bonusesForReferral',
				value: null,
			},
			maxReferralsPerUser: {
				name: 'maxReferralsPerUser',
				value: null,
			},
		},
	},

	cashback: {
		options: {
			cashbackPercentForEachOrder: {
				name: 'cashbackPercentForEachOrder',
				value: null,
			},
			cashbackPercentForFirstOrder: {
				name: 'cashbackPercentForFirstOrder',
				value: null,
			},
		},
	},

	delivery: {
		options: {
			deliveryDesc: {
				name: 'deliveryDesc',
				type: 'area',
				rows: 2,
				value: null,
			},
			deliveryFreeSum: {
				name: 'deliveryFreeSum',
				value: null,
			},
			deliveryTodayHour: {
				name: 'deliveryTodayHour',
				value: null,
			},
			deliveryTitle: {
				name: 'deliveryTitle',
				value: null,
			},
			deliveryMaxInInterval: {
				name: 'deliveryMaxInInterval',
				value: null,
			},
		},
	},

	payment: {
		options: {
			paymentDesc: {
				name: 'paymentDesc',
				type: 'area',
				rows: 3,
				value: null,
			},
			paymentGateway: {
				name: 'paymentGateway',
				type: 'select',
				items: [
					{
						text: 'YooKassa',
						value: 'yk',
					},
					// {
					// 	divider: true,
					// },
					// {
					// 	text: 'Stripe',
					// 	value: 'st',
					// 	disabled: true,
					// },
					// {
					// 	text: 'PayPal',
					// 	value: 'pl',
					// 	disabled: true,
					// },
				],
				value: null,
			},
			paymentGatewayShopId: {
				name: 'paymentGatewayShopId',
				value: null,
			},
			paymentGatewayMobileKey: {
				name: 'paymentGatewayMobileKey',
				value: null,
			},
			paymentGatewaySecretKey: {
				name: 'paymentGatewaySecretKey',
				value: null,
			},
			paymentGatewayReturnUrl: {
				name: 'paymentGatewayReturnUrl',
				value: null,
			},
			paymentGatewayPurchaseName: {
				name: 'paymentGatewayPurchaseName',
				value: null,
			},
			paymentGatewayPurchaseDesc: {
				name: 'paymentGatewayPurchaseDesc',
				value: null,
			},
			paymentCurrency: {
				name: 'paymentCurrency',
				type: 'select',
				items: [
					'USD',
					'RUB',
				],
				value: null,
			},
			paymentCurrencySign: {
				name: 'paymentCurrencySign',
				value: null,
			},
			paymentCashEnabled: {
				name: 'paymentCashEnabled',
				type: 'check',
				value: null,
			},
			paymentCCardEnabled: {
				name: 'paymentCCardEnabled',
				type: 'check',
				value: null,
			},
			paymentGPayEnabled: {
				name: 'paymentGPayEnabled',
				type: 'check',
				value: null,
			},
			paymentAPayEnabled: {
				name: 'paymentAPayEnabled',
				type: 'check',
				value: null,
			},
			paymentCardEnabled: {
				name: 'paymentCardEnabled',
				type: 'check',
				value: null,
			},
			paymentBonusEnabled: {
				name: 'paymentBonusEnabled',
				type: 'check',
				value: null,
			},
		},
	},

	contacts: {
		options: {
			officeAddress: {
				name: 'officeAddress',
				value: null,
			},
			officeLat: {
				name: 'officeLat',
				value: null,
			},
			officeLon: {
				name: 'officeLon',
				value: null,
			},
			officePhoneClient: {
				name: 'officePhoneClient',
				value: null,
			},
			officePhonePartners: {
				name: 'officePhonePartners',
				value: null,
			},
			officeEmail: {
				name: 'officeEmail',
				inputType: 'email',
				value: null,
			},
		},
	},

	socials: {
		options: {
			socialVk: {
				name: 'socialVk',
				value: null,
			},
			socialInstagram: {
				name: 'socialInstagram',
				value: null,
			},
			socialFacebook: {
				name: 'socialFacebook',
				value: null,
			},
			socialTelegram: {
				name: 'socialTelegram',
				value: null,
			},
		},
	},

	support: {
		options: {
			supportTelegram: {
				name: 'supportTelegram',
				value: null,
			},
		},
	},

	about: {
		options: {
			aboutPurposes: {
				name: 'aboutPurposes',
				value: null,
			},
			aboutAdvantage1: {
				name: 'aboutAdvantage1',
				value: null,
			},
			aboutAdvantage2: {
				name: 'aboutAdvantage2',
				value: null,
			},
			aboutAdvantage3: {
				name: 'aboutAdvantage3',
				value: null,
			},
			aboutAdvantage4: {
				name: 'aboutAdvantage4',
				value: null,
			},
			aboutOrgDetails: {
				name: 'aboutOrgDetails',
				type: 'area',
				rows: 10,
				value: null,
			},
		},
	},
	
	founder: {
		options: {
			founderName: {
				name: 'founderName',
				value: null,
			},
			founderPhoto: {
				name: 'founderPhoto',
				type: 'img',
				value: null,
			},
		},
	},

	map: {
		options: {
			mapsToken: {
				name: 'mapsToken',
				value: null,
			},
			mapsPrimaryCity: {
				name: 'mapsPrimaryCity',
				value: null,
			},
			mapsCenterLat: {
				name: 'mapsCenterLat',
				value: null,
			},
			mapsCenterLon: {
				name: 'mapsCenterLon',
				value: null,
			},
			mapsZoom: {
				name: 'mapsZoom',
				value: null,
			},
			mapSearchRadius: {
				name: 'mapSearchRadius',
				value: null,
			},
		},
	},

	promo: {
		options: {
			promoProdsName: {
				name: 'promoProdsName',
				value: null,
			},
			promoProdsBgColor: {
				name: 'promoProdsBgColor',
				type: 'color',
				value: null,
			},
			promoProdsImg: {
				name: 'promoProdsImg',
				type: 'img',
				value: null,
			},
		},
	},

	stores: {
		options: {
			storeCanAdminMinus: {
				name: 'storeCanAdminMinus',
				type: 'check',
				value: null,
			},
		},
	},

}