function btn_cart_click(){
    var sel = document.getElementById("country_select");
    var val = sel.options[sel.selectedIndex].value;
    if (val == 0) alert('Выберите страну');
}

function user_set(type){
	switch (type){
		case 'exist':
			toShow= 'formPaymentUExist';
			toHide= 'formPayment';
		break;
		case 'new':
			toShow= 'formPayment';
			toHide= 'formPaymentUExist';
		break;
		case 'fast':
			toShow= 'formPaymentFast';
		break;
	}
	document.getElementById("formToSubmit").value= toShow;
	if (type != 'fast'){
		document.getElementById(toShow).style.display="block";
		document.getElementById(toHide).style.display="none";
	}
}

function show_comment(){
    document.getElementById("comment").style.display="block";
}

function set_mask(){
   var country = projectBasket.get('country')
   switch(country){
    case '1':
        code = '+38';
    break;
    case '2':
        code = '+7';
    break;
    case '3':
        code = '+375';
    break;
    case '4':
        code = '+7';
    break;
    case '5':
        code = '+44';
    break;
    case '99':
        code = '+99';
    break;
   }
    $("#phone").mask(code+" (999) 999-9999");
} 
function validate_fast(){
    if($('#email').val() != '') {
    var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
        if(pattern.test($('#email').val())){
            $('#email').css({'border' : '1px solid #569b44'});
            if($('#phone').val() != ''){
                $('#email').css({'border' : '1px solid #569b44'});
                move_down();
            }else{
                $('#phone').css({'border' : '1px solid #ff0000'});
            }
        } else {
            $('#email').css({'border' : '1px solid #ff0000'});
        }
    } else {
        $('#email').css({'border' : '1px solid #ff0000'});
    }  
}
function move_down(){
	$('#payment_types').css('display','block');
	var target_top = $('#payment').offset().top;
	$('html, body').animate({
		scrollTop: target_top - 100
	}, 'slow');
}
function getForm(){
	return document.getElementById("formToSubmit").value;
}

function getPaymentId(){
	var form= getForm();
	switch (form){
		case 'formPayment':
			var res= 'paymentType';
		break;
		case 'formPaymentUExist':
			var res= 'paymentTypeUexist';
		break;
		case 'formPaymentFast':
			var res= 'paymentTypeFast';
		break;
	}
	return res;
}

function validate_f(){
    if($('#email_f').val() != '') {
        if($('#password_f').val() != ''){
            $('#email_f').css({'border' : '1px solid #569b44'});
			$('#password_f').css({'border' : '1px solid #569b44'});
            move_down();
        } else {
            $('#password_f').css({'border' : '1px solid #ff0000'});
        }
    } else {
        $('#email_f').css({'border' : '1px solid #ff0000'});
    }  
}

function validate_full(){
    if($('#name_full').val() != '') {
		$('#name_full').css({'border' : '1px solid #569b44'});
        if($('#lastname_full').val() != '') {
            $('#lastname_full').css({'border' : '1px solid #569b44'});
            if($('#email_full').val() != ''){
                $('#email_full').css({'border' : '1px solid #569b44'});
				if($('#phone_full').val() != ''){
					$('#phone_full').css({'border' : '1px solid #569b44'});
					move_down();
				}else{
					$('#phone_full').css({'border' : '1px solid #ff0000'});
				}
            }else{
                $('#email_full').css({'border' : '1px solid #ff0000'});
            }
        } else {
            $('#lastname_full').css({'border' : '1px solid #ff0000'});
        }
    } else {
        $('#name_full').css({'border' : '1px solid #ff0000'});
    }  
}