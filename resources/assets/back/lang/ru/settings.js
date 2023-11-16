export default {
	general: {
		title: 'Общие',
		options: {
			lang: {
				label: 'Язык',
			},
			mobilePhoneFormat: {
				label: 'Формат мобильного телефона, dart',
			}
		}
	},

	referral: {
		title: 'Реферальная программа',
		options: {
			bonusesForReferral: {
				label: 'Реферальный бонус',
			},
			maxReferralsPerUser: {
				label: 'Максимум рефералов на пользователя',
			},
		},
	},

	cashback: {
		title: 'Кэшбэк',
		options: {
			cashbackPercentForEachOrder: {
				label: '% кешбека за каждый заказ',
			},
			cashbackPercentForFirstOrder: {
				label: '% кешбек за первый заказ',
			},
		},
	},

	delivery: {
		title: 'Доставка',
		options: {
			deliveryDesc: {
				label: 'Описание условий доставки',
			},
			deliveryFreeSum: {
				label: 'Сумма заказа для бесплатной доставки',
			},
			deliveryTodayHour: {
				label: 'Час заказа для доставки в тот же день, 0-24',
			},
			deliveryTitle: {
				label: 'Заголовок доставки',
			},
			deliveryMaxInInterval: {
				label: 'Максимальное число доставок за интервал',
				hint: 'Пока не работает',
			},
		},
	},

	payment: {
		title: 'Оплата',
		options: {
			paymentDesc: {
				label: 'Описание способов оплаты',
			},
			paymentGateway: {
				label: 'Платежный сервис',
				type: 'select',
			},
			paymentGatewayShopId: {
				label: 'ID продавца',
			},
			paymentGatewayMobileKey: {
				label: 'Ключ платежей для мобильных устройств',
			},
			paymentGatewaySecretKey: {
				label: 'Ключ платежей для сервера',
			},
			paymentGatewayReturnUrl: {
				label: 'Адрес перенаправления после платежа',
			},
			paymentGatewayPurchaseName: {
				label: 'Название предмета оплаты',
				hint: 'Для платежной формы',
			},
			paymentGatewayPurchaseDesc: {
				label: 'Описание оплаты',
				hint: 'Для платежной формы',
			},
			paymentCurrency: {
				label: 'Валюта платежа',
			},
			paymentCurrencySign: {
				label: 'Символ выбранной валюты',
				hint: 'Например, ₽ или $',
			},
			paymentCashEnabled: {
				label: '@:paymentMethods.cash',
			},
			paymentCCardEnabled: {
				label: '@:paymentMethods.ccard',
			},
			paymentGPayEnabled: {
				label: '@:paymentMethods.gpay',
			},
			paymentAPayEnabled: {
				label: '@:paymentMethods.apay',
			},
			paymentCardEnabled: {
				label: '@:paymentMethods.card',
			},
			paymentBonusEnabled: {
				label: '@:paymentMethods.bonus',
			},
		},
	},

	contacts: {
		title: 'Контакты',
		options: {
			officeAddress: {
				label: 'Адрес офиса',
			},
			officeLat: {
				label: 'Широта',
			},
			officeLon: {
				label: 'Долгота',
			},
			officePhoneClient: {
				label: 'Телефон для клиентов',
			},
			officePhonePartners: {
				label: 'Телефон для партнеров',
			},
			officeEmail: {
				label: 'Электронная почта',
			},
		},
	},

	socials: {
		title: 'Социальные сети',
		options: {
			socialVk: {
				label: 'Вконтакте',
			},
			socialInstagram: {
				label: 'Instagram',
			},
			socialFacebook: {
				label: 'Facebook',
			},
			socialTelegram: {
				label: 'Telegram',
			},
		},
	},

	support: {
		title: 'Поддержка',
		options: {
			supportTelegram: {
				label: 'Telegram',
			},
		},
	},

	about: {
		title: 'О компании',
		options: {
			aboutPurposes: {
				label: 'Цели',
			},
			aboutAdvantage1: {
				label: 'Преимущество',
			},
			aboutAdvantage2: {
				label: 'Преимущество',
			},
			aboutAdvantage3: {
				label: 'Преимущество',
			},
			aboutAdvantage4: {
				label: 'Преимущество',
			},
			aboutOrgDetails: {
				label: 'Реквизиты компании',
			},
		},
	},

	founder: {
		title: 'Основатель',
		options: {
			founderName: {
				label: 'Имя владельца',
			},
			founderPhoto: {
				label: 'Фото владельца',
			},
		},
	},

	map: {
		title: 'Карта',
		options: {
			mapsToken: {
				label: 'Токен GoogleMaps',
			},
			mapsPrimaryCity: {
				label: 'Основной город доставки',
				hint: 'Этот город нужно подставлять в запрос подсказки адреса. Нужно для фильтрации адресов в определенном городе.',
			},
			mapsCenterLat: {
				label: 'Широта координаты для центрирования карты',
				hint: 'Вещественное число до 7 знаков',
			},
			mapsCenterLon: {
				label: 'Долгота координаты для центрирования карты',
				hint: 'Вещественное число до 7 знаков',
			},
			mapsZoom: {
				label: 'Первоночальный масштаб карты',
				hint: 'Вещественное число до 2 знаков',
			},
			mapSearchRadius: {
				label: 'Радиус поиска, км',
				hint: 'Целое число',
			},
		},
	},

	promo: {
		title: 'Промо товары',
		options: {
			promoProdsName: {
				label: 'Название категории',
			},
			promoProdsBgColor: {
				label: 'Цвет фона',
			},
			promoProdsImg: {
				label: 'Картинка группы товаров по акции',
			},
		},
	},

	stores: {
		title: 'Склады',
		options: {
			storeCanAdminMinus: {
				label: 'Разрешить отрицательное кол-во товара на складе',
			},
		},
	},

}

