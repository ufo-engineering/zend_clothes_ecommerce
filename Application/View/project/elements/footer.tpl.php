

    <section class="links-box">
      <div class="block">
        <div class="links">
          <div class="head">Сервис и помощь</div>
          <div class="clear"></div>
          <ul>
          	<?php foreach ($menuHelp as $menuItem): ?><li><a href="<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a></li><?php endforeach; ?>
          </ul>
        </div>

        <div class="links">
          <div class="head">О компании</div>
          <div class="clear"></div>
          <ul>
          	<?php foreach ($menuAbout as $menuItem): ?><li><a href="<?= get($menuItem, 'url') ?>"><?= get($menuItem, 'titleRu') ?></a></li><?php endforeach; ?>
          </ul>
        </div>

        <div class="contacts">
          <div class="head">Контакты</div>
          <div class="clear"></div>
          <div class="phones"><?= $config->get('indexPhoneFirst') ?><br><?= $config->get('indexPhoneSecond') ?></div>
          <div class="clear"></div>
          <div class="email"><a href="mailto:<?= $config->get('indexMail') ?>"><?= $config->get('indexMail') ?></a></div>
          <div class="clear"></div>
          <a href="#" class="all">Все контакты</a>
        </div>

        <div class="group">
          <div class="head">Присоединяйтесь в соцсетях:</div>
          <div class="vk">
            <img src="/public/project/img/join.png" alt=""/>
          </div>
        </div>
      </div>
    </section>

    <section class="section-hr">
      <div class="block"></div>
    </section>

    <section class="partners">
      <div class="head">мы принимаем К оплате :</div>

      <div class="items">
        <img src="/public/project/img/partners.png" alt=""/>
      </div>
    </section>

    <section class="section-hr gray">
      <div class="block"></div>
    </section>
    <footer>
      <div class="block">
        <?= $config->get('indexCopy') ?>
      </div>
    </footer>

    <!--  SCRIPTS  -->
    <script src="/public/project/js/jquery.js"></script>
    <script src="/public/project/js/jquery.flexslider-min.js"></script>
    <script src="/public/project/js/jquery.nouislider.js"></script>
    <script src="/public/project/js/jquery.liblink.js"></script>
    <script src="/public/project/js/jquery.selecter.js"></script>
    <script src="/public/project/js/init.js"></script>

    <script>
      $(document).ready(function () {
        $("#top-slider").flexslider({
          animation: 'slide',
          directionNav: false
        });
        $("#new-slider").flexslider({
          animation: 'slide',
          controlNav: false,
          itemWidth: 169,
          minItems: 5,
          maxItems: 5
        });
        $("#popular-slider").flexslider({
          animation: 'slide',
          controlNav: false,
          itemWidth: 169,
          minItems: 5,
          maxItems: 5
        });
        $("#filter-slider").noUiSlider({
          start: [ 200, 2500 ],
          range: {
            min: [ 200 ],
            max: [ 4000 ]
          },
          step: 1
        }).noUiSlider_pips({
          mode: 'values',
          values: [200, 1000, 2000, 3000, 4000],
          density: 4,
          stepped: true
        }).Link('lower').to($('#from')).Link('upper').to($('#to'));
      });
    </script>
<script type="text/javascript">
var _cp = {trackerId: 10512};
(function(d){
var cp=d.createElement('script');cp.type='text/javascript';cp.async = true;
cp.src='//tracker.cartprotector.com/cartprotector.js';
var s=d.getElementsByTagName('script')[0]; s.parentNode.insertBefore(cp, s);
})(document);
</script>
  </body>
</html>