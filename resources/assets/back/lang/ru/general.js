import settingsListObject from './settings'
import alerts from './alerts'

export default {
	settingsListObject,
	alerts,

	data: 'Данные',
	general: 'Общее',
	show: 'Показать',
	error: 'Ошибка | Ошибки',
	yes: 'Да',
	no: 'Нет',
	search: 'Поиск',
	type: 'Тип | Типы',
	method: 'Метод | Методы',
	all: 'Все',
	step: 'Шаг | Шаги',
	quantity: 'Количество',
	sum: 'Сумма',
	total: 'Итого',
	realValue: 'Факт.',
	status: 'Состояние',
	table: 'Таблица',
	map: 'Карта',
	history: 'История',
	tag: 'Тег | Теги',
	promocode: 'Промокод',
	blobNotSupported: 'Ваш бразуер не поддерживает работу с Blob-объектами',
	areYouSure: 'Вы уверены, что хотите',
	performThisAction: 'совершить это действие',
	changesHistory: 'История изменений',
	user: 'Пользователь',
	confirmation: 'Подтверждение',
	priority: 'Приоритет',
	pc: 'шт.',
	bonuses_short: 'ББ',

	activity: 'Активность',
	activeOnly: 'Только активные',
	activeM: 'Активен',
	notActiveM: 'Не активен',
	activeF: 'Активна',
	notActiveF: 'Не активна',

	setting: 'Настройка | Настройки',
	slider: 'Слайдер',
	slide: 'Слайд',

	administration: 'Администрирование',
	registration: 'Регистрация',
	authorization: 'Авторизация',
	logIn: 'Войти',
	logOut: 'Выйти',
	defaultUsername: 'Ваш профиль',
	lastLogin: 'Последний вход',

	caption: 'Заголовок',
	subcaption: 'Подзаголовок',
	title: 'Название',
	description: 'Описание',
	bgColor: 'Цвет фона',
	price: 'Цена | Цены',
	sortOrder: 'Порядок',
	id: 'ID',
	min: 'Минимум',
	images: 'Изображение | Изображения',
	cover: 'Обложка',
	costPrice: 'Себестоимость',
	historyCostPrice: 'История закупочных цен',

	unit: 'Единица измерения | Единицы измерения',
	unitType: 'Форма ед. измерения',
	unitShort: 'Ед.',
	unitFullTitle: 'Полное название',
	unitShortTitle: 'СБН',
	unitShortDerTitle: 'СПН',
	unitFactorTitle: 'Кратность',
	unitBaseStepTitle: 'Шаг в базовых единицах',
	unitStepOfChange: 'Шаг изменения',
	unitEdit: 'единицу',
	unitsNotMatchStepErr: 'Кол-во не соответствует шагу',

	add: 'Добавить',
	new: 'Новый',
	create: 'Создать',
	save: 'Сохранить',
	cancel: 'Отменить',
	close: 'Закрыть',
	cancellation: 'Отмена',
	edit: 'Редактировать',
	delete: 'Удалить',
	deletion: 'Удаление',
	upload: 'Загрузить',
	clear: 'Очистить',
	refresh: 'Обновить',
	actions: 'Действия',
	moreDetailed: 'Подробнее',

	date: 'Дата',
	hour: 'Час | Часы',
	start: 'Начало',
	end: 'Конец',
	dateStarts: 'с',
	dateEnds: 'по',
	dayOfWeek: 'День недели',

	delivery: 'Доставка',
	purchase: 'Закупка | Закупки',
	purchased: 'Закуплено',
	packing: 'Комплектация',
	created: 'Создан',
	updated: 'Обновлен',
	placed: 'Размещен',
	packed: 'Собран',
	delivered: 'Доставлен',
	notDelivered: 'Не доставлен',
	canceled: 'Отменен',
	deleted: 'Удален',
	completed: 'Выполнен',
	refund: 'Возврат',

	createdDate: 'Дата создания',
	updatedDate: 'Дата обновления',

	interval: 'Интервал | Интервалы',
	deliveryDate: 'Дата доставки | Даты доставки',
	deliveryHours: 'Час доставки | Часы доставки',
	deliveryInterval: 'Интервал доставки | Интервалы доставки',
	updateDelivery: 'Перенести доставку',
	deliveryUpdateHint: 'Дата обновится в соответствии с интервалом',
	deliveryDateUpdateHint: 'Дата обновится',
	deliveryIntervalUpdateHint: 'в соответсвии с интервалом',
	
	catalog: 'Каталог',
	gift: 'Подарок | Подарки',
	giftName: 'Название подарка',
	giftsList: 'Список подарков',
	section: 'Категория | Категории',
	sectionEdit: 'категорию',
	product: 'Товар | Товары',
	productEdit: 'товар',
	shelfLife: 'Хранение',
	promo: 'Продвижение',
	composition: 'Состав',
	nutrition: 'БЖУ',
	cert: 'Сертификат | Сертификаты',
	productName: 'Название товара',
	productOfTheDay: 'Товар дня | Товары дня',
	productOfTheWeek: 'Товар недели | Товары недели',
	productsList: 'Список товаров',
	addProduct: 'Добавить товар',
	addGift: 'Добавить подарок',
	addingProduct: 'Добавление товара',
	addingGift: 'Добавление подарка',
	productSearch: 'Поиск товаров',
	giftsSearch: 'Поиск подарков',
	startEnterProductName: 'Начните вводить название товара',
	startEnterGiftName: 'Начните вводить название подарка',
	productRemains: 'Остатки товара',
	remains: 'Остаток',
	withoutPromotion: 'Без продвижения',

	store: 'Склад | Склады',
	storeType: 'Тип склада',
	onStore: 'На склад',
	storeName: 'Наименование склада',
	storePlace: 'Место склада',
	storePlaceName: 'Наименование места склада',
	storeOperationTitle: 'Операция по складу',
	storeOperationType: 'Тип операции',
	storeOperations: {
		put: 'Поступление | Новое поступление',
		take: 'Списание | Новое списание',
		correction: 'Коррекция | Новая коррекция',
		resetCorrection: 'Сброс склада',
		ordering: 'Заказ клиента',
	},
	storeOperationStatus: {
		completed: 'Выполнен',
		reserve: 'Зарезервирован',
		cancel: 'Отменён',
	},
	operationContents: 'Содержимое',

	order: 'Заказ | Заказы',
	orderDetails: 'Детали заказа',
	orderId: 'ID заказа',
	orderDeliveryDateAt: 'на',
	orderNoItemsCall: 'Если товара нет, то позвонить',
	orderItem: 'Позиция | Позиции',
	orderItemEdit: 'Позицию | Позиции',
	orderItemProduct: 'Позиция товара | Позиции товаров',
	orderItemProductEdit: 'Позицию товара | Позиции товаров',
	orderItemGift: 'Позиция подарка | Позиции подарков',
	orderItemGiftEdit: 'Позицию подарка | Позиции подарков',
	orderItemCreated: 'Создана',
	orderItemDeleted: 'Отменена',
	updateOrderInfo: 'Обновить информацию',
	packOrder: 'Заказ укомплектован',
	unpackOrder: 'Разукомплектовать',
	atPar: 'По номиналу',
	actionIfNotDelivery: 'Если нет товара',
	findAnalog: 'Позвонить и подобрать аналог',
	notCallNotBuy: 'Не звонить и не закупать',

	payment: 'Оплата | Оплаты',
	paymentData: 'Данные оплаты',
	transaction: 'Транзакция',
	paymentCreated: 'Проведена',

	client: 'Клиент | Клиенты',
	clientComment: 'Комментарий клиента',
	admin: 'Администратор | Администраторы',
	adminEdit: 'Администратора | Администраторов',
	adminComment: 'Комментарий админа',
	commentForClient: 'Комментарий для клиента',
	comment: 'Комментарий | Комментарии',
	clientId: 'ID клиента',
	adminId: 'ID администратора',


	address: 'Адрес',
	house: 'Дом',
	building: 'Строение',
	entrance: 'Вход',
	floor: 'Этаж',
	doorPhone: 'Домофон',
	apartment: 'Квартира',

	name: 'Имя',
	phone: 'Телефон',
	mail: 'Почта',
	mailing: 'Почтовая рассылка',
	password: 'Пароль',
	role: 'Роль | Роли',
	platform: 'Платформа | Платформы',
	birthday: 'День рождения',
	bonus: 'Бонус | Бонусы',
	blockedBonus: 'Заблокированный бонус | Заблокированные бонусы',
	invitedBy: 'Приглашен | Приглашены',

	deliveryArea: 'Зона доставки | Зоны доставки',

	deliveryFreeSum: 'Минимальная цена для бесплатной доставки',
	deliverySum: 'Цена доставки',
	minPrice: 'Минимальная цена',
	forFreeDelivery: 'для бесплатной доставки',
	mapEditor: 'Редактор карт',
	uploadKML: 'Загрузить карту формата KML',
	uploadKMLHint: 'Текущие данные зон с соответсвующим названием сохранятся <br> (но нестандартный цвет будет применен)',

	downloadExcel: 'Скачать в Excel',
	downloadExcelPrefix: 'Закупка заказов ',

	toOrder: 'К заказу',
	toList: 'К списку',

	titles: {
		settings: 'Основные настройки',
		slider: 'Слайдер на главной странице',
		adminList: 'Список администраторов',
		clients: 'Список клиентов',
		deliveryIntervals: 'Интервалы доставки',
		units: 'Единицы измерения',
		tags: 'Список тегов',
		orders: 'Список заказов',
		purchaseOrders: 'Список позиций для закупки',
		packOrders: 'Список заказов для комплектации',
		PackOrderDetails: 'Комплектация заказа',
		deliveryOrders: 'Список заказов для доставки',
		deliveryOrderDetails: 'Доставка заказа',
		stores: 'Склады',
		createNewStore: 'Добавление нового склада',
		storePlaces: 'Места склада',
		createNewStorePlace: 'Добавление нового места склада',
		updateStorePlace: 'Обновление места склада',
		storeOperations: 'Операции',
		storeProducts: 'Продукты',
	},

	adminRoles: {
		superadmin: 'Суперадмин',
		admin: 'Админ',
		operator: 'Оператор',
		purchaser: 'Закупщик',
		packer: 'Комплектовщик',
		delivery: 'Доставщик',
		storekeeper: 'Склад',
	},

	orderStatuses: {
		new: 'Новый',
		placed: 'Размещен',
		canceled: 'Отменен',
		packed: 'Собран',
		delivery: 'Доставка',
		finished: 'Выполнен',
	},

	paymentStatuses: {
		waiting: 'Ожидание поступления',
		done: 'Поступила',
		cancel: 'Отменена',
	},

	transactionTypes: {
		pay: 'Оплата',
		refund: 'Возврат',
	},

	paymentMethods: {
		cash: 'Наличными',
		card: 'Картой онлайн',
		ccard: 'Картой курьеру',
		gpay: 'GooglePay',
		apay: 'ApplePay',
		bonus: 'Бонусами',
	},

	daysOfWeek: [
		'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье',
	],

	file: 'Файл',
	attachedFile: 'Приложенный файл | Приложенные файлы', 
	addFile: 'Добавить файл',
	openFile: 'Открыть файл',
	downloadFile: 'Скачать файл',
	hasAttachedFiles: 'Есть приложенные файлы',
	maxFileSize: 'Макс. размер файла',
	allowedFileTypes: 'Разрешенные типы файлов',
	mb: 'Мб',

	shortage: 'Нехватка',
	shortageProductPositions: 'Недостаточно товаров на складе по следующим позициям',

	getPurchaseOrdersSelect: {
		label: 'Показывать продукты',
		true: 'Которых нет на складе',
		false: 'Все продукты',
	},

}