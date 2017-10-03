<script type="text/html" id="productViewPopup">

<var (groupedImages = Unit.group(hash.images, 'colorId'))>
<var (defaultGroup = Unit.keys(groupedImages)[0])>
<var (defaultImage = Unit.toArray(groupedImages[defaultGroup] || {})[0] || {})>

<form ui="Form" uiControl="project.basketControl" id="productPopupForm">
	<div class="popup" id="item-popup" ui="Preview">
		
		<for (var groupId in groupedImages)>
			<for (var imgId in groupedImages[groupId])>
				<var (imgData = groupedImages[groupId][imgId])>
				<div class="left hidden" id="image_{$imgId}:{$imgId}">
					<a href="/public/products/show/{$imgData.view}" uiPreview="/public/products/show/{$imgData.view}" class="zoom"><img src="/public/products/show/{$imgData.view}" alt="" width="430px" /></a>
				</div>
			<endfor>
		<endfor>
			
		<div class="right">
			<div class="head ru">{$hash.titleRu || ''}</div>
			<div class="head en">{$hash.titleEn || ''}</div>
	
			<div class="info">
				<div class="i1" ui="Menu" uiRequired="true" id="productColor">
					
					<input type="hidden" name="id" value="{$hash.id}" />
					<input type="hidden" name="colorId" uiMenu="value" value="0" />
					<input type="hidden" name="price" value="{$hash.priceFloat}" />
					
					<!--
					<input type="hidden" name="discount" value="{$hash.discount}" />
					<input type="hidden" name="url" value="{$hash.url}" />
					<input type="hidden" name="title" value="{$hash.title}" />
					<input type="hidden" name="article" value="{$hash.article}" />
					<input type="hidden" name="image" value="{$defaultImage.view || ''}" id="previewBasketImg" />
					-->
					
					<span class="i1title">Артикул:</span> {$hash.article || ''}
					<br>
					<span class="i1title">Цвет:</span>
					<for (var colorId in hash.colors)>
						<span uiMenuOption="{$colorId}" class="hiddenActive">{$hash.colors[colorId]}</span>
					<endfor>
				</div>
	
				<div class="price">
					{$hash.price || ''}
				</div>
			</div>
	
			<div class="row" ui="Menu" uiRequired="true" uiAction="Unit('productColor').selectMenu(this.selected)">
				<div class="head">
					Выберите цвет:
				</div>
	
				<for (var groupId in groupedImages)>
					<for (var imgId in groupedImages[groupId])>
						<var (imgData = groupedImages[groupId][imgId])>
						<img src="/public/products/mini/{$imgData.view}" alt="" width="55px" uiMenuOption="#image_group_prev_{$imgId}:{$groupId}" onclick="Unit('imagePreviews').selectMenu('{$imgId}');Unit.inner('previewBasketImg','{$imgData.view}');" />
						<% break; %>
					<endfor>
				<endfor>
			</div>
	
			<if (hash.availability > 0)>
				<div class="sizes">
					<div class="head">
						Выберите размер:
					</div>
					<div class="data">
						<% if ( ! Unit.toArray(hash.sizes).length) hash.sizes = {0:'UN'}; %>
						<for (var i in hash.sizes)>
							<div class="item" ui="Counter" uiAction="this.trigger('active', this.getValue() > 0);Unit.inner('productBasketMessage','');" id="productCounter_{$i}">
								<a href="#" onClick="Unit('productCounter_{$i}').setValue(true); return false;">{$hash.sizes[i]}</a>
			
								<div class="counter">
									<span class="up" uiCounter="add"></span>
									<input type="text" uiCounter="val" name="sizeId[{$i}]" value="0" maxlength="3">
									<span class="down" uiCounter="del"></span>
								</div>
							</div>
						<endfor>
					</div>
					<input type="submit" class="btn" value="" />
					<p class="message" uiForm="message" id="productBasketMessage"></p>
		
					<div class="clear"></div>
					<p class="know" onclick="Unit.trigger('productDetectSizes')">Определить размер</p>
					
					<div class="knowSizes hidden" id="productDetectSizes">
						<table border="1">
							<tr>
								<td>Международный</td>
								<td>Евро размер</td>
								<td>Российский</td>
								<td>Объем груди</td>
								<td>Объем бедер</td>
								<td>Объем талии</td>
							</tr>
							<tr>
								<td>S</td>
								<td>36</td>
								<td>42</td>
								<td>80-90</td>
								<td>80-90</td>
								<td>до 66</td>
							</tr>
							<tr>
								<td>M</td>
								<td>38</td>
								<td>44</td>
								<td>91-95</td>
								<td>91-96</td>
								<td>до 70</td>
							</tr>
							<tr>
								<td>L</td>
								<td>40</td>
								<td>46</td>
								<td>96-100</td>
								<td>97-104</td>
								<td>до 76</td>
							</tr>
							<tr>
								<td>XL</td>
								<td>—</td>
								<td>48</td>
								<td>102</td>
								<td>106</td>
								<td>78</td>
							</tr>
							<tr>
								<td>XXL</td>
								<td>—</td>
								<td>50</td>
								<td>104</td>
								<td>106-108</td>
								<td>82</td>
							</tr>
							<tr>
								<td>XXXL</td>
								<td>—</td>
								<td>52</td>
								<td>106</td>
								<td>109-112</td>
								<td>86</td>
							</tr>
						</table>
					</div>
					
				</div>
			<else>
				<p class="notAvailable">Снято с производства</p>
			<endif>
	
			<div class="row big" ui="Menu" uiRequired="true" id="imagePreviews">
				<div class="head">
					Другие фотографии:
				</div>
				
				<for (var groupId in groupedImages)>
					<div id="image_group_prev_{$Unit.keys(groupedImages[groupId])[0]}:{$groupId}" class="hidden">
					<for (var imgId in groupedImages[groupId])>
						<var (imgData = groupedImages[groupId][imgId])>
						<img src="/public/products/mini/{$imgData.view}" alt="" width="48px" uiMenuOption="#image_{$imgId}:{$imgId}" />
					<endfor>
					</div>
				<endfor>
			</div>
	
			<div class="links">
				<ul>
					<li>
						<a href="/shipping/">Быстрая доставка</a>
					</li>
					<li>
						<a href="/shipping/">Удобная оплата</a>
					</li>
					<li>
						<a href="/shipping/">Обмен и возврат товара</a>
					</li>
					<li class="more">
						<a href="/{$hash.url || ''}/">Подробнее о товаре</a>
					</li>
				</ul>
			</div>
		</div>
		<button class="mfp-close" type="button" title="Close (Esc)" onclick="Unit.remove('productPopup')"></button>
	</div>
</form>

</script>