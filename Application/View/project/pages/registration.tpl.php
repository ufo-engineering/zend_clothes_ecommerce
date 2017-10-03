<section class="content cart form registration">	
	<div class="block simplePage">
		<div class="head"><h1><?= get($this->page, 'titleRu') ?></h1></div>
		
		<div class="tabs" id="registrationForm">
			<form class="form-box" ui="Form" uiControl="project.registrationControl" id="formRegistration">
				
				<div id="basketError" uiForm="message" class="message"></div>
				
				<div class="item-group">
					<div class="item" uiField="name">
						<label>Имя<span>*</span></label>
						<input type="text" name="name" placeholder="" value="">
					</div>
					<div class="item" uiField="lastname">
						<label>Фамилия<span>*</span></label>
						<input type="text" name="lastname" placeholder="" value="">
					</div>
					<div class="item phone-item" uiField="email">
						<label>E-mail<span>*</span></label>
						<input type="text" name="email" placeholder="" value="">
					</div>
					<div class="item phone-item" uiField="phone">
						<label>Телефон<span>*</span></label>
						<input type="text" name="phone" placeholder="" value="">
					</div>
					<div class="item phone-item" uiField="password">
						<label>Пароль<span>*</span></label>
						<input type="password" name="password" placeholder="" value="">
					</div>
					<div class="item phone-item" uiField="confirm">
						<label>Подтверждение пароля<span>*</span></label>
						<input type="password" name="confirm" placeholder="" value="">
					</div>
				</div>
				
				<input type="hidden" name="active" value="1">
				<button type="submit" class="button">РЕГИСТРАЦИЯ</button>
				
			</form>
		</div>
		
		<div id="registrationSuccess" class="hidden">
			Поздравляем, Вы успешно зарегистрированы на сайте.<br>
			Для перехода в личный кабинет воспользуйтесь ссылкой:<br>
			<a href="<?= $this->link('login') ?>">Войти в личный кабинет</a>
		</div>
	</div>
</section>