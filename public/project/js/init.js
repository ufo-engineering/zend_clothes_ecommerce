(function ($) {
  $(function () {
    $('.tabs > .tabs-head').on('click', 'a:not(.active)', function () {
      $(this).parent().find("a").each(function () {
        $(this).removeClass("active");
      });
      $(this).addClass("active").parent().parent().children(".tabs-content").children(".tab").each(function () {
        $(this).removeClass("active");
      }).parent().children("." + $(this).attr("href").split("#")[1]).addClass("active");

      return false;
    })
  });
  $(function () {
    $('.payments-box').on('click', 'a:not(.active)', function () {
      $(this).parent().find("a").each(function () {
        $(this).removeClass("active");
      });
      $(this).addClass("active").parent().find('input').val($(this).data('name'));

      return false;
    })
  });
  $(".accordion .name").click(function () {
    var el = $(this), li = el.parent(), text = li.find('.text');
    if (text.css('display') == 'block') {
      text.slideUp('slow');
      el.removeClass("active");
    } else {
      text.slideDown('slow');
      el.addClass('active');
    }

    return false;
  });
  $(".counter span").click(function () {
    var el = $(this),
			parent = $(this).parent(),
      field = parent.find('input'),
      val = parseInt(field.val());
			
		if(field.attr('readonly') != 'readonly') {
			if (el.hasClass('up')) {
				val++;
			} else {
				val = val == 0 ? 0 : val - 1;
			}

			field.val(val);
		}
		
		if(parent.hasClass('box')) {
			var index = parent.index() / 2;
			var size = parent.parent().parent().find('.size').find('.box').eq(index);
			if(val == 0) {
				parent.removeClass('active');
				size.addClass('none');
			} else {
				parent.addClass('active');
				size.removeClass('none');
			}
			//parent(
		}
  });
  $(function () {
    $('.tabs > .tabs-head').on('click', 'a', function () {
      var el = $(this);

      if (el.hasClass("active"))
        return false;


      el.parent().parent().find("a").each(function () {
        $(this).removeClass("active");
      });
      el.addClass("active").parent().parent().children(".tabs-content").children(".tab").each(function () {
        $(this).removeClass("active");
      }).parent().children("." + el.attr("href").split("#")[1]).addClass("active");

      return false;
    })
    $(".dropDownItem").on('click', '.key', function () {
      var el = $(this).parent();

      if (el.hasClass('active')) {
        el.find(".content").slideUp(500);
        setTimeout(function () {

          el.removeClass('active');
        }, 600);
      } else {
        el.find(".content").slideDown(500);
        setTimeout(function () {
          el.addClass('active');
        }, 600);
      }

      return false;
    });
    $(".dropDownItem").on('click', '.h', function () {
      var el = $(this).parent();

      if (el.hasClass('active')) {
        el.find(".content").slideUp(500);
        setTimeout(function () {

          el.removeClass('active');
        }, 600);
      } else {
        el.find(".content").slideDown(500);
        setTimeout(function () {
          el.addClass('active');
        }, 600);
      }

      return false;
    });
    $(".select").selecter();
    $(".itemSlider").on('click', '.left img:not(.active)', function () {
      var el = $(this).parent().parent();
      el.find('.left').find('img').each(function () {
        $(this).removeClass('active');
      });
      $(this).addClass('active');
      el.find('.img').find('img').attr('src', $(this).data('img'));
    });
  });
})(jQuery);