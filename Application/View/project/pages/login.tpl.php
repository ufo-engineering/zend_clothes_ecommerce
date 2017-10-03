<section class="content cart form registration">	
	<div class="block simplePage">
		<div class="head"><h1><?= get($this->page, 'titleRu') ?></h1></div>
		
		<div class="tabs" id="registrationForm">
			<form class="form-box" ui="Form" uiControl="project.login" id="formLogin">
				
				<div id="basketError" uiForm="message" class="message"></div>
				
				<div class="item-group">
					<div class="item phone-item" uiField="email">
						<label>E-mail<span>*</span></label>
						<input type="text" name="email" placeholder="" value="">
					</div>
					<div class="item phone-item" uiField="password">
						<label>Пароль<span>*</span></label>
						<input type="password" name="password" placeholder="" value="">
					</div>
				</div>
				
				<button type="submit" class="button">ВОЙТИ</button>
				
			</form>
		</div>
	</div>
</section>