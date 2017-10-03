<?php
$url_str = $_SERVER['REQUEST_URI'];
    if(strpos($url_str,'/filter') != false){
    $new_filter = explode('/filter/',$url_str);
}
$fOptions	= $this->getFilteringOptions();
//$filtered	= get($_GET, 'f', array());
$filtered = $this->parse_filter($new_filter[1],$fOptions);
?>

<div class="more-setting">
	
	<div class="head">Расширенный поиск:</div>
	<div id="seoHideFiltersContent">
	<script src="/public/project/js/filter.js"></script>	
	<script type="text/html" id="seoHideFilters">

		<?php foreach ($fOptions as $optionId => $key): ?>
			<?php $filterCat_en = get($key, 'title_en');
                  $filterCat = get($key, 'title');
            //if (get($key,'id') != 2 && get($key,'id') !=5 && get($key,'id') !=7 && get($key,'id') !=8 && get($key,'id') !=9 && get($key,'id') !=38 && get($key,'id') !=42) continue; //Unit('catalogForm').submit()?>
			<div class="item dropDownItem ">
				<div class="h inactive" ui="Trigger" uiTarget="catalogFilter_<?= $optionId ?>"><?= $filterCat ?><span class="key"></span></div>
				
				<div class="clear"></div>
				<div class="content hidden" id="catalogFilter_<?= $optionId ?>">
					<?php foreach (get($key, 'options', array()) as $values): ?>
					<?php $filterId = get($values, 'id'); ?>
                    <?php $filterTitleEn = get($values, 'title_en'); ?>
					<?php $filterKeyId = get($values, 'keyId'); ?>
					<div class="li">
						<input id="filter_<?= $filterId ?>" class="filter_param <?= isset($filtered[$filterKeyId]) && in_array($filterId, $filtered[$filterKeyId]) ? 'checked_param' : '' ?>" type="checkbox"  hidden <?= isset($filtered[$filterKeyId]) && in_array($filterId, $filtered[$filterKeyId]) ? 'checked' : '' ?> onchange="filter_set(<?= "'".$filterCat_en."','".$filterTitleEn."'"?>)" onclick="filter_apply()" />
						<label for="filter_<?= $filterId ?>"><?= get($values, 'title') ?><!--<span>(17)</span>--></label>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	
		<div class="item dropDownItem active">
			<div class="h" ui="Trigger" uiTarget="catalogFilter_price">Цена<span class="key"></span></div>
			<div class="clear"></div>
			<div class="content" id="catalogFilter_price" ui="Selecter" uiSelecterMin="0" uiSelecterMax="1000" uiAction="Unit('catalogForm').submit();">
				<div class="inputs">
					<input type="text" name="priceFrom" uiSelecter="minValue" id="priceFrom" value="<?= htmlspecialchars((float) get($_GET,'priceFrom',0)) ?>" onchange="Unit('catalogForm').submit();">
					<div>—</div>
					<input type="text" name="priceTo" uiSelecter="maxValue" id="priceTo" value="<?= htmlspecialchars((float) get($_GET,'priceTo',1000)) ?>" onchange="Unit('catalogForm').submit();">
				</div>
				
				<div class="selecter">
					<div class="selecterBar" uiSelecter="bar"><div uiSelecter="cur"></div></div>
					<div class="selecterMin" uiSelecter="min"></div>
					<div class="selecterMax" uiSelecter="max"></div>
					<div class="selecterSteps">
						<span>0</span>
						<span>200</span>
						<span>400</span>
						<span>600</span>
						<span>800</span>
						<span>1000</span>
					</div>
				</div>

			</div>
		</div>
		
	</script>
	
	<script>
	Unit.inner('seoHideFiltersContent', Unit('seoHideFilters').innerHTML);
	</script>
	
</div>