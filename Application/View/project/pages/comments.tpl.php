<section class="content cart form">
	<div class="block simplePage" style="position:relative;">
		
		<div class="head"><h1><?= get($this->page, 'titleRu') ?></h1></div>
		
		<div id="commentPageButton">
			<input type="button" onclick="Unit.trigger('commentPageButton');Unit.trigger('commentPageForm');" value="Оставить отзыв" class="buttonThin" style="position:absolute;right:0px;">
		</div>
		
		<div id="commentPageAdded" class="hidden">
			Спасибо за отзыв!<br>
			Ваш отзыв будет опубликован после проверки менеджером магазина.
		</div>
				
		<div class="tabs hidden" id="commentPageForm">
			
			<form class="form-box" ui="Form" uiControl="project.commentControl" id="commentForm">
				
				<div id="commentError" uiForm="message" class="message"></div>
				
				<div class="item-group">
					<div class="item" uiField="name">
						<label>Имя<span>*</span></label>
						<input type="text" name="name" placeholder="" value="" id="commentName">
					</div>
					<div class="item" uiField="email">
						<label>E-mail<span>*</span></label>
						<input type="text" name="email" placeholder="" value="" id="commentEmail">
					</div>
					<div class="item" uiField="rating">
						<label>Оценка<span>*</span></label>
						<div class="ui_rating" ui="Rating" uiRatingCount="5">
							<input type="hidden" name="rating" value="" uiRating="value">
							<div uiRatingItem="1"></div>
							<div uiRatingItem="2"></div>
							<div uiRatingItem="3"></div>
							<div uiRatingItem="4"></div>
							<div uiRatingItem="5"></div>
						</div>
					</div>
				</div>
				
				<div class="item" uiField="message">
					<label><span class="comment">Отзыв:</span></label>
					<textarea name="message" id="commentMessage" value=""></textarea>
				</div>
				
				<input type="hidden" name="active" value="2">
				<button type="submit" class="button">ОТПРАВИТЬ</button>
				
			</form>
			
		</div>

		<div class="siteComments">
			<?php
			
			$limit = 100;
			$index = get($_GET, 'page', 0) * $limit;
			
			$modelComment	= $this->loadModel('comment');
			$dataComments	= $modelComment->findAll(array('where' => array('active' => 1), 'order' => 'added', 'drect' => 0, 'index' => $index, 'limit' => $limit));
			$listComments	= get($dataComments, 'records');
			
			if ( ! count($listComments)): ?>
				
				<p>Нет озывов.</p>
				
			<?php endif; foreach ($listComments as $iComment): ?>
			
			<div class="stItem">
				<div class="stHead"><?= date('d.m.Y', get($iComment, 'added')) . '. '. htmlspecialchars(get($iComment, 'name')) ?></div>
				<div class="stRate stRate<?= get($iComment, 'rating', '') ?>"></div>
				<div class="stBody"><?= htmlspecialchars(get($iComment, 'message')) ?></div>
				<?php if (strlen(trim(get($iComment, 'answer')))): ?>
					<div class="stAnsw"><b>Ответ менеджера:</b><br><?= htmlspecialchars(get($iComment, 'answer')) ?></div>
				<?php endif; ?>
			</div>
		
			<?php endforeach; ?>
		</div>
		
		<div class="bottom-line">
		
			<?php
			
			/*print('<pre>');
			print_r($dataComments);
			print('</pre>');*/
			
			$pIndex = get($dataComments, 'index');
			$pLimit = get($dataComments, 'limit');
			$pFound = get($dataComments, 'total');
			
			$pCur = ceil($pIndex / $pLimit);
			$pMin = $pCur - 1;
			$pMax = $pCur + 1;
			$pEnd = ceil($pFound / $pLimit);
			
			?>
			
			<?php if ($pFound > $pLimit): ?>
			<div class="pagination">
				<ul>
					
					<li><a href="/comments/?page=<?= ($pMin >= 0 ? $pMin : 0) ?>" class="prev"></a></li>
					<?php for ($i = 0; $i < $pEnd; $i++): ?>
						<?php if ($i == $pCur): ?>
						<li><span class="active"><?= $i + 1 ?></span></li>
						<?php else: ?>
						<li><a href="/comments/?page=<?= $i ?>" class="<?= $pCur == $i ? 'active' : '' ?>"><?= $i + 1 ?></a></li>
						<?php endif; ?>
					<?php endfor; ?>
					<li><a href="/comments/?page=<?= $pMax >= $pEnd ? $pEnd : $pMax ?>" class="next"></a></li>
				</ul>
			</div>
			<?php endif; ?>
			
		</div>
		
	</div>
</section>