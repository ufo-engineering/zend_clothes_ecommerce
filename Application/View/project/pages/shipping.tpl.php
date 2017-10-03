<?php

$ua = $this->getPageInfo('forukraine');
$ru = $this->getPageInfo('forrussia');
$ww = $this->getPageInfo('forworld');

$host = $this->getHost();

switch ($host)
{
	case 'project.ru':
		$tab = 'RU';
	break;
	case 'project.com.ua':
		$tab = 'UA';
	break;
	case 'project.co.uk':
		$tab = 'WW';
	break;
	default:
		$tab = 'UA';
	break;
}

?>

<section class="content">

	<div class="block">

		<aside class="rightSide" style="width: 100%">
        <div class="breadcrumbs">
					<ul itemprop="breadcrumb">
						<li><a href="/"><?= $this->translate('Главная страница', 'Home page') ?></a></li>
						<li>|</li>
						<li><a href="<?=$this->url?>"><?=get($this->page, 'titleRu')?></a></li>
					</ul>
				</div>
			<div class="pay-text">
				<table class="links" ui="Menu" uiSelected="<?= $tab ?>">
					<tr>
						<?php if($host == 'project.ru'): ?>
						<td uiMenuOption="#shipping:RU"><h2><?= get($ru, 'titleRu') ?></h2></td>
						<td uiMenuOption="#shipping:UA"><h2><?= get($ua, 'titleRu') ?></h2></td>
						<td uiMenuOption="#shipping:WW"><h2><?= get($ww, 'titleRu') ?></h2></td>
						<?php elseif ($host == 'project.com.ua'): ?>
						<td uiMenuOption="#shipping:UA"><h2><?= get($ua, 'titleRu') ?></h2></td>
						<td uiMenuOption="#shipping:RU"><h2><?= get($ru, 'titleRu') ?></h2></td>
						<td uiMenuOption="#shipping:WW"><h2><?= get($ww, 'titleRu') ?></h2></td>
						<?php else: ?>
						<td uiMenuOption="#shipping:WW"><h2><?= get($ww, 'titleRu') ?></h2></td>
						<td uiMenuOption="#shipping:UA"><h2><?= get($ua, 'titleRu') ?></h2></td>
						<td uiMenuOption="#shipping:RU"><h2><?= get($ru, 'titleRu') ?></h2></td>
						<?php endif; ?>
					</tr>
				</table>

				<div id="shipping:UA" class="hidden"><?= get($ua, 'contentRu') ?></div>
				<div id="shipping:RU" class="hidden"><?= get($ru, 'contentRu') ?></div>
				<div id="shipping:WW" class="hidden"><?= get($ww, 'contentRu') ?></div>
			</div>

		</aside>
	</div>
</section>