@extends('front.layouts.main')

@push('head')

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-MJ3TF99');</script>
  <!-- End Google Tag Manager -->

@endpush

@section('content')

  <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MJ3TF99"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <section class="intro-sect">
    <div class="intro-sect-inner">
      <div class="container">
        <div class="row">
          <div class="col-md-6 content-col">
            <div class="intro-sect__logo">
              <img src="/img/logo.svg" alt="BioVillage">
            </div>
            <h1>Доставляем <br>отборные <br>продукты</h1>
            <div class="intro-sect__subtitle">
              Ваше гастрономическое <br>счастье – наша забота
            </div>
            <div class="intro-sect__action-btns">
              <a href="https://play.google.com/store/apps/details?id=ru.biovillage.android&hl=ru&gl=US" target="_blank" class="download-app-btn">
                <i class="icon icon-googleplay"></i>
                <div class="download-app-btn__label">
                  Скачать
                  <span>Google Play</span>
                </div>
              </a>
              <a href="https://apps.apple.com/RU/app/biovillage/id1546373052" target="_blank" class="download-app-btn">
                <i class="icon icon-appstore"></i>
                <div class="download-app-btn__label">
                  Скачать
                  <span>App Store</span>
                </div>
              </a>
            </div>
          </div>
          <div class="col-md-6">
            <div class="intro-sect__images">
              <div class="prlx" data-speed="-.3"><div class="img-1"></div></div>
              <div class="prlx" data-speed=".5">
                <div class="img-2"></div>
                <div class="img-3"></div>
              </div>
              <div class="prlx" data-speed="-1.5"><div class="img-4"></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <img class="intro-sect-after" src="/img/intro-sect__after.svg" alt="" />
  </section>

  <section class="about-sect">
    <div class="container">
      <div class="row">
        <div class="col-6 d-none d-lg-block">
          <div class="about-sect__images">
            <div class="img-1"></div>
            <div class="img-2 prlx" data-speed="-1"></div>
            <div class="img-3 prlx" data-speed="1"></div>
            <div class="img-4 prlx" data-speed="4"></div>
          </div>
        </div>
        <div class="col-lg-6">
          <h2>Бесплатная доставка при заказе от 2000₽</h2>
          <div class="h3">Фильтры по типу питания:</div>
          <div class="about-sect__filters">
            <div class="filter-item"><i class="icon icon-no-meat"></i> <span>Веган</span></div>
            <div class="filter-item"><i class="icon icon-no-milk"></i> <span>Без лактозы</span></div>
            <div class="filter-item"><i class="icon icon-no-sugar"></i> <span>Без сахара</span></div>
            <div class="filter-item"><i class="icon icon-filters"></i> <span>И многие другие</span></div>
          </div>
          <img class="about-sect__filters-mockup" src="/img/about-sect__filters-mockup.png" alt="Фильтры по типу питания" />
          <div class="h3">Быстрая и удобная оплата</div>
          <div class="about-sect__payment-methods">
            <div class="payment-method">
              <i class="icon icon-applepay"></i>
              <span class="label">Pay</span></div>
            <div class="payment-method">
              <i class="icon icon-googlepay"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
              <span class="label">Pay</span></div>
            <div class="payment-method">
              <i class="icon icon-visa"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>
              <span class="label">Visa</span>
            </div>
            <div class="w-100 d-none d-xl-flex"></div>
            <div class="payment-method">
              <i class="icon icon-mastercard"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
              <span class="label">MasterCard</span>
            </div>
            <div class="payment-method">
              <img class="icon icon-mir" src="/img/mir-logo.svg" alt="">
              <span class="label">МИР</span>
            </div>
          </div>
          <div class="h3">Экоупаковка</div>
          <div class="about-sect__eco-packing">
            <i class="icon icon-recyclable"></i> <span>Доставляем продукты в экопакетах</span>
          </div>
          <img class="about-sect__eco-packing-img" src="/img/about-sect__eco-packing.png" alt="" />
        </div>
      </div>
    </div>
  </section>

  <section class="recommend-sect">
    <img class="recommend-sect-before" src="/img/recommend-sect__before.svg" alt="" />
    <div class="recommend-sect-inner">
      <div class="container">
        <h2>Порекомендуйте <br class="d-none d-md-block">близким наш сервис</h2>
        <div class="row no-gutters">
          <div class="col-md-5">
            <div class="recommend-sect__subtitle">
              и мы отблагодарим вас <br class="d-none d-md-block">бонусными баллами
            </div>
            <div class="prlx" data-speed="2">
              <div class="recommend-sect__products-bag"></div>
            </div>
          </div>
          <div class="col-md">
            <div class="recommend-sect__mockups">
              <img class="prlx" data-speed="-1" srcset="/img/recommend-sect__mockup-1.png 2x" alt="" />
              <img class="prlx" data-speed="1" srcset="/img/recommend-sect__mockup-2.png 2x" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <img class="recommend-sect-after" src="/img/recommend-sect__after.svg" alt="" />
  </section>

  <section class="gift-sect">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-5 col-lg-6 d-none d-md-block">
          <div class="gift-sect__basket">
            <div class="prlx" data-speed="6">
              <img class="basket-img" src="/img/gift-sect__fruits-basket.png" alt="">
            </div>
          </div>
        </div>
        <div class="col-md-7 col-lg-6">
          <h2>Возвращаем 20%</h2>
          <div class="gift-sect__subtitle">суммы первого заказа бонусными баллами</div>
          <div class="gift-sect__action-btns">
            <a href="https://play.google.com/store/apps/details?id=ru.biovillage.android&hl=ru&gl=US" target="_blank" class="download-app-btn">
              <i class="icon icon-googleplay"></i>
              <div class="download-app-btn__label">
                Скачать
                <span>Google Play</span>
              </div>
            </a>
            <a href="https://apps.apple.com/RU/app/biovillage/id1546373052" target="_blank" class="download-app-btn">
              <i class="icon icon-appstore"></i>
              <div class="download-app-btn__label">
                Скачать
                <span>App Store</span>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="decisions-sect">
    <img class="decisions-sect-before" src="/img/decisions-sect__before.svg" alt="" />
    <div class="decisions-sect-inner">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8 col-lg-7 content-col">
            <h2>Зачем мы создали сервис:</h2>
            <div class="decisions-sect__subtitle">Чтобы продлить вашу жизнь за счёт полезных свежих продуктов и освободившегося времени для вашей семьи, увлечений и отдыха.</div>
          </div>
          <div class="col-md-4 col-lg-5">
            <img class="decisions-sect__title-img prlx" data-speed="3" src="/img/decisions-sect__img-1.svg" alt="" />
          </div>
        </div>
        <div class="decisions-sect__decisions-list">
          <div class="decision">
            <div class="decision__img prlx" data-speed="-.8">
              <img src="/img/decisions-sect__img-2.svg" alt="">
            </div>
            <div class="decision__text">
              <div class="title">Проблема:</div>
              <div class="text">Не можем в одном магазине купить всё необходимое. То ли товар закончился, то ли он плохого качества — идём в несколько магазинов или довольствуемся тем, что есть в наличии</div>
              <div class="spacer"></div>
              <div class="title">Решение BioVillage:</div>
              <div class="text">Ассортимент в приложении стабильно поддерживается. Мы привезём всё, что вы закажете, в любом количестве</div>
            </div>
          </div>
          <div class="decision">
            <div class="decision__img prlx" data-speed=".9">
              <img src="/img/decisions-sect__img-3.svg" alt="">
            </div>
            <div class="decision__text">
              <div class="title">Проблема:</div>
              <div class="text">Едем на рынок, тратим 3-4 часа и 10 000 нервных клеток на дорогу и выбор продуктов</div>
              <div class="spacer"></div>
              <div class="title">Решение BioVillage:</div>
              <div class="text">В приложении за 15 минут вы сможете выбрать всё, что нужно</div>
            </div>
          </div>
          <div class="decision">
            <div class="decision__img prlx" data-speed="-.5">
              <img src="/img/decisions-sect__img-4.svg" alt="">
            </div>
            <div class="decision__text">
              <div class="title">Проблема:</div>
              <div class="text">Закупаем продукты на неделю, получается 4 пакета. Их нужно донести до машины, потом от машины до дома</div>
              <div class="spacer"></div>
              <div class="title">Решение BioVillage:</div>
              <div class="text">Доставляем заказ до двери в удобное вам время</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="bv-care">
      <img class="bv-care-before" src="/img/bv-care__before.svg" alt="" />
      <div class="bv-care-inner">
        <div class="container">
          <div class="d-flex flex-column flex-md-row align-items-center justify-content-center">
            <div class="bv-care__title">
              С заботой о вашем <br>
              здоровье, команда BioVillage
            </div>
            <div class="bv-care__img">
              <img class="prlx" data-speed=".8" src="/img/logo_simple-white.svg" alt="">
            </div>
          </div>
        </div>
      </div>
      <img class="bv-care-after" src="/img/bv-care__after.svg" alt="" />
    </div>

    <div class="subscribe-block">
      <div class="container">
        <h2>Хотите получать подарки и промокоды?</h2>
        <form class="subscribe-form">
          <div class="subscribe-form__title">Подпишитесь на рассылку по e-mail и получите свой первый промокод на 2 кг апельсинов или сочный манго "Бразилия"</div>
          <div class="subscribe-form-inner">
            <input type="email" name="email" placeholder="E-Mail" required>
            <button class="btn-accent">Подписаться</button>
          </div>
          <div class="success-msg">
            <div class="success-msg__title">Вы успешно подписались!</div>
            <div class="success-msg__text">Промокод отправлен вам на почту</div>
          </div>
        </form>
      </div>
    </div>

    <div class="footer-inner">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-auto">
            <div class="footer__logo">
              <img src="/img/logo.svg" alt="BioVillage">
            </div>
          </div>
          <div class="col-md-auto">
            <div class="footer__socials">
              <a href="https://vk.com/bio_village" target="_blank"><img src="/img/vk.svg" alt="vk"></a>
              <a href="https://www.instagram.com/biovillage.ru/?hl=ru" target="_blank"><img src="/img/instagram.svg" alt="instagram"></a>
            </div>
          </div>
          <div class="col-auto">
            <div class="footer__phone">
              <i class="icon icon-phone"></i>
              <a href="tel:+7 (916) 911-26-26">+7 (916) 911-26-26</a>
            </div>
          </div>
          <div class="col-auto">
            <div class="footer__email">
              <i class="icon icon-email"></i>
              <a href="mailto:info@biovillage.ru">info@biovillage.ru</a>
            </div>
          </div>
          <div class="col d-none d-xl-block"></div>
          <div class="col-md-auto">
            <div class="footer__action-btns">
              <a href="https://play.google.com/store/apps/details?id=ru.biovillage.android&hl=ru&gl=US" target="_blank" class="download-app-btn small">
                <i class="icon icon-googleplay"></i>
                <div class="download-app-btn__label">
                  Скачать
                  <span>Google Play</span>
                </div>
              </a>
              <a href="https://apps.apple.com/RU/app/biovillage/id1546373052" target="_blank" class="download-app-btn small">
                <i class="icon icon-appstore"></i>
                <div class="download-app-btn__label">
                  Скачать
                  <span>App Store</span>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div class="footer__divider"></div>
        <div class="row align-items-center">
          <div class="col-md">
            <div class="footer__requisites">
              <div class="requisite">
                <span class="requisite__name">ИНН:</span>
                <span class="requisite__val">7734438979</span>
              </div>
              <div class="requisite">
                <span class="requisite__name">ОГРН:</span>
                <span class="requisite__val">1207700459339</span>
              </div>
              <div class="requisite-divider">|</div>
              <div class="requisite">
                <span class="requisite__val"><a href="#policy" class="popup-link">Пользовательское соглашение</a></span>
              </div>
            </div>
            <div class="footer__divider d-md-none"></div>
          </div>
          <div class="col-xl-auto">
            <div class="footer__dev-by">
              <div class="label">Разработка и продвижение проекта:</div> <a href="https://www.globalcode.eu/" target="_blank">Global Code</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <div class="mobile-apps-banner d-sm-none">
    <a href="https://play.google.com/store/apps/details?id=ru.biovillage.android&hl=ru&gl=US" target="_blank" class="download-app-btn">
      <i class="icon icon-googleplay"></i>
      <div class="download-app-btn__label">
        Скачать
        <span>Google Play</span>
      </div>
    </a>
    <a href="https://apps.apple.com/RU/app/biovillage/id1546373052" target="_blank" class="download-app-btn">
      <i class="icon icon-appstore"></i>
      <div class="download-app-btn__label">
        Скачать
        <span>App Store</span>
      </div>
    </a>
  </div>

  <div class="d-none">
    <div class="popup policy-popup" id="policy">
      <div class="h3">Пользовательское соглашение</div>
      <p>Настоящее Пользовательское соглашение является публичной офертой и определяет условия использования сервисов Bio Village (существующих и создаваемых в будущем) веб-сайта Bio Village, доступного по ссылке <a href="https://biovillage.ru" target="_blank">biovillage.ru</a> (далее — Сайт), любыми пользователями сети Интернет, просматривающими Сайт (далее — Пользователи Сайта).</p>
      <p>Настоящее Пользовательское соглашение приравнивается к договору, составленному в письменной форме. Принимая настоящее Пользовательское соглашение, Пользователь выражает полное и безоговорочное согласие со всеми его условиями, в том числе, в части предоставления согласия на обработку персональных данных Пользователя на условиях, указанных в разделе 2 настоящего Пользовательского соглашения. В случае несогласия с данными условиями Пользователь должен покинуть Сайт.</p>
      <ol>
        <li><b>Общие положения</b></li>
        <ol>
          <li>Настоящее Пользовательское соглашение вступает в силу с момента размещения его на Сайте и действует в отношении всей информации, размещенной на сайте в информационно-телекоммуникационной сети «Интернет».</li>
          <li>Пользователь принимает условия настоящего Пользовательского соглашения в полном объеме путем нажатия кнопки подтверждения ознакомления с настоящим Пользовательским соглашением. Пользователь подтверждает свое ознакомление и согласие на использование файлов cookie.</li>
          <li>Cайт использует сервисы веб-аналитики Яндекс.Метрика и Google Analytics. Собранная при помощи cookie информация не может идентифицировать Пользователя, при этом направлена на улучшение работы сайта. Информация об использовании Сайта, собранная при помощи cookie, будет передаваться Яндексу для обработки и оценки использования Сайта, составления отчетов о деятельности Сайта.</li>
          <li>Данный сайт защищен reCAPTCHA от Google. <a href="https://policies.google.com/privacy" target="_blank">Политика конфиденциальности</a> и <a href="https://policies.google.com/terms" target="_blank">Пользовательское соглашение</a>.</li>
        </ol>
        <li><b>Персональные данные</b></li>
        <ol>
          <li>В случае если отдельные сервисы Сайта предусматривают ввод персональных данных, такие персональные данные хранятся и обрабатываются в соответствии с принципами и правилами обработки персональных данных, предусмотренными Федеральным законом Российской Федерации от 27 июля 2006 г. № 152-ФЗ «О персональных данных».</li>
          <li>В отношении персональных данных сохраняется их конфиденциальность, кроме случаев добровольного предоставления Пользователем информации о себе для общего доступа неограниченному кругу лиц.</li>
          <li>Сайт не передает персональные данные третьим лицам, если только такая передача не предусмотрена законодательством Российской Федерации.</li>
          <li>Администрация Сайта принимает необходимые организационные и технические меры для защиты персональных данных от использования, не предусмотренного настоящим Пользовательским соглашением.</li>
        </ol>
        <li><b>Обязательства Пользователя</b></li>
        <ol>
          <li>Пользователь соглашается не предпринимать действий и не оставлять комментарии и записи, которые могут рассматриваться как нарушающие законодательство Российской Федерации или нормы международного права, в том числе в сфере интеллектуальной собственности, авторских и/или смежных прав, общепринятые нормы морали и нравственности, а также любых действий, которые приводят или могут привести к нарушению нормальной работы сервисов Сайта и Сайта в целом.</li>
          <li>Использование материалов Сайта без согласия правообладателей не допускается.</li>
          <li>При цитировании материалов Сайта, включая охраняемые авторские произведения, ссылка на Сайт обязательна.</li>
          <li>Администрация Сайта не несет ответственности за посещение и использование Пользователем внешних ресурсов, ссылки на которые могут содержаться на Сайте.</li>
          <li>Администрация Сайта не несет ответственности и не имеет прямых или косвенных обязательств перед Пользователем в связи с любыми возможными или возникшими убытками, связанными с любым содержанием Сайта, регистрацией авторских прав и сведениями о такой регистрации, доступными на Сайте или полученными через внешние сайты или ресурсы либо иные контакты Пользователя, в которые он вступил, используя размещенную на Сайте информацию или ссылки на внешние ресурсы.</li>
        </ol>
        <li><b>Прочие условия</b></li>
        <ol>
          <li>Все возможные споры, вытекающие из настоящего Пользовательского соглашения или связанные с ним, подлежат разрешению в соответствии с законодательством Российской Федерации.</li>
          <li>Бездействие со стороны Администрации Сайта в случае нарушения Пользователем положений Пользовательского соглашения не лишает Администрацию Сайта права предпринять позднее соответствующие действия в защиту своих интересов и защиту авторских прав на охраняемые в соответствии с законодательством материалы Сайта.</li>
          <li>Администрация Сайта вправе в любое время в одностороннем порядке изменять условия настоящего Пользовательского соглашения. Такие изменения вступают в силу с момента размещения новой версии Пользовательского соглашения на сайте. При несогласии Пользователя с внесенными изменениями он обязан покинуть Сайт, прекратить использование материалов и сервисов Сайта.</li>
        </ol>
      </ol>
      <br>
      <p>Последнее обновление пользовательского соглашения: 28 декабря 2020 г.</p>
    </div>
  </div>

@endsection
